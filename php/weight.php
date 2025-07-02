<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

$today = date('ym');

// Processing form data when form is submitted
if (isset($_POST['transactionId'], $_POST['transactionStatus'], $_POST['weightType'], $_POST['transactionDate'], $_POST['grossIncoming'], $_POST['grossIncomingDate']
, $_POST['manualWeight'], $_POST['plantCode'], $_POST['plant'], $_POST['exDel'], $_POST['loadDrum'])) {
    $isCancel = 'N';
    $isComplete = 'N';
    $isApproved = 'Y';
    $misValue = '';

    if (empty($_POST["id"])) {
        $weightId = null;
    } else {
        $weightId = trim($_POST["id"]);
    }

    if (empty($_POST["plantCode"])) {
        $plantCode = null;
    } else {
        $plantCode = trim($_POST["plantCode"]);
    }

    if (empty($_POST["plant"])) {
        $plant = null;
    } else {
        $plant = trim($_POST["plant"]);
    }

    if (empty($_POST["exDel"])) {
        $exDel = null;
    } else {
        if ($_POST["exDel"] == 'true'){
            $exDel = 'EX';
        }else{
            $exDel = 'DEL';
        }
    }

    if (empty($_POST["loadDrum"])) {
        $loadDrum = null;
    } else {
        if ($_POST["loadDrum"] == 'true'){
            $loadDrum = 'LOAD';
        }else{
            $loadDrum = 'DRUM';
        }
    }

    if (empty($_POST["weightType"])) {
        $weightType = 'Normal';
    } else {
        $weightType = trim($_POST["weightType"]);
    }

    if (empty($_POST["transactionId"])) {
        $status = $_POST['transactionStatus'];

		if($update_stmt2 = $db->prepare("SELECT * FROM status WHERE status=?")){
			$update_stmt2->bind_param('s', $status);

			if (! $update_stmt2->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong when pulling status"
                    )
                ); 
            }
            else{
                $result2 = $update_stmt2->get_result();
				$id = '1';
				$transactionId = $plantCode.'/';

				if ($row2 = $result2->fetch_assoc()) {
					//$id = $row2['misc_id'];

                    if ($weightType == 'Container'){
                        $transactionId .= 'C/'.$row2['prefix'] . '/'.$today . '-';
                    }else{
                        $transactionId .= $row2['prefix'] . '/' .$today . '-';
                    }
				} 

                $queryPlant = "SELECT sales as curcount FROM Plant WHERE plant_code='$plantCode'";

                if($status == 'Purchase'){
                    $queryPlant = "SELECT purchase as curcount FROM Plant WHERE plant_code='$plantCode'";
                }
                else if($status == 'Local'){
                    $queryPlant = "SELECT locals as curcount FROM Plant WHERE plant_code='$plantCode'";
                }
                else if($status == 'Misc'){
                    $queryPlant = "SELECT misc as curcount FROM Plant WHERE plant_code='$plantCode'";
                }

				if ($update_stmt = $db->prepare($queryPlant)) {
					// Execute the prepared query.
					if (! $update_stmt->execute()) {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Something went wrong"
							)); 
					}
					else{
						$result = $update_stmt->get_result();
						$message = array();
						
						if ($row = $result->fetch_assoc()) {
							$charSize = strlen($row['curcount']);
							$misValue = $row['curcount'];
		
							for($i=0; $i<(4-(int)$charSize); $i++){
								$transactionId.='0';  // S0000
							}
					
							$transactionId .= $misValue;  //S00009
                        }
                    }
                }
            }
		}
    } 
    else {
        $transactionId = trim($_POST["transactionId"]);
    }

    if (empty($_POST["transactionStatus"])) {
        $transactionStatus = null;
    } else {
        $transactionStatus = trim($_POST["transactionStatus"]);
    }

    if (empty($_POST["unitPrice"])) {
        $unitPrice = null;
    } else {
        $unitPrice = trim($_POST["unitPrice"]);
    }

    if (empty($_POST["subTotalPrice"])) {
        $subTotalPrice = '0.00';
    } else {
        $subTotalPrice = trim($_POST["subTotalPrice"]);
    }

    if (empty($_POST["sstPrice"])) {
        $sstPrice = '0.00';
    } else {
        $sstPrice = trim($_POST["sstPrice"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = '0.00';
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["customerType"])) {
        $customerType = 'Normal';
    } else {
        $customerType = trim($_POST["customerType"]);
    }

    if (empty($_POST["transactionDate"])) {
        $transactionDate = null;
    } else {
        $transactionDate = DateTime::createFromFormat('d-m-Y', $_POST["transactionDate"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["poSupplyWeight"])) {
        $poSupplyWeight = null;
    } else {
        $poSupplyWeight = trim($_POST["poSupplyWeight"]);
    }

    if (empty($_POST["supplierWeight"])) {
        $supplierWeight = null;
    } else {
        $supplierWeight = trim($_POST["supplierWeight"]);
    }

    if (empty($_POST["orderWeight"])) {
        $orderWeight = null;
    } else {
        $orderWeight = trim($_POST["orderWeight"]);
    }

    if (empty($_POST["grossIncoming"])) {
        $grossIncoming = 0;
    } else {
        $grossIncoming = trim($_POST["grossIncoming"]);
    }

    if (empty($_POST["grossIncomingDate"])) {
        $grossIncomingDate = date('Y-m-d H:i:s');
    } 
    else {
        $grossIncomingDate = trim(str_replace(["AM", "PM"], "", $_POST["grossIncomingDate"]));
        $grossIncomingDate = DateTime::createFromFormat('d/m/Y H:i:s', $grossIncomingDate)->format('Y-m-d H:i:s');
    } 

    if (empty($_POST["grossWeightBy1"])) {
        $grossWeightBy1 = 0;
    } else {
        $grossWeightBy1 = trim($_POST["grossWeightBy1"]);
    }
    
    if (empty($_POST["tareOutgoing"])) {
        $tareOutgoing = 0;
    } else {
        $tareOutgoing = trim($_POST["tareOutgoing"]);
    }

    if (empty($_POST["tareOutgoingDate"])) {
        $tareOutgoingDate = null;
    } else {
        $tareOutgoingDate = trim(str_replace(["AM", "PM"], "", $_POST["tareOutgoingDate"]));
        $tareOutgoingDate = DateTime::createFromFormat('d/m/Y H:i:s', $tareOutgoingDate)->format('Y-m-d H:i:s');
    }

    if (empty($_POST["tareWeightBy1"])) {
        $tareWeightBy1 = 0;
    } else {
        $tareWeightBy1 = trim($_POST["tareWeightBy1"]);
    }

    if (empty($_POST["nettWeight"])) {
        $nettWeight = 0;
    } else {
        $nettWeight = trim($_POST["nettWeight"]);
    }

    if (empty($_POST["manualWeight"])) {
        $manualWeight = null;
    } else {
        $manualWeight = trim($_POST["manualWeight"]);
    }

    if (empty($_POST["weighbridge"])) {
        $weighbridge = 'Weigh1';
    } else {
        $weighbridge = trim($_POST["weighbridge"]);
    }

    if (empty($_POST["indicatorId"])) {
        $indicatorId = null;
    } else {
        $indicatorId = trim($_POST["indicatorId"]);
    }

    if (empty($_POST["invoiceNo"])) {
        $invoiceNo = null;
    } else {
        $invoiceNo = trim($_POST["invoiceNo"]);
    }

    /*if ($transactionStatus == 'Sales'){
        $deliveryNo = $transactionId;
    }else{*/
    if (empty($_POST["deliveryNo"])) {
        $deliveryNo = null;
    } else {
        $deliveryNo = trim($_POST["deliveryNo"]);
    }
    //}

    if (empty($_POST["purchaseOrder"])) {
        $purchaseOrder = null;
    } else {
        $purchaseOrder = trim($_POST["purchaseOrder"]);
    }

    if (empty($_POST["containerNo"])) {
        if ($weightType == 'Container'){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Container No cannot be empty"
                )
            );

            exit;
        }else{
            $containerNo = null;
        }
    } else {
        $containerNo = trim($_POST["containerNo"]);
    }

    if (empty($_POST["sealNo"])) {
        $sealNo = null;
    } else {
        $sealNo = trim($_POST["sealNo"]);
    }

    if (empty($_POST["containerNo2"])) {
        $containerNo2 = null;
    } else {
        $containerNo2 = trim($_POST["containerNo2"]);
    }

    if (empty($_POST["sealNo2"])) {
        $sealNo2 = null;
    } else {
        $sealNo2 = trim($_POST["sealNo2"]);
    }

    if (empty($_POST["customerName"])) {
        $customerName = null;
    } else {
        $customerName = trim($_POST["customerName"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }

    if (empty($_POST["rawMaterialName"])) {
        $rawMaterialName = null;
    } else {
        $rawMaterialName = trim($_POST["rawMaterialName"]);
    }

    if (empty($_POST["siteName"])) {
        $siteName = null;
    } else {
        $siteName = trim($_POST["siteName"]);
    }

    if (empty($_POST["transporter"])) {
        $transporter = null;
    } else {
        $transporter = trim($_POST["transporter"]);
    }

    if (empty($_POST["weightDifference"])) {
        $weightDifference = null;
    } else {
        $weightDifference = trim($_POST["weightDifference"]);
    }
    
    if (empty($_POST["destination"])) {
        $destination = null;
    } else {
        $destination = trim($_POST["destination"]);
    }

    if (empty($_POST["reduceWeight"])) {
        $reduceWeight = '0';
    } else {
        $reduceWeight = trim($_POST["reduceWeight"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = null;
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["otherRemarks"])) {
        $otherRemarks = null;
    } else {
        $otherRemarks = trim($_POST["otherRemarks"]);
    }

    // container
    if (empty($_POST["vehiclePlateNo2"])) {
        $vehiclePlateNo2 = null;
    } else {
        $vehiclePlateNo2 = trim($_POST["vehiclePlateNo2"]);
    }

    if(filter_has_var(INPUT_POST,'manualVehicle2')) {
        $vehiclePlateNo2 = trim($_POST["vehicleNoTxt2"]);
    }

    if (empty($_POST["grossIncoming2"])) {
        $grossIncoming2 = null;
    } else {
        $grossIncoming2 = trim($_POST["grossIncoming2"]);
    }

    if (empty($_POST["grossIncomingDate2"])) {
        $grossIncomingDate2 = null;
    } else {
        $grossIncomingDate2 = DateTime::createFromFormat('d/m/Y H:i:s A', $_POST["grossIncomingDate2"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["grossWeightBy2"])) {
        $grossWeightBy2 = 0;
    } else {
        $grossWeightBy2 = trim($_POST["grossWeightBy2"]);
    }

    if (empty($_POST["tareOutgoing2"])) {
        $tareOutgoing2 = null;
    } else {
        $tareOutgoing2 = trim($_POST["tareOutgoing2"]);
    }

    if (empty($_POST["tareOutgoingDate2"])) {
        $tareOutgoingDate2 = null;
    } else {
        $tareOutgoingDate2 = DateTime::createFromFormat('d/m/Y H:i:s A', $_POST["tareOutgoingDate2"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["tareWeightBy2"])) {
        $tareWeightBy2 = 0;
    } else {
        $tareWeightBy2 = trim($_POST["tareWeightBy2"]);
    }

    if (empty($_POST["nettWeight2"])) {
        $nettWeight2 = null;
    } else {
        $nettWeight2 = trim($_POST["nettWeight2"]);
    }

    if (empty($_POST["agent"])) {
        $agent = null;
    } else {
        $agent = trim($_POST["agent"]);
    }

    if (empty($_POST["agentCode"])) {
        $agentCode = null;
    } else {
        $agentCode = trim($_POST["agentCode"]);
    }
    
    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if (empty($_POST["supplierCode"])) {
        $supplierCode = null;
    } else {
        $supplierCode = trim($_POST["supplierCode"]);
    }

    if (empty($_POST["supplierName"])) {
        $supplierName = null;
    } else {
        $supplierName = trim($_POST["supplierName"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["rawMaterialCode"])) {
        $rawMaterialCode = null;
    } else {
        $rawMaterialCode = trim($_POST["rawMaterialCode"]);
    }

    if (empty($_POST["siteCode"])) {
        $siteCode = null;
    } else {
        $siteCode = trim($_POST["siteCode"]);
    }

    if (empty($_POST["destinationCode"])) {
        $destinationCode = null;
    } else {
        $destinationCode = trim($_POST["destinationCode"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["finalWeight"])) {
        $finalWeight = '0';
    } else {
        $finalWeight = trim($_POST["finalWeight"]);
    }

    if (empty($_POST["indicatorId2"])) {
        $indicatorId2 = null;
    } else {
        $indicatorId2 = trim($_POST["indicatorId2"]);
    }

    if (empty($_POST["productDescription"])) {
        $productDescription = null;
    } else {
        $productDescription = trim($_POST["productDescription"]);
    }

    if (empty($_POST["vehiclePlateNo1"])) {
        $vehiclePlateNo1 = null;
    } else {
        $vehiclePlateNo1 = trim($_POST["vehiclePlateNo1"]);
    }

    if(filter_has_var(INPUT_POST,'manualVehicle')) {
        $vehiclePlateNo1 = trim($_POST["vehicleNoTxt"]);
    }

    if(($weightType == 'Normal' || $weightType == 'Empty Container') && ($grossIncoming != null && $tareOutgoing != null)){
        $isComplete = 'Y';
    }
    else if($weightType == 'Container' && ($grossIncoming != null && $tareOutgoing != null && $grossIncoming2 != null && $tareOutgoing2 != null)){
        $isComplete = 'Y';
    }
    else{
        $isComplete = 'N';
    }

    if(isset($_POST['status']) && $_POST['status'] != null && $_POST['status'] != ''){
        if($_POST['status'] == 'pending'){
            $isComplete = 'N';
            $isApproved = 'N';
        }
    }

    if(isset($_POST['bypassReason']) && $_POST['bypassReason'] != null && $_POST['bypassReason'] != ''){
        $approved_reason = $_POST['bypassReason'];
    }
    else{
        $approved_reason = null;
    }

    if(isset($_POST['noOfDrum']) && $_POST['noOfDrum'] != null && $_POST['noOfDrum'] != ''){
        $noOfDrum = $_POST['noOfDrum'];
    }
    else{
        $noOfDrum = null;
    }

    if(isset($_POST['balance']) && $_POST['balance'] != null && $_POST['balance'] != ''){
        $prevBalance = $_POST['balance'];
    }
    else{
        $prevBalance = 0;
    } 

    /*if(isset($_POST['emptyContainerNo']) && $_POST['emptyContainerNo'] != null && $_POST['emptyContainerNo'] != ''){
        $emptyContainerNo = $_POST['emptyContainerNo'];
    }
    else{
        $emptyContainerNo = null;
    } 

    if($_POST['grossIncomingDate'] != null && $_POST['grossIncomingDate'] != ''){
        // $inDate = new DateTime($_POST['grossIncomingDate']);
        // $inCDateTime = date_format($inDate,"Y-m-d H:i:s");
        $pStatus = "Pending";
    }

    if($_POST['tareOutgoingDate'] != null && $_POST['tareOutgoingDate'] != ''){
        // $outDate = new DateTime($_POST['tareOutgoingDate']);
        // $outGDateTime = date_format($outDate,"Y-m-d H:i:s");
        $pStatus = "Complete";
    }*/

    if($weightType == 'Empty Container'){
        if(! empty($weightId)){
            $action = "2";
            
            if ($update_stmt = $db->prepare("UPDATE Weight_Container SET transaction_id=?, transaction_status=?, weight_type=?, customer_type=?, transaction_date=?, lorry_plate_no1=?, lorry_plate_no2=?, supplier_weight=?, order_weight=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?,
            product_code=?, product_name=?, ex_del=?, raw_mat_code=?, raw_mat_name=?, site_name=?, site_code=?, container_no=?, seal_no=?, container_no2=?, seal_no2=?, invoice_no=?, purchase_order=?, delivery_no=?, transporter_code=?, transporter=?, destination_code=?, destination=?, remarks=?, gross_weight1=?, gross_weight1_date=?, gross_weight_by1=?, tare_weight1=?, tare_weight1_date=?, tare_weight_by1=?, nett_weight1=?,
            gross_weight2=?, gross_weight2_date=?, gross_weight_by2=?, tare_weight2=?, tare_weight2_date=?, tare_weight_by2=?, nett_weight2=?, reduce_weight=?, final_weight=?, weight_different=?, is_complete=?, is_cancel=?, manual_weight=?, indicator_id=?, weighbridge_id=?, created_by=?, modified_by=?, indicator_id_2=?, 
            product_description=?, unit_price=?, sub_total=?, sst=?, total_price=?, is_approved=?, approved_reason=?, plant_code=?, plant_name=?, agent_code=?, agent_name=?, load_drum=?, no_of_drum=? WHERE id=?"))
            {
                $update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
                $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $sealNo, $containerNo2, $sealNo2, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
                $grossIncoming, $grossIncomingDate, $grossWeightBy1, $tareOutgoing, $tareOutgoingDate, $tareWeightBy1, $nettWeight, $grossIncoming2, $grossIncomingDate2, $grossWeightBy2, $tareOutgoing2, $tareOutgoingDate2, $tareWeightBy2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
                $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum, $weightId);
    
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
                }
                else
                {
                    $update_stmt->close();
                    $db->close();
    
                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> "Updated Successfully!!",
                            "id"=>$weightId
                        )
                    );
                }
                
            }
        }
        else{
            $action = "1";
    
            if ($insert_stmt = $db->prepare("INSERT INTO Weight_Container (transaction_id, transaction_status, weight_type, customer_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, customer_code, customer_name, supplier_code, supplier_name,
            product_code, product_name, ex_del, raw_mat_code, raw_mat_name, site_code, site_name, container_no, seal_no, container_no2, seal_no2, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, tare_weight1, tare_weight1_date, tare_weight_by1, nett_weight1,
            gross_weight2, gross_weight2_date, gross_weight_by2, tare_weight2, tare_weight2_date, tare_weight_by2, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, manual_weight, indicator_id, weighbridge_id, created_by, modified_by, indicator_id_2, 
            product_description, unit_price, sub_total, sst, total_price, is_approved, approved_reason, plant_code, plant_name, agent_code, agent_name, load_drum, no_of_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
                $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $sealNo, $containerNo2, $sealNo2, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
                $grossIncoming, $grossIncomingDate, $grossWeightBy1, $tareOutgoing, $tareOutgoingDate, $tareWeightBy1, $nettWeight, $grossIncoming2, $grossIncomingDate2, $grossWeightBy2, $tareOutgoing2, $tareOutgoingDate2, $tareWeightBy2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
                $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum);
    
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $insert_stmt->error
                        )
                    );
                }
                else{
                    $misValue++;
                    $id = $insert_stmt->insert_id;
    
                    $queryPlantU = "UPDATE Plant SET sales=? WHERE plant_code='$plantCode'";
    
                    if($status == 'Purchase'){
                        $queryPlantU = "UPDATE Plant SET purchase=? WHERE plant_code='$plantCode'";
                    }
                    else if($status == 'Local'){
                        $queryPlantU = "UPDATE Plant SET locals=? WHERE plant_code='$plantCode'";
                    }
                    else if($status == 'Misc'){
                        $queryPlantU = "UPDATE Plant SET misc=? WHERE plant_code='$plantCode'";
                    }
                    
                    ///insert miscellaneous
                    if ($update_stmt = $db->prepare($queryPlantU)){
                        $update_stmt->bind_param('s', $misValue);
                        
                        // Execute the prepared query.
                        if (! $update_stmt->execute()){
            
                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> $update_stmt->error
                                )
                            );
                        } 
                        else{
                            $update_stmt->close();

                            echo json_encode(
                                array(
                                    "status"=> "success", 
                                    "message"=> "Added Successfully!!",
                                    "id"=>$id
                                )
                            );
                        }
                    } 
                    else{
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt->error
                            )
                        );
                    }
    
                    $insert_stmt->close();
                    $db->close();
                }
            }
        }  
    }else{
        if(! empty($weightId)){
            $action = "2";
    
            if ($update_stmt = $db->prepare("UPDATE Weight SET transaction_id=?, transaction_status=?, weight_type=?, customer_type=?, transaction_date=?, lorry_plate_no1=?, lorry_plate_no2=?, supplier_weight=?, order_weight=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?,
            product_code=?, product_name=?, ex_del=?, raw_mat_code=?, raw_mat_name=?, site_name=?, site_code=?, container_no=?, seal_no=?, container_no2=?, seal_no2=?, invoice_no=?, purchase_order=?, delivery_no=?, transporter_code=?, transporter=?, destination_code=?, destination=?, remarks=?, gross_weight1=?, gross_weight1_date=?, gross_weight_by1=?, tare_weight1=?, tare_weight1_date=?, tare_weight_by1=?, nett_weight1=?,
            gross_weight2=?, gross_weight2_date=?, gross_weight_by2=?, tare_weight2=?, tare_weight2_date=?, tare_weight_by2=?, nett_weight2=?, reduce_weight=?, final_weight=?, weight_different=?, is_complete=?, is_cancel=?, manual_weight=?, indicator_id=?, weighbridge_id=?, created_by=?, modified_by=?, indicator_id_2=?, 
            product_description=?, unit_price=?, sub_total=?, sst=?, total_price=?, is_approved=?, approved_reason=?, plant_code=?, plant_name=?, agent_code=?, agent_name=?, load_drum=?, no_of_drum=? WHERE id=?"))
            {
                $update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
                $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $sealNo, $containerNo2, $sealNo2, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
                $grossIncoming, $grossIncomingDate, $grossWeightBy1, $tareOutgoing, $tareOutgoingDate, $tareWeightBy1, $nettWeight, $grossIncoming2, $grossIncomingDate2, $grossWeightBy2, $tareOutgoing2, $tareOutgoingDate2, $tareWeightBy2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
                $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum, $weightId);
    
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
                }
                else{
                    // update empty container status
                    if(!empty($containerNo) && $weightType == 'Container'){
                        if ($update_container = $db->prepare("UPDATE Weight_Container SET is_cancel=? WHERE container_no=? AND status='0'")){
                           $update_container->bind_param('ss', $isComplete, $containerNo);

                           // Execute the prepared query.
                           if (! $update_container->execute()) {
                               echo json_encode(
                                   array(
                                       "status"=> "failed", 
                                       "message"=> $update_container->error
                                   )
                               );
                           }
                           else
                           {
                               $update_container->close();

                               echo json_encode(
                                   array(
                                       "status"=> "success", 
                                       "message"=> "Updated Successfully!!",
                                       "id"=>$weightId
                                   )
                               );
                           }
                        }
                    }else{
                        echo json_encode(
                            array(
                                "status"=> "success", 
                                "message"=> "Updated Successfully!!",
                                "id"=>$weightId
                            )
                        );
                    }
    
                    $update_stmt->close();
                    $db->close();
                }
                
            }
        }
        else{
            $action = "1";
    
            if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, customer_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, customer_code, customer_name, supplier_code, supplier_name,
            product_code, product_name, ex_del, raw_mat_code, raw_mat_name, site_code, site_name, container_no, seal_no, container_no2, seal_no2, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, gross_weight_by1, tare_weight1, tare_weight1_date, tare_weight_by1, nett_weight1,
            gross_weight2, gross_weight2_date, gross_weight_by2, tare_weight2, tare_weight2_date, tare_weight_by2, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, manual_weight, indicator_id, weighbridge_id, created_by, modified_by, indicator_id_2, 
            product_description, unit_price, sub_total, sst, total_price, is_approved, approved_reason, plant_code, plant_name, agent_code, agent_name, load_drum, no_of_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $customerType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
                $supplierCode, $supplierName, $productCode, $productName, $exDel, $rawMaterialCode, $rawMaterialName, $siteCode, $siteName, $containerNo, $sealNo, $containerNo2, $sealNo2, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
                $grossIncoming, $grossIncomingDate, $grossWeightBy1, $tareOutgoing, $tareOutgoingDate, $tareWeightBy1, $nettWeight, $grossIncoming2, $grossIncomingDate2, $grossWeightBy2, $tareOutgoing2, $tareOutgoingDate2, $tareWeightBy2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
                $isComplete, $isCancel, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $unitPrice, $subTotalPrice, $sstPrice, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $agentCode, $agent, $loadDrum, $noOfDrum);
    
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $insert_stmt->error
                        )
                    );
                }
                else{
                    $misValue++;
                    $id = $insert_stmt->insert_id;
    
                    $queryPlantU = "UPDATE Plant SET sales=? WHERE plant_code='$plantCode'";
    
                    if($status == 'Purchase'){
                        $queryPlantU = "UPDATE Plant SET purchase=? WHERE plant_code='$plantCode'";
                    }
                    else if($status == 'Local'){
                        $queryPlantU = "UPDATE Plant SET locals=? WHERE plant_code='$plantCode'";
                    }
                    else if($status == 'Misc'){
                        $queryPlantU = "UPDATE Plant SET misc=? WHERE plant_code='$plantCode'";
                    }
                    
                    ///insert miscellaneous
                    if ($update_stmt = $db->prepare($queryPlantU)){
                        $update_stmt->bind_param('s', $misValue);
                        
                        // Execute the prepared query.
                        if (! $update_stmt->execute()){
            
                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> $update_stmt->error
                                )
                            );
                        } 
                        else{
                            $update_stmt->close();
                            //$db->close();
    
                            # Update PO or SO table row balance
                            /*if ($transactionStatus == 'Purchase'){
                                $currentBalance = $prevBalance - $supplierWeight;
                                $poSo_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND status='Open' AND deleted='0'");
                            }else{
                                $currentBalance = $prevBalance - $nettWeight;
                                $poSo_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND status='Open' AND deleted='0'");
                            }
    
                            $poSo_stmt->bind_param('s', $purchaseOrder);
                            $poSo_stmt->execute();
                            $result = $poSo_stmt->get_result();
                            $poSoRow = $result->fetch_assoc();    
                            $poSoId = $poSoRow['id'];
    
                            if ($currentBalance <= 0){
                                $poSoStatus = 'Close'; //set status to close if current balance is less than equal 0
                            }else{
                                $poSoStatus = 'Open';
                            }
    
                            $poSo_stmt->close();
    
                            if ($transactionStatus == 'Purchase'){
                                $updatePoSoStmt = $db->prepare("UPDATE Purchase_Order SET balance=?, status=? WHERE id=?");
                            }else{
                                $updatePoSoStmt = $db->prepare("UPDATE Sales_Order SET balance=?, status=? WHERE id=?");
                            }
    
                            $updatePoSoStmt->bind_param('sss', $currentBalance, $poSoStatus, $poSoId);
                            $updatePoSoStmt->execute();
        
                            $updatePoSoStmt->close();*/

                            // update empty container status
                            // if(!empty($containerNo)){
                            //     if ($update_container = $db->prepare("UPDATE Weight_Container SET is_complete=? WHERE container_no=? AND status='0'")){
                            //         $update_container->bind_param('ss', $isComplete, $containerNo);
    
                            //         // Execute the prepared query.
                            //         if (! $update_container->execute()) {
                            //             echo json_encode(
                            //                 array(
                            //                     "status"=> "failed", 
                            //                     "message"=> $update_container->error
                            //                 )
                            //             );
                            //         }
                            //         else
                            //         {
                            //             $update_container->close();

                            //             echo json_encode(
                            //                 array(
                            //                     "status"=> "success", 
                            //                     "message"=> "Added Successfully!!",
                            //                     "id"=>$id
                            //                 )
                            //             );
                            //         }
                            //     }
                            // }else{
                                
                            // }

                            echo json_encode(
                                array(
                                    "status"=> "success", 
                                    "message"=> "Added Successfully!!",
                                    "id"=>$id
                                )
                            );
                        }
                    } 
                    else{
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt->error
                            )
                        );
                    }
    
                    // $sel = mysqli_query($db,"select count(*) as allcount from Vehicle");
                    // $records = mysqli_fetch_assoc($sel);
                    // $totalRecords = $records['allcount'];
    
                    // if ($insert_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, vehicle_weight, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                    //     $insert_log->bind_param('sssss', $totalRecords, $vehicleNo, $vehicleWeight, $action, $username);
            
                    //     // Execute the prepared query.
                    //     if (! $insert_log->execute()) {
                    //         // echo json_encode(
                    //         //     array(
                    //         //         "status"=> "failed", 
                    //         //         "message"=> $insert_stmt->error
                    //         //     )
                    //         // );
                    //     }
                    //     else{
                    //         $insert_log->close();
                    //         // echo json_encode(
                    //         //     array(
                    //         //         "status"=> "success", 
                    //         //         "message"=> "Added Successfully!!" 
                    //         //     )
                    //         // );
                    //     }
                    // }
    
                    $insert_stmt->close();
                    $db->close();
                }
            }
        }  
    }
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>