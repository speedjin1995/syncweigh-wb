<?php
// Customer/Supplier deposit balance helpers, used by weight.php, deleteWeight.php and the Customer/Supplier services.
// Callers open the transaction. depositApply() relies on it for the row lock.

require_once __DIR__ . '/../classes/BaseController.php';

// Sales -> Customer, Purchase -> Supplier. Returns null for other statuses or an unknown or inactive party.
function depositParty($db, $transactionStatus, $code) {
    if ($transactionStatus === 'Sales') {
        $party = ['type' => 'CUSTOMER', 'table' => 'Customer', 'column' => 'customer_code'];
    } elseif ($transactionStatus === 'Purchase') {
        $party = ['type' => 'SUPPLIER', 'table' => 'Supplier', 'column' => 'supplier_code'];
    } else {
        return null;
    }

    $stmt = $db->prepare("SELECT id, deposit_balance FROM {$party['table']} WHERE {$party['column']} = ? AND status = '0'");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('s', $code);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) return null;

    $party['id'] = $row['id'];
    $party['code'] = $code;
    $party['balance'] = (float) $row['deposit_balance'];
    return $party;
}

// Unit price for the item: a FLOAT row uses its own price, anything else uses the item's standard price.
// Returns 0 when the item is not found.
function depositPrice($db, $transactionStatus, $partyId, $itemCode) {
    if ($transactionStatus === 'Sales') {
        $itemSql = "SELECT id, standard_price FROM Product WHERE product_code = ? AND status = '0'";
        $priceSql = "SELECT pricing_type, price FROM Product_Customer_Price WHERE product_id = ? AND customer_id = ? AND status = '0' ORDER BY id DESC LIMIT 1";
    } elseif ($transactionStatus === 'Purchase') {
        $itemSql = "SELECT id, standard_price FROM Raw_Mat WHERE raw_mat_code = ? AND status = '0'";
        $priceSql = "SELECT pricing_type, price FROM Raw_Mat_Supplier_Price WHERE raw_mat_id = ? AND supplier_id = ? AND status = '0' ORDER BY id DESC LIMIT 1";
    } else {
        return 0.0;
    }

    $stmt = $db->prepare($itemSql);
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('s', $itemCode);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$item) return 0.0;

    $standardPrice = (float) $item['standard_price'];

    $stmt = $db->prepare($priceSql);
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('ss', $item['id'], $partyId);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && $row['pricing_type'] === 'FLOAT') {
        return (float) $row['price'];
    }
    return $standardPrice;
}

// Applies a deposit movement. A positive $delta charges (balance goes down) and a negative one refunds.
// Returns false, and writes nothing, when a charge is larger than the balance.
function depositApply($db, $party, $delta, $weightId, $txnType, $remark, $username) {
    $delta = round((float) $delta, 2);

    $stmt = $db->prepare("SELECT deposit_balance FROM {$party['table']} WHERE id = ? FOR UPDATE");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('s', $party['id']);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) return false;

    $before = (float) $row['deposit_balance'];
    if ($delta > 0 && $before < $delta) return false;

    $after = round($before - $delta, 2);

    $stmt = $db->prepare("UPDATE {$party['table']} SET deposit_balance = ?, modified_by = ? WHERE id = ?");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('dss', $after, $username, $party['id']);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $stmt->close();

    depositLog($db, $party, $weightId, $txnType, -$delta, $before, $after, $remark, $username);
    return true;
}

// Writes one Deposit_Transaction row. $amount is signed from the deposit's point of view (top-up +, charge -).
function depositLog($db, $party, $weightId, $txnType, $amount, $before, $after, $remark, $username) {
    $stmt = $db->prepare("INSERT INTO Deposit_Transaction (party_type, party_id, party_code, weight_id, txn_type, amount, balance_before, balance_after, remark, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('sisisdddss', $party['type'], $party['id'], $party['code'], $weightId, $txnType, $amount, $before, $after, $remark, $username);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $stmt->close();
}

// Amount already charged for a saved weighing (0 for a new one or one never charged)
function depositChargedAmount($db, $weightId) {
    $stmt = $db->prepare("SELECT deposit_amount FROM Weight WHERE id = ?");
    if (!$stmt) throw new Exception($db->error);
    $stmt->bind_param('s', $weightId);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? (float) $row['deposit_amount'] : 0.0;
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

// Refunds the deposit charged by cancelled weighings, then clears the charge so it cannot be refunded twice.
// Accepts an array or a comma-separated list of ids. Weighings whose party is no longer active are left as they are.
function depositRefundWeights($db, $weightIds, $username) {
    $ids = is_array($weightIds) ? $weightIds : explode(',', $weightIds);

    foreach ($ids as $weightId) {
        $weightId = (int) $weightId;
        if ($weightId <= 0) continue;

        $stmt = $db->prepare("SELECT transaction_status, customer_code, supplier_code, deposit_amount FROM Weight WHERE id = ?");
        if (!$stmt) throw new Exception($db->error);
        $stmt->bind_param('i', $weightId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row || (float) $row['deposit_amount'] <= 0) continue;

        $code = $row['transaction_status'] === 'Sales' ? $row['customer_code'] : $row['supplier_code'];
        $party = depositParty($db, $row['transaction_status'], (string) $code);
        if (!$party) continue;

        $db->begin_transaction();
        depositApply($db, $party, -(float) $row['deposit_amount'], $weightId, 'REFUND', 'Cancelled weighing ' . $weightId, $username);

        $stmt = $db->prepare("UPDATE Weight SET deposit_amount = NULL WHERE id = ?");
        if (!$stmt) throw new Exception($db->error);
        $stmt->bind_param('i', $weightId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        $db->commit();
    }
}

// Feature flag gate. The deposit check only runs when the company has deposit_control switched on.
function depositControlEnabled($db) {
    $baseController = new BaseController($db);
    return $baseController->isFeatureEnabled('deposit_control');
}

// Price = subtotal + SST. Weights are kg; the price is per tonne.
function depositAmounts($finalWeightKg, $unitPrice, $sstRate) {
    $subTotal = round(((float) $finalWeightKg) / 1000 * (float) $unitPrice, 2);
    $sst = round($subTotal * (float) $sstRate / 100, 2);
    return ['sub_total' => $subTotal, 'sst' => $sst, 'total_price' => round($subTotal + $sst, 2)];
}
?>
