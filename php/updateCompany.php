<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

if(isset($_POST['companyRegNo'], $_POST['companyName'], $_POST['companyAddress'], $_POST['companyPhone'], $_POST['includePrice'], $_POST['includeContainer'], $_POST['includeDisplaySetup'], $_POST['includeGrading'])) {
	$companyRegNo = filter_input(INPUT_POST, 'companyRegNo', FILTER_SANITIZE_STRING);
	$companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_STRING);
	$companyAddress = filter_input(INPUT_POST, 'companyAddress', FILTER_SANITIZE_STRING);
	$companyPhone = filter_input(INPUT_POST, 'companyPhone', FILTER_SANITIZE_STRING);
	$companyAddress2 = null;
	$companyAddress3 = null;
	$companyFax = null;
	$companyPackage = null;
	$mpobNo = null;
	$mpobExpiryDate = null;
	$mspoNo = null;
	$mspoExpiryDate = null;
	$epf = null;
	$nonResidentPcbRate = null;
	$today = date("Y-m-d H:i:s");
	$id = '1';
	$action = '2';

	if($_POST['companyAddress2'] != null && $_POST['companyAddress2'] != ""){
		$companyAddress2 = filter_input(INPUT_POST, 'companyAddress2', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['companyAddress3'] != null && $_POST['companyAddress3'] != ""){
		$companyAddress3 = filter_input(INPUT_POST, 'companyAddress3', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyFax'] != null && $_POST['companyFax'] != ""){
		$companyFax = filter_input(INPUT_POST, 'companyFax', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyPackage'] != null && $_POST['companyPackage'] != ""){
		$companyPackage = filter_input(INPUT_POST, 'companyPackage', FILTER_SANITIZE_STRING);
	}

	if($_POST['mpobNo'] != null && $_POST['mpobNo'] != ""){
		$mpobNo = filter_input(INPUT_POST, 'mpobNo', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['mpobExpiry'] != null && $_POST['mpobExpiry'] != ""){
		$mpobExpiryDate = DateTime::createFromFormat('d-m-Y', $_POST["mpobExpiry"])->format('Y-m-d H:i:s');
	}

	if($_POST['mspoNo'] != null && $_POST['mspoNo'] != ""){
		$mspoNo = filter_input(INPUT_POST, 'mspoNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['mspoExpiry'] != null && $_POST['mspoExpiry'] != ""){
		$mspoExpiryDate = DateTime::createFromFormat('d-m-Y', $_POST["mspoExpiry"])->format('Y-m-d H:i:s');
	}

	if($_POST['employeeEpf'] != null && $_POST['employeeEpf'] != ""){
		$employeeEpf = filter_input(INPUT_POST, 'employeeEpf', FILTER_SANITIZE_STRING);
	}

	if($_POST['nonResidentPcbRate'] != null && $_POST['nonResidentPcbRate'] != ""){
		$nonResidentPcbRate = filter_input(INPUT_POST, 'nonResidentPcbRate', FILTER_SANITIZE_STRING);
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

	// ["pcbNo"]=> array(2) { [0]=> string(1) "1" [1]=> string(1) "2" } ["pcbMin"]=> array(2) { [0]=> string(4) "5001" [1]=> string(5) "20001" } ["pcbMax"]=> array(2) { [0]=> string(5) "20000" [1]=> string(5) "35000" } ["pcbM"]=> array(2) { [0]=> string(4) "5000" [1]=> string(5) "20000" } ["pcbR"]=> array(2) { [0]=> string(1) "1" [1]=> string(1) "3" } ["pcbB1"]=> array(2) { [0]=> string(4) "-400" [1]=> string(4) "-250" } ["pcbB2"]=> array(2) { [0]=> string(4) "-800" [1]=> string(4) "-650" } }
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

	if ($stmt2 = $db->prepare("UPDATE Company SET company_reg_no=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, package=?, fax_no=?, name=?, mpob_no=?, mpob_expiry_date=?, mspo_no=?, mspo_expiry_date=?, include_price=?, include_container=?, include_display_setup=?, include_grading=?, non_resident_pcb_rate=?, epf=?, socso=?, eis=?, tax=?, modified_date=?, modified_by=? WHERE id=?")) {
		$stmt2->bind_param('ssssssssssssssssssssssss', $companyRegNo, $companyAddress, $companyAddress2, $companyAddress3, $companyPhone, $companyPackage, $companyFax, $companyName, $mpobNo, $mpobExpiryDate, $mspoNo, $mspoExpiryDate, $_POST['includePrice'], $_POST['includeContainer'], $_POST['includeDisplaySetup'], $_POST['includeGrading'], $nonResidentPcbRate, $employeeEpf, $socsoJson, $eisJson, $pcbJson, $today, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();

			echo '<script type="text/javascript">alert("Your company profile is updated successfully!");</script>'; 
			header("location: ../companyProfile.php");
		} 
		else{
			echo '<script type="text/javascript">alert("Failed due to '.$stmt2->error.'");</script>'; 
			header("location: ../companyProfile.php");
		}
	} 
	else{
		echo '<script type="text/javascript">alert("Something went wrong!");</script>'; 
		header("location: ../companyProfile.php");
	}
} 
else{
	echo '<script type="text/javascript">alert("Please fill in all fields!");</script>'; 
	header("location: ../companyProfile.php");
}
?>
