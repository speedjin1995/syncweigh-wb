<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

if(isset($_POST['id'], $_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type']; // "Product" or "RawMaterial"

    if ($type == "Product"){
        $table = "Product";
        $message = "Default product updated";
    }else{
        $table = "Raw_Mat";
        $message = "Default raw mat updated";
    }

    // Unset all default products
    if ($unset_stmt = $db->prepare("UPDATE $table SET is_default = 0 WHERE is_default = 1")) {

        // Execute the prepared statement
        if(!$unset_stmt->execute()){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $unset_stmt->error
                )
            );
        } else {
            // Then set the selected product as default
            if ($set_stmt = $db->prepare("UPDATE $table SET is_default = 1 WHERE id = ?")) {
                $set_stmt->bind_param('s', $id);

                // Execute the prepared query.
                if (! $set_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $set_stmt->error
                        )
                    );
                }
                else{
                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> $message
                        )
                    );
                }
                $set_stmt->close();
            }
        }
        $unset_stmt->close();
    } 
    $db->close();
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "ID is required"
        )
    );
}
?>
