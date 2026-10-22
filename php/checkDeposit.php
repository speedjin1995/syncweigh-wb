<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/deposit.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("status" => "failed", "message" => "Session expired, please login again"));
    exit;
}

// Returns the party balance and the unit price for the weighing screen
$transactionStatus = trim($_POST['transactionStatus'] ?? '');
$code = trim($_POST['code'] ?? '');
$itemCode = trim($_POST['itemCode'] ?? '');

$party = $code !== '' ? depositParty($db, $transactionStatus, $code) : null;

if (!$party) {
    echo json_encode(array("status" => "success", "enabled" => false, "balance" => 0, "price" => 0));
    $db->close();
    exit;
}

$price = $itemCode !== '' ? depositPrice($db, $transactionStatus, $party['id'], $itemCode) : 0;

echo json_encode(array(
    "status" => "success",
    "enabled" => depositControlEnabled($db),
    "balance" => $party['balance'],
    "price" => $price
));

$db->close();
?>
