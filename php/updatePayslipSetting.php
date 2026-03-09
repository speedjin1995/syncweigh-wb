<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

$nonResidentPcbRate = null;
$individualReliefFund = null;
$individualEpfReliefFund = null;

if($_POST['nonResidentPcbRate'] != null && $_POST['nonResidentPcbRate'] != ""){
	$nonResidentPcbRate = filter_input(INPUT_POST, 'nonResidentPcbRate', FILTER_SANITIZE_STRING);
}

if($_POST['individualReliefFund'] != null && $_POST['individualReliefFund'] != ""){
	$individualReliefFund = filter_input(INPUT_POST, 'individualReliefFund', FILTER_SANITIZE_STRING);
}

if($_POST['individualEpfReliefFund'] != null && $_POST['individualEpfReliefFund'] != ""){
	$individualEpfReliefFund = filter_input(INPUT_POST, 'individualEpfReliefFund', FILTER_SANITIZE_STRING);
}

// Socso Details Build
$socsoNo = isset($_POST['socsoNo']) ? $_POST['socsoNo']: [];
$socsoMin = isset($_POST['socsoMin']) ? $_POST['socsoMin']: [];
$socsoMax = isset($_POST['socsoMax']) ? $_POST['socsoMax']: [];
$socsoEmployer = isset($_POST['socsoEmployer']) ? $_POST['socsoEmployer']: [];
$socsoEmployee = isset($_POST['socsoEmployee']) ? $_POST['socsoEmployee']: [];

$socsoDetails = [];
if(isset($socsoNo) && $socsoNo != null && count($socsoNo) > 0){ 
	foreach ($socsoNo as $key => $value) {
		if (!empty($value)) {
			$socsoDetails[] = [
				"no" => $value,
				"min" => $socsoMin[$key],
				"max" => $socsoMax[$key],
				"employer" => $socsoEmployer[$key],
				"employee" => $socsoEmployee[$key]
			];
		}
	}
}

$socsoJson = !empty($socsoDetails) ? json_encode($socsoDetails) : null;
// End of Socso Details Build

// EIS Details Build
$eisNo = isset($_POST['eisNo']) ? $_POST['eisNo']: [];
$eisMin = isset($_POST['eisMin']) ? $_POST['eisMin']: [];
$eisMax = isset($_POST['eisMax']) ? $_POST['eisMax']: [];
$eisEmployer = isset($_POST['eisEmployer']) ? $_POST['eisEmployer']: [];
$eisEmployee = isset($_POST['eisEmployee']) ? $_POST['eisEmployee']: [];

$eisDetails = [];
if(isset($eisNo) && $eisNo != null && count($eisNo) > 0){ 
	foreach ($eisNo as $key => $value) {
		if (!empty($value)) {
			$eisDetails[] = [
				"no" => $value,
				"min" => $eisMin[$key],
				"max" => $eisMax[$key],
				"employer" => $eisEmployer[$key],
				"employee" => $eisEmployee[$key]
			];
		}
	}
}

$eisJson = !empty($eisDetails) ? json_encode($eisDetails) : null;
// End of EIS Details Build

// PCB Details Build
$pcbNo = isset($_POST['pcbNo']) ? $_POST['pcbNo']: [];
$pcbMin = isset($_POST['pcbMin']) ? $_POST['pcbMin']: [];
$pcbMax = isset($_POST['pcbMax']) ? $_POST['pcbMax']: [];
$pcbM = isset($_POST['pcbM']) ? $_POST['pcbM']: [];
$pcbR = isset($_POST['pcbR']) ? $_POST['pcbR']: [];
$pcbB1 = isset($_POST['pcbB1']) ? $_POST['pcbB1']: [];
$pcbB2 = isset($_POST['pcbB2']) ? $_POST['pcbB2']: [];

$pcbDetails = [];
if(isset($pcbNo) && $pcbNo != null && count($pcbNo) > 0){ 
	foreach ($pcbNo as $key => $value) {
		if (!empty($value)) {
			$pcbDetails[] = [
				"no" => $value,
				"min" => $pcbMin[$key],
				"max" => $pcbMax[$key],
				"m" => $pcbM[$key],
				"r" => $pcbR[$key],
				"b1" => $pcbB1[$key],
				"b2" => $pcbB2[$key]
			];
		}
	}
}

$pcbJson = !empty($pcbDetails) ? json_encode($pcbDetails) : null;
// End of PCB Details Build

$updates = [
    'socso' => $socsoJson,
    'eis' => $eisJson,
    'tax' => $pcbJson,
    'non_res_epf' => $nonResidentPcbRate,
    'ind_relief' => $individualReliefFund,
    'ind_epf_relief' => $individualEpfReliefFund,
];

if ($stmt = $db->prepare("UPDATE miscellaneous SET value = ? WHERE name = ?")) {
	$success = true;
	foreach ($updates as $name => $value) {
		$stmt->bind_param('ss', $value, $name);
		if (!$stmt->execute()) {
			$success = false;
			break;
		}
	}
	$stmt->close();
	$db->close();

	if ($success) {
		echo '<script type="text/javascript">alert("Payslip settings updated successfully!");</script>';
	} else {
		echo '<script type="text/javascript">alert("Failed to update settings!");</script>';
	}
} else {
	echo '<script type="text/javascript">alert("Something went wrong!");</script>';
}
header("location: ../payslipSetting.php");


?>
