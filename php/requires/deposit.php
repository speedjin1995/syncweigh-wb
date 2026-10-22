<?php
// Deposit helpers for weight.php (save). The logic lives in services/DepositService.php;
// these wrappers keep the legacy calls working until the weighing save moves to the service (Step 3).
// Callers open the transaction. depositApply() relies on it for the row lock.

require_once __DIR__ . '/../services/DepositService.php';

// Sales -> Customer, Purchase -> Supplier. Returns null for other statuses or an unknown or inactive party.
function depositParty($db, $transactionStatus, $code) {
    $service = new DepositService($db, '');
    $partyType = $service->partyTypeForStatus($transactionStatus);

    return $partyType ? $service->getPartyByCode($partyType, (string) $code) : null;
}

// Unit price for the item: a FLOAT row uses its own price, anything else uses the item's standard price.
function depositPrice($db, $transactionStatus, $partyId, $itemCode) {
    $service = new DepositService($db, '');
    $partyType = $service->partyTypeForStatus($transactionStatus);

    return $partyType ? $service->price($partyType, $partyId, $itemCode) : 0.0;
}

function depositApply($db, $party, $delta, $weightId, $txnType, $remark, $username) {
    return (new DepositService($db, $username))->apply($party, $delta, $weightId, $txnType, $remark);
}

function depositLog($db, $party, $weightId, $txnType, $amount, $before, $after, $remark, $username) {
    (new DepositService($db, $username))->log($party, $weightId, $txnType, $amount, $before, $after, $remark);
}

function depositChargedAmount($db, $weightId) {
    return (new DepositService($db, ''))->chargedAmount($weightId);
}

// Last step of a Sales/Purchase save, inside the save transaction opened by weight.php:
// stores the SST rate and deposit amount on the weighing, charges or refunds the difference, then commits.
// On an insufficient balance it rolls back and ends the request.
function depositSettle($db, $ctx, $weightId) {
    if ($ctx === null) return;

    $stmt = $db->prepare("UPDATE Weight SET sst_rate = ?, deposit_amount = ? WHERE id = ?");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('dds', $ctx['sstRate'], $ctx['depositAmount'], $weightId);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $stmt->close();

    if ($ctx['party'] !== null && $ctx['delta'] != 0) {
        $txnType = $ctx['delta'] > 0 ? 'DEDUCT' : 'REFUND';
        if (!depositApply($db, $ctx['party'], $ctx['delta'], $weightId, $txnType, $ctx['remark'], $ctx['username'])) {
            $db->rollback();
            echo json_encode(array("status" => "failed", "message" => "Insufficient Deposit Please Contact Office Admin"));
            exit();
        }
    }

    $db->commit();
}

function depositControlEnabled($db) {
    return (new DepositService($db, ''))->controlEnabled();
}

function depositAmounts($finalWeightKg, $unitPrice, $sstRate) {
    return (new DepositService(null, ''))->amounts($finalWeightKg, $unitPrice, $sstRate);
}
?>
