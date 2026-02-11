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
function calculateFFBPurchase($weighingRecords){
    $dataReturn = [];
    $totalPurchaseWeight = 0.00;

    if(!isset($weighingRecords['Purchase'])){
        return $totalPurchaseWeight;
    }

    if (empty($weighingRecords['Purchase'])){
        return $totalPurchaseWeight;
    }

    foreach($weighingRecords['Purchase'] as $purchaseRecord){
        
        $totalPurchaseWeight += (float) $purchaseRecord['final_weight'];
    }

    $dataReturn['totalPurchaseWeight'] = $totalPurchaseWeight;

    return $dataReturn;
}

// Calculate Sales Weight
function calculateFFBSales($weighingRecords){
    $dataReturn = [];
    $totalSalesWeight = 0.00;

    if(!isset($weighingRecords['Sales'])){
        return $totalSalesWeight;
    }

    if (empty($weighingRecords['Sales'])){
        return $totalSalesWeight;
    }

    foreach($weighingRecords['Sales'] as $salesRecord){
        $totalSalesWeight += (float) $salesRecord['final_weight'];
    }

    $dataReturn['totalSalesWeight'] = $totalSalesWeight;
}