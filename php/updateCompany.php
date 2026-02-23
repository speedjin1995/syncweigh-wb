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

	if ($stmt2 = $db->prepare("UPDATE Company SET company_reg_no=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?,  package=?, fax_no=?, name=?, mpob_no=?, mpob_expiry_date=?, mspo_no=?, mspo_expiry_date=?, include_price=?, include_container=?, include_display_setup=?, include_grading=?, modified_date=?, modified_by=? WHERE id=?")) {
		$stmt2->bind_param('sssssssssssssssssss', $companyRegNo, $companyAddress, $companyAddress2, $companyAddress3, $companyPhone, $companyPackage, $companyFax, $companyName, $mpobNo, $mpobExpiryDate, $mspoNo, $mspoExpiryDate, $_POST['includePrice'], $_POST['includeContainer'], $_POST['includeDisplaySetup'], $_POST['includeGrading'], $today, $username, $id);
		
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
