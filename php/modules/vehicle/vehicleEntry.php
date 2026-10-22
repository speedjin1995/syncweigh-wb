<?php
// Vehicle entry helpers for the legacy weight.php and vehicleEntryLimit.php. The logic lives in services/VehicleEntryService.php;
// these wrappers go when those legacy files are ported.

require_once __DIR__ . '/../../services/VehicleEntryService.php';

function getVehicleDateLimitRow($db, $vehicleId, $date){
    return (new VehicleEntryService($db, ''))->limitRow($vehicleId, $date);
}

function getVehicleEntryLimit($db, $vehicleId, $defaultMax, $date){
    return (new VehicleEntryService($db, ''))->entryLimit($vehicleId, $defaultMax, $date);
}

function getVehicleEntryCount($db, $vehicleId, $date){
    return (new VehicleEntryService($db, ''))->entryCount($vehicleId, $date);
}

function getVehicleEntryStatus($db, $vehNumber, $date){
    return (new VehicleEntryService($db, ''))->status($vehNumber, $date);
}

function logVehicleEntry($db, $fields){
    return (new VehicleEntryService($db, ''))->logEntry($fields);
}

function logVehicleEntryIn($db, $entryStatus, $weightTable, $weightId, $transactionId, $isOverride, $username){
    return (new VehicleEntryService($db, $username))->logEntryIn($entryStatus, $weightTable, $weightId, $transactionId, $isOverride, $username);
}

function getVehicleEntryInRow($db, $weightTable, $weightId){
    return (new VehicleEntryService($db, ''))->inRow($weightTable, $weightId);
}

function hasVehicleEntryAction($db, $weightTable, $weightId, $action){
    return (new VehicleEntryService($db, ''))->hasAction($weightTable, $weightId, $action);
}

function reverseVehicleEntry($db, $weightTable, $ids, $username, $reason){
    (new VehicleEntryService($db, $username))->reverse($weightTable, $ids, $username, $reason);
}

function getVehicleEntryLogs($db, $weightTable, $weightId){
    return (new VehicleEntryService($db, ''))->logs($weightTable, $weightId);
}

function verifyUserPassword($db, $userId, $password){
    return (new VehicleEntryService($db, ''))->verifyPassword($userId, $password);
}
