<?php
require_once __DIR__ . '/BaseService.php';

// Charge larger than the party's deposit balance (the save rolls back)
class DepositInsufficient extends Exception {}

/**
 * Customer / Supplier deposit balance. The party is passed in (type CUSTOMER or SUPPLIER plus its id or code),
 * so the same methods serve the weighing screen, the weighing save and the party screens.
 * Callers open the transaction. apply() relies on it for the row lock.
 */
class DepositService extends BaseService {

    private $parties = [
        'CUSTOMER' => ['type' => 'CUSTOMER', 'table' => 'Customer', 'column' => 'customer_code'],
        'SUPPLIER' => ['type' => 'SUPPLIER', 'table' => 'Supplier', 'column' => 'supplier_code'],
    ];

    /**
     * Sales -> CUSTOMER, Purchase -> SUPPLIER. Null for other statuses.
     */
    public function partyTypeForStatus($transactionStatus) {
        if ($transactionStatus === 'Sales') return 'CUSTOMER';
        if ($transactionStatus === 'Purchase') return 'SUPPLIER';
        return null;
    }

    /**
     * Active party by its id, or null when unknown or inactive.
     */
    public function getParty($partyType, $partyId) {
        return $this->findParty($partyType, 'id', $partyId);
    }

    /**
     * Active party by its code (customer_code / supplier_code), or null.
     */
    public function getPartyByCode($partyType, $code) {
        return $this->findParty($partyType, null, $code);
    }

    /**
     * Unit price for the item: a FLOAT row uses its own price, anything else uses the item's standard price.
     * Returns 0 when the item is not found.
     */
    public function price($partyType, $partyId, $itemCode) {
        if ($partyType === 'CUSTOMER') {
            $itemSql = "SELECT id, standard_price FROM Product WHERE product_code = ? AND status = '0'";
            $priceSql = "SELECT pricing_type, price FROM Product_Customer_Price WHERE product_id = ? AND customer_id = ? AND status = '0' ORDER BY id DESC LIMIT 1";
        } elseif ($partyType === 'SUPPLIER') {
            $itemSql = "SELECT id, standard_price FROM Raw_Mat WHERE raw_mat_code = ? AND status = '0'";
            $priceSql = "SELECT pricing_type, price FROM Raw_Mat_Supplier_Price WHERE raw_mat_id = ? AND supplier_id = ? AND status = '0' ORDER BY id DESC LIMIT 1";
        } else {
            return 0.0;
        }

        $item = $this->fetchOne($itemSql, 's', [$itemCode]);
        if (!$item) return 0.0;

        $standardPrice = (float) $item['standard_price'];

        $row = $this->fetchOne($priceSql, 'ss', [$item['id'], $partyId]);
        if ($row && $row['pricing_type'] === 'FLOAT') {
            return (float) $row['price'];
        }

        return $standardPrice;
    }

    /**
     * Applies a deposit movement. A positive $delta charges (balance goes down) and a negative one refunds.
     * Returns false, and writes nothing, when a charge is larger than the balance.
     */
    public function apply($party, $delta, $weightId, $txnType, $remark) {
        $delta = round((float) $delta, 2);

        $row = $this->fetchOne("SELECT deposit_balance FROM {$party['table']} WHERE id = ? FOR UPDATE", 's', [$party['id']]);
        if (!$row) return false;

        $before = (float) $row['deposit_balance'];
        if ($delta > 0 && $before < $delta) return false;

        $after = round($before - $delta, 2);

        $this->run("UPDATE {$party['table']} SET deposit_balance = ?, modified_by = ? WHERE id = ?", 'dss', [$after, $this->username, $party['id']]);

        $this->log($party, $weightId, $txnType, -$delta, $before, $after, $remark);
        return true;
    }

    /**
     * Writes one Deposit_Transaction row. $amount is signed from the deposit's point of view (top-up +, charge -).
     */
    public function log($party, $weightId, $txnType, $amount, $before, $after, $remark) {
        $this->run(
            "INSERT INTO Deposit_Transaction (party_type, party_id, party_code, weight_id, txn_type, amount, balance_before, balance_after, remark, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'sisisdddss',
            [$party['type'], $party['id'], $party['code'], $weightId, $txnType, $amount, $before, $after, $remark, $this->username]
        );
    }

    /**
     * Amount already charged for a saved weighing (0 for a new one or one never charged)
     */
    public function chargedAmount($weightId) {
        $row = $this->fetchOne("SELECT deposit_amount FROM Weight WHERE id = ?", 's', [$weightId]);
        return $row ? (float) $row['deposit_amount'] : 0.0;
    }

    /**
     * Refunds the deposit charged by cancelled weighings, then clears the charge so it cannot be refunded twice.
     * Accepts an array or a comma-separated list of ids. Weighings whose party is no longer active are left as they are.
     */
    public function refundWeights($weightIds) {
        $ids = is_array($weightIds) ? $weightIds : explode(',', (string) $weightIds);

        foreach ($ids as $weightId) {
            $weightId = (int) $weightId;
            if ($weightId <= 0) continue;

            $row = $this->fetchOne("SELECT transaction_status, customer_code, supplier_code, deposit_amount FROM Weight WHERE id = ?", 'i', [$weightId]);
            if (!$row || (float) $row['deposit_amount'] <= 0) continue;

            $code = $row['transaction_status'] === 'Sales' ? $row['customer_code'] : $row['supplier_code'];
            $partyType = $this->partyTypeForStatus($row['transaction_status']);
            $party = $partyType ? $this->getPartyByCode($partyType, (string) $code) : null;
            if (!$party) continue;

            $this->db->begin_transaction();
            $this->apply($party, -(float) $row['deposit_amount'], $weightId, 'REFUND', 'Cancelled weighing ' . $weightId);
            $this->run("UPDATE Weight SET deposit_amount = NULL WHERE id = ?", 'i', [$weightId]);
            $this->db->commit();
        }
    }

    /**
     * Last step of a Sales/Purchase save, inside the save transaction (opened by the caller):
     * stores the SST rate and deposit amount on the weighing, charges or refunds the difference, then commits.
     * On an insufficient balance it rolls back and throws DepositInsufficient.
     */
    public function settle($ctx, $weightId) {
        if ($ctx === null) return;

        $this->run("UPDATE Weight SET sst_rate = ?, deposit_amount = ? WHERE id = ?", 'dds', [$ctx['sstRate'], $ctx['depositAmount'], $weightId]);

        if ($ctx['party'] !== null && $ctx['delta'] != 0) {
            $txnType = $ctx['delta'] > 0 ? 'DEDUCT' : 'REFUND';
            if (!$this->apply($ctx['party'], $ctx['delta'], $weightId, $txnType, $ctx['remark'])) {
                $this->db->rollback();
                throw new DepositInsufficient('Insufficient Deposit Please Contact Office Admin');
            }
        }

        $this->db->commit();
    }

    /**
     * Feature flag gate. The deposit check only runs when the company has deposit_control switched on.
     */
    public function controlEnabled() {
        return $this->isFeatureEnabled('deposit_control');
    }

    /**
     * Price = subtotal + SST. Weights are kg; the price is per tonne.
     */
    public function amounts($finalWeightKg, $unitPrice, $sstRate) {
        $subTotal = round(((float) $finalWeightKg) / 1000 * (float) $unitPrice, 2);
        $sst = round($subTotal * (float) $sstRate / 100, 2);
        return ['sub_total' => $subTotal, 'sst' => $sst, 'total_price' => round($subTotal + $sst, 2)];
    }

    private function findParty($partyType, $key, $value) {
        if (!isset($this->parties[$partyType]) || $value === null || $value === '') {
            return null;
        }

        $party = $this->parties[$partyType];
        $where = $key === null ? $party['column'] : $key;

        $row = $this->fetchOne("SELECT id, {$party['column']} AS code, deposit_balance FROM {$party['table']} WHERE {$where} = ? AND status = '0'", 's', [$value]);
        if (!$row) return null;

        $party['id'] = $row['id'];
        $party['code'] = $row['code'];
        $party['balance'] = (float) $row['deposit_balance'];
        return $party;
    }

    private function prepare($sql) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log('DepositService - ' . $this->db->error);
            throw new Exception('Something went wrong');
        }

        return $stmt;
    }

    private function fetchOne($sql, $types, $params) {
        $stmt = $this->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('DepositService - execute failed');
            throw new Exception('Something went wrong');
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    private function run($sql, $types, $params) {
        $stmt = $this->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            error_log('DepositService - execute failed');
            throw new Exception('Something went wrong');
        }

        $stmt->close();
    }
}
