<?php

// Group Weighing Records by Transaction Status
function groupByTransactionStatus($weights) {
    $grouped = [];
    foreach ($weights as $weight) {
        $grouped[$weight['transaction_status']][] = $weight;
    }
    return $grouped;
}

// Calculate Purchase Weight
function calculateFFBPurchase($weighingRecords, $db){
    $dataReturn = [];
    $totalCashWeight = 0.00;
    $totalTermWeight = 0.00;
    $totalRejectedWeight = 0.00;
    $totalCashPrice = 0.00;

    if(!isset($weighingRecords['Purchase'])){
        $dataReturn['totalCashWeight'] = $totalCashWeight;
        $dataReturn['totalTermWeight'] = $totalTermWeight;
        $dataReturn['totalRejectedWeight'] = $totalRejectedWeight;
        $dataReturn['totalCashPrice'] = $totalCashPrice;
        return $dataReturn;
    }

    if (empty($weighingRecords['Purchase'])){
        $dataReturn['totalCashWeight'] = $totalCashWeight;
        $dataReturn['totalTermWeight'] = $totalTermWeight;
        $dataReturn['totalRejectedWeight'] = $totalRejectedWeight;
        $dataReturn['totalCashPrice'] = $totalCashPrice;
        return $dataReturn;
    }

    foreach($weighingRecords['Purchase'] as $purchaseRecord){
        $paymentTerm = searchSupplierTermByCode($purchaseRecord['supplier_code'], $db);
        if ($paymentTerm == 'Cash') {
            $totalCashWeight += (float) $purchaseRecord['nett_weight1'];
            $totalRejectedWeight += (float) $purchaseRecord['reduce_weight'];
            $totalCashPrice += (float) $purchaseRecord['total_price'];
        }else{
            $totalTermWeight += (float) $purchaseRecord['nett_weight1'];
            $totalRejectedWeight += (float) $purchaseRecord['reduce_weight'];
        }
    }

    $dataReturn['totalCashWeight'] = $totalCashWeight;
    $dataReturn['totalTermWeight'] = $totalTermWeight;
    $dataReturn['totalRejectedWeight'] = $totalRejectedWeight;
    $dataReturn['totalCashPrice'] = $totalCashPrice;
    return $dataReturn;
}

// Calculate Sales Weight
function calculateFFBSales($weighingRecords, $db){
    $dataReturn = [];
    $totalSalesWeight = 0.00;

    if(!isset($weighingRecords['Sales'])){
        $dataReturn['totalSalesWeight'] = $totalSalesWeight;
        return $dataReturn;
    }

    if (empty($weighingRecords['Sales'])){
        $dataReturn['totalSalesWeight'] = $totalSalesWeight;
        return $dataReturn;
    }

    foreach($weighingRecords['Sales'] as $salesRecord){
        $totalSalesWeight += (float) $salesRecord['nett_weight1'];
    }

    $dataReturn['totalSalesWeight'] = $totalSalesWeight;

    return $dataReturn;
}