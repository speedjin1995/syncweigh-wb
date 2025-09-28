<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['id'];
}

$stmt = $db->prepare("SELECT * FROM Users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if (($row = $result->fetch_assoc()) !== null) {
	$updates = [];
	$params = [];
	$types = "";

	// Password 1
    if (!empty($_POST['oldPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmPassword'])) {
		if ($_POST['newPassword'] !== $_POST['confirmPassword']) {
			echo '<script>alert("New password and confirm password do not match (Password 1)."); window.location.href="../ChangePassword.php";</script>';
            exit;
		}

		if (!password_verify($_POST['oldPassword'], $row['password'])) {
            echo '<script>alert("Old password is incorrect (Password 1)."); window.location.href="../ChangePassword.php";</script>';
            exit;
        }

		$updates[] = "password = ?";
        $params[] = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
        $types .= "s";
	}

	// Password 2
    if (!empty($_POST['oldPassword2']) && !empty($_POST['newPassword2']) && !empty($_POST['confirmPassword2'])) {
		if ($_POST['newPassword2'] !== $_POST['confirmPassword2']) {
			echo '<script>alert("New password and confirm password do not match (Password 2)."); window.location.href="../ChangePassword.php";</script>';
            exit;
		}

        if (!empty($row['password2']) && !password_verify($_POST['oldPassword2'], $row['password2'])) {
            echo '<script>alert("Old password is incorrect (Password 2)."); window.location.href="../ChangePassword.php";</script>';
            exit;
        }

        $updates[] = "password2 = ?";
        $params[] = password_hash($_POST['newPassword2'], PASSWORD_DEFAULT);
        $types .= "s";
	}

	// Password 3
	if (!empty($_POST['oldPassword3']) && !empty($_POST['newPassword3']) && !empty($_POST['confirmPassword3'])) {
		if ($_POST['newPassword3'] !== $_POST['confirmPassword3']) {
			echo '<script>alert("New password and confirm password do not match (Password 3)."); window.location.href="../ChangePassword.php";</script>';
			exit;
		}
		if (!empty($row['password3']) && !password_verify($_POST['oldPassword3'], $row['password3'])) {
			echo '<script>alert("Old password is incorrect (Password 3)."); window.location.href="../ChangePassword.php";</script>';
			exit;
		}
		$updates[] = "password3 = ?";
		$params[] = password_hash($_POST['newPassword3'], PASSWORD_DEFAULT);
		$types .= "s";
	}

	// Run update if there are changes
    if (!empty($updates)) {
		$sql = "UPDATE Users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt2 = $db->prepare($sql);
        $params[] = $id;
        $types .= "s";

        $stmt2->bind_param($types, ...$params);

        if ($stmt2->execute()) {
            echo '<script>alert("Password(s) updated successfully!"); window.location.href="../ChangePassword.php";</script>';
        } else {
            echo '<script>alert("Failed to update: ' . $stmt2->error . '"); window.location.href="../ChangePassword.php";</script>';
        }

        $stmt2->close();
	}else{
		echo '<script>alert("Please fill in at least one password set to update."); window.location.href="../ChangePassword.php";</script>';
	}
}else{
	echo '<script>alert("User not found."); window.location.href="../ChangePassword.php";</script>';
}

$db->close();
## Old Code ##
// if(isset($_POST['oldPassword'], $_POST['newPassword'], $_POST['confirmPassword'])){
// 	$oldPassword = filter_input(INPUT_POST, 'oldPassword', FILTER_SANITIZE_STRING);
// 	$newPassword = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
// 	$confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);
	
// 	$stmt = $db->prepare("SELECT * from Users where id = ?");
// 	$stmt->bind_param('s', $id);
// 	$stmt->execute();
// 	$result = $stmt->get_result();
	
// 	if(($row = $result->fetch_assoc()) !== null){
// 		if (password_verify($oldPassword, $row['password'])){
// 			$param_password = password_hash($newPassword, PASSWORD_DEFAULT); // Creates a password hash
// 			$stmt2 = $db->prepare("UPDATE Users SET password = ? WHERE id = ?");
// 			$stmt2->bind_param('ss', $param_password, $id);
			
// 			if($stmt2->execute()){
//     			$stmt2->close();
//     			$db->close();

// 				echo '<script type="text/javascript">alert("Update successfully!");window.location.href = "../ChangePassword.php";</script>'; 
// 				//header("location: ../ChangePassword.php");
//     		} else{
//     		    echo '<script type="text/javascript">alert("Failed to update due to "'.$stmt2->error.');window.location.href = "../ChangePassword.php";</script>'; 
// 				//header("location: ../ChangePassword.php");
//     		}
// 		} else{
// 		    echo '<script type="text/javascript">alert("Old password is not matched");window.location.href = "../ChangePassword.php";</script>'; 
// 			//header("location: ../ChangePassword.php");
// 		}
// 	} else{
// 	    echo '<script type="text/javascript">alert("Data retrieve failed");window.location.href = "../ChangePassword.php";</script>'; 
// 		//header("location: ../ChangePassword.php");
// 	}
// } else{
//     echo '<script type="text/javascript">alert("Please fill in all the fields");window.location.href = "../ChangePassword.php";</script>'; 
// 	//header("location: ../ChangePassword.php");
// }
?>
