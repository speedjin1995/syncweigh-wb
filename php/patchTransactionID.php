<?php
session_start();
require_once "db_connect.php";

$salesCount = 0;
$purchaseCount = 0;
$localCount = 0;
$miscCount = 0;

if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE transaction_date >= '2026-02-01 00:00:00' UNION ALL SELECT * FROM Weight_Container WHERE transaction_date >= '2026-02-01 00:00:00' ORDER BY transaction_date ASC;
")) {
    // Execute the prepared query.
    if (! $select_stmt->execute()) {
        echo "Error executing query: " . $select_stmt->error;
    }
    else{
        $result = $select_stmt->get_result();
        $message = array();
        
        while ($row = $result->fetch_assoc()) {
            $transaction_id = $row['transaction_id'];

            if ($row['transaction_status'] == 'Sales') {
                $salesCount++;
                $parts = explode('-', $transaction_id);
                $transactionId = $parts[0] . '-';

                $charSize = strlen($salesCount);
                for($i=0; $i<(4-(int)$charSize); $i++){
                    $transactionId.='0';  // S0000
                }
                
                $transactionId .= $salesCount;

                echo "Sales Count: " . $salesCount . '<br>';
                echo "Previous Transaction ID: " . $transaction_id . '<br>';
                echo "New Transaction ID: " . $transactionId . '<br><br>';

                if ($row['weight_type'] == 'Empty Container') {
                    $update_stmt = $db->prepare("UPDATE Weight_Container SET transaction_id = ? WHERE id = ?");
                } else {
                    $update_stmt = $db->prepare("UPDATE Weight SET transaction_id = ? WHERE id = ?");
                }
                
                $update_stmt->bind_param("ss", $transactionId, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            } elseif ($row['transaction_status'] == 'Purchase') {
                $purchaseCount++;
                $parts = explode('-', $transaction_id);
                $transactionId = $parts[0] . '-';

                $charSize = strlen($purchaseCount);
                for($i=0; $i<(4-(int)$charSize); $i++){
                    $transactionId.='0';  // S0000
                }
                
                $transactionId .= $purchaseCount;
                echo "Purchase Count: " . $purchaseCount . '<br>';
                echo "Previous Transaction ID: " . $transaction_id . '<br>';
                echo "New Transaction ID: " . $transactionId . '<br><br>';

                if ($row['weight_type'] == 'Empty Container') {
                    $update_stmt = $db->prepare("UPDATE Weight_Container SET transaction_id = ? WHERE id = ?");
                } else {
                    $update_stmt = $db->prepare("UPDATE Weight SET transaction_id = ? WHERE id = ?");
                }

                $update_stmt->bind_param("ss", $transactionId, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            } elseif ($row['transaction_status'] == 'Local') {
                $localCount++;
                $parts = explode('-', $transaction_id);
                $transactionId = $parts[0] . '-';

                $charSize = strlen($localCount);
                for($i=0; $i<(4-(int)$charSize); $i++){
                    $transactionId.='0';  // S0000
                }

                $transactionId .= $localCount;
                echo "Local Count: " . $localCount . '<br>';
                echo "Previous Transaction ID: " . $transaction_id . '<br>';
                echo "New Transaction ID: " . $transactionId . '<br><br>';

                if ($row['weight_type'] == 'Empty Container') {
                    $update_stmt = $db->prepare("UPDATE Weight_Container SET transaction_id = ? WHERE id = ?");
                } else {
                    $update_stmt = $db->prepare("UPDATE Weight SET transaction_id = ? WHERE id = ?");
                }     

                $update_stmt->bind_param("ss", $transactionId, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            } elseif ($row['transaction_status'] == 'Misc') {
                $miscCount++;
                $parts = explode('-', $transaction_id);
                $transactionId = $parts[0] . '-';

                $charSize = strlen($miscCount);
                for($i=0; $i<(4-(int)$charSize); $i++){
                    $transactionId.='0';  // S0000
                }

                $transactionId .= $miscCount;
                echo "Misc Count: " . $miscCount . '<br>';
                echo "Previous Transaction ID: " . $transaction_id . '<br>';
                echo "New Transaction ID: " . $transactionId . '<br><br>';

                if ($row['weight_type'] == 'Empty Container') {
                    $update_stmt = $db->prepare("UPDATE Weight_Container SET transaction_id = ? WHERE id = ?");
                } else {
                    $update_stmt = $db->prepare("UPDATE Weight SET transaction_id = ? WHERE id = ?");
                }         
                $update_stmt->bind_param("ss", $transactionId, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        
        $salesCount++;
        $purchaseCount++;
        $localCount++;
        $miscCount++;

        // Update plant details
        $update_stmt2 = $db->prepare("UPDATE Plant SET sales = ?, purchase = ?, locals = ?, misc = ? WHERE id = 26");
        $update_stmt2->bind_param("ssss", $salesCount, $purchaseCount, $localCount, $miscCount);
        $update_stmt2->execute();
        $update_stmt2->close();
    }

    $select_stmt->close();
}

// Check for duplicate transaction IDs after patching
if ($duplicate_stmt = $db->prepare("SELECT transaction_id, COUNT(*) as count FROM (SELECT transaction_id FROM Weight WHERE transaction_date >= '2026-02-01 00:00:00' UNION ALL SELECT transaction_id FROM Weight_Container WHERE transaction_date >= '2026-02-01 00:00:00') as combined GROUP BY transaction_id HAVING COUNT(*) > 1")) {
    if ($duplicate_stmt->execute()) {
        $duplicate_result = $duplicate_stmt->get_result();
        if ($duplicate_result->num_rows > 0) {
            echo "<h3>Duplicate Transaction IDs Found:</h3>";
            while ($duplicate_row = $duplicate_result->fetch_assoc()) {
                echo "Transaction ID: " . $duplicate_row['transaction_id'] . " (Count: " . $duplicate_row['count'] . ")<br>";
            }
        } else {
            echo "<h3>No duplicate transaction IDs found.</h3>";
        }
    }
    $duplicate_stmt->close();
}

$db->close();

?>