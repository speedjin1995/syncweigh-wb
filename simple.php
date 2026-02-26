<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$pid = "1";
$pid2 = "2";
$user = $_SESSION['id'];
$username = $_SESSION["username"];
$plantId = $_SESSION['plant'];
$allowManual = $_SESSION['allowManual'];
$stmt = $db->prepare("SELECT * from Port WHERE id = ?");
$stmt->bind_param('s', $pid);
$stmt->execute();
$result = $stmt->get_result();
//$role = 'NORMAL';
$port = 'COM5';
$baudrate = 9600;
$databits = "8";
$parity = "N";
$stopbits = '1';
$indicator = 'X722';
    
if(($row = $result->fetch_assoc()) !== null){
    //$role = $row['role_code'];
    $port = $row['com_port'];
    $baudrate = $row['bits_per_second'];
    $databits = $row['data_bits'];
    $parity = $row['parity'];
    $stopbits = $row['stop_bits'];
    $indicator = $row['indicator'];
}


$stmt2 = $db->prepare("SELECT * from Port WHERE id = ?");
$stmt2->bind_param('s', $pid2);
$stmt2->execute();
$result2 = $stmt2->get_result();

if(($row2 = $result2->fetch_assoc()) !== null){
    $port2 = $row2['com_port'];
    $baudrate2 = $row2['bits_per_second'];
    $databits2 = $row2['data_bits'];
    $parity2 = $row2['parity'];
    $stopbits2 = $row2['stop_bits'];
    $indicator2 = $row2['indicator'];
}
$plantName = '-';

if($plantId != null && count($plantId) > 0){
    $stmt2 = $db->prepare("SELECT * from Plant WHERE plant_code = ?");
    $stmt2->bind_param('s', $plantId[0]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
        
    if(($row2 = $result2->fetch_assoc()) !== null){
        $plantName = $row2['name'];
    }
}

$role = 'NORMAL';
if ($user != null && $user != ''){
    $stmt3 = $db->prepare("SELECT * from Users WHERE id = ?");
    $stmt3->bind_param('s', $user);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
        
    if(($row3 = $result3->fetch_assoc()) !== null){
        $role = $row3['role'];
    }
}


//$lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$product = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$graders = $db->query("SELECT * FROM Grader WHERE status = '0' ORDER BY grader_name ASC");
$drivers = $db->query("SELECT * FROM Driver WHERE status = '0' ORDER BY driver_name ASC");

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}


// Get Company Detail
$stmt = $db->prepare("SELECT * from Company WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();

$includePrice = '';
$includeContainer = '';
$includeGrading = '';
$package = '';

if(($row = $result->fetch_assoc()) !== null){
    $includePrice = $row['include_price'];
    $includeContainer = $row['include_container'];
    $includeGrading = $row['include_grading'];
    $package = $row['package'];
}

if ($package == 'Standard') {
    echo "<script>window.location = 'index.php';</script>";
}

// For Deductions
$did = '1';
$status = 'Disable';
$F1 = $F2 = $F3 = $F4 = $F5 = $F6 = $F7 = $F8 = $F9 = $F10 = $F11 = $F12 = 0;
$autoDataJson = '[]';
$autoCustomerJson = '[]';
$autoSupplierJson = '[]';
$default_range_min = $default_range_max = $default_range_weight = 0;

$stmtd = $db->prepare("SELECT * FROM Deduction WHERE id = ? LIMIT 1");
$stmtd->bind_param('s', $did);
$stmtd->execute();
$resultd = $stmtd->get_result();

if ($rowd = $resultd->fetch_assoc()) {
    $dstatus = $rowd['status'] ?? 'Disable';
    $F1 = $rowd['F1'] ?? 0;
    $F2 = $rowd['F2'] ?? 0;
    $F3 = $rowd['F3'] ?? 0;
    $F4 = $rowd['F4'] ?? 0;
    $F5 = $rowd['F5'] ?? 0;
    $F6 = $rowd['F6'] ?? 0;
    $F7 = $rowd['F7'] ?? 0;
    $F8 = $rowd['F8'] ?? 0;
    $F9 = $rowd['F9'] ?? 0;
    $F10 = $rowd['F10'] ?? 0;
    $F11 = $rowd['F11'] ?? 0;
    $F12 = $rowd['F12'] ?? 0;
    $autoDataJson = $rowd['auto_data'] ?? '[]';
    $autoCustomerJson = $rowd['customers'] ?? '[]';
    $autoSupplierJson = $rowd['suppliers'] ?? '[]';
    //$autoDataArray = json_decode($autoDataJson, true) ?? [];
    //$default_range_min = $rowd['default_range_min'] ?? 0;
    //$default_range_max = $rowd['default_range_max'] ?? 0  ;
}
?>

<head>

    <title>Weighing | PWS - Weighing System</title>
    <?php include 'layouts/title-meta.php'; ?>

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!--datatable css-->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Include jQuery library -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>

    <?php include 'layouts/head-css2.php'; ?>
    <style>
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .modal-header {
            padding: var(1rem, 1rem) !important;
        }

        #gradingModal {
            z-index: 1055 !important;
        }

        #gradingModal .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="h-100">
                            <div class="row mb-3 pb-1">
                                <div class="col-12">
                                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                        <div class="flex-grow-1">
                                            <!--h4 class="fs-16 mb-1">Good Morning, Anna!</h4>
                                            <p class="text-muted mb-0">Here's what's happening with your store
                                                today.</p-->
                                        </div>
                                        <div class="mt-3 mt-lg-0">
                                            <form action="javascript:void(0);">
                                                <div class="row g-3 mb-0 align-items-center">

                                            </form>
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="row">
                                <div class="col-5">
                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="hstack gap-2 justify-content-center">
                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                        <div class="card bg-danger">
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <h4 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="indicatorWeight">0</span> Kg</h4>
                                                                    </div>
                                                                    <div>
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                                                                <i class="mdi mdi-weight-kilogram"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div><!-- end card body -->
                                                        </div> <!-- end card-->
                                                    </div> <!-- end col-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <div class="col-12 mb-3" <?php 
                                                            if($_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY' && $_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'MANAGER' && $allowManual == 'N'){
                                                                echo 'style="display:none;"';
                                                            }?>>
                                                            <div class="row">
                                                                <label for="manualWeight" class="col-sm-4 col-form-label">Manual Weight</label>
                                                                <div class="col-sm-8">
                                                                    <div class="form-check align-radio mr-2">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightYes" value="true">
                                                                        <label class="form-check-label" for="manualWeightYes">
                                                                            Yes
                                                                        </label>
                                                                    </div>

                                                                    <div class="form-check align-radio">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightNo" value="false" checked>
                                                                        <label class="form-check-label" for="manualWeightNo">
                                                                            No
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="transactionStatus" class="col-sm-4 col-form-label">Trans Status</label>
                                                                <div class="col-sm-8">
                                                                    <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                        <option value="Sales" selected>Sales</option>
                                                                        <option value="Purchase">Purchase</option>
                                                                        <option value="Local">Public</option>
                                                                    </select>  
                                                                </div>
                                                            </div>
                                                        </div><br>
                                                        <div class="row">
                                                            <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">
                                                                Vehicle Plate No.
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <div class="input-group-text">
                                                                        <input class="form-check-input mt-0" id="manualVehicle" name="manualVehicle" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                    </div>
                                                                    <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No" style="display:none" >
                                                                    <div class="col-10 index-vehicle">
                                                                        <select class="form-select select2" id="vehiclePlateNo1" name="vehiclePlateNo1" >
                                                                            <option selected="-">-</option>
                                                                            <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                                                                                <option value="<?=$row2['veh_number'] ?>" data-weight="<?=$row2['vehicle_weight'] ?>"><?=$row2['veh_number'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <input type="text" class="form-control" id="vehiclePlateNo1Edit" name="vehiclePlateNo1Edit" hidden>
                                                                    </div>
                                                                    <!--div class="invalid-feedback">
                                                                        Please fill in the field.
                                                                    </div-->
                                                                </div>
                                                            </div>
                                                            <!--div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No">
                                                                </div>
                                                            </div-->
                                                        </div><br>
                                                        <div class="row">
                                                            <div class="col-12" id="divCustomerName">
                                                                <div class="row">
                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select js-choice select2" id="customerName" name="customerName" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>" data-id="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12" id="divSupplierName" style="display:none;">
                                                                <div class="row">
                                                                    <label for="supplierName" class="col-sm-4 col-form-label">Supplier Name</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="supplierName" name="supplierName" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowSupplier=mysqli_fetch_assoc($supplier)){ ?>
                                                                                <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>" data-id="<?=$rowSupplier['id'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><br>
                                                        <div class="row" id="productNameDisplay">
                                                            <label for="productName" class="col-sm-4 col-form-label">Product</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-select select2" id="productName" name="productName" required>
                                                                    <option selected="-">-</option>
                                                                    <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                        <option 
                                                                            value="<?=$rowProduct['name'] ?>" 
                                                                            data-price="<?=$rowProduct['price'] ?>" 
                                                                            data-code="<?=$rowProduct['product_code'] ?>" 
                                                                            data-high="<?=$rowProduct['high'] ?>" 
                                                                            data-low="<?=$rowProduct['low'] ?>" 
                                                                            data-variance="<?=$rowProduct['variance'] ?>" 
                                                                            data-description="<?=$rowProduct['description'] ?>">
                                                                            <?=$rowProduct['product_code'] ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>                                                                                        
                                                            </div>
                                                        </div>
                                                        <div class="row" id="rawMaterialDisplay" style="display:none;">
                                                            <label for="rawMaterialName" class="col-sm-4 col-form-label">Raw Material</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-select select2" id="rawMaterialName" name="rawMaterialName" required>
                                                                    <option selected="-">-</option>
                                                                    <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                        <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] ?></option>
                                                                    <?php } ?>
                                                                </select>           
                                                            </div>
                                                        </div><br>
                                                        <div class="row" id="unitPriceDisplay" style="display:none;">
                                                            <label for="unitPrice" class="col-sm-4 col-form-label">
                                                                Unit Price
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control input-readonly" id="unitPrice" name="unitPrice" placeholder="0">
                                                                    <div class="input-group-text">RM</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br id="unitPriceBr" style="display:none;">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="grossIncoming" class="col-sm-4 col-form-label">Incoming</label>
                                                                <div class="col-sm-8">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                        <div class="input-group-text">Kg</div>
                                                                        <button class="input-group-text btn btn-success fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" style="display:none;">
                                                            <div class="row">
                                                                <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate">
                                                                </div>
                                                            </div>
                                                        </div><br>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="tareOutgoing" class="col-sm-4 col-form-label">Outgoing</label>
                                                                <div class="col-sm-8">                                                                                     
                                                                    <div class="input-group">
                                                                        <!-- <div class="input-group-text">
                                                                            <input class="form-check-input mt-0" id="manualOutgoing" name="manualOutgoing" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                        </div>                                                                                                -->
                                                                        <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0" readonly>
                                                                        <div class="input-group-text">Kg</div>
                                                                        <button class="input-group-text btn btn-success fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                    </div>                                                                                       
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12" style="display:none;">
                                                            <div class="row">
                                                                <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate">
                                                                </div>
                                                            </div> 
                                                        </div><br>
                                                        <div class="col-12">                                                                       
                                                            <div class="row mb-3">
                                                                <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                <div class="col-sm-8">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                        <div class="input-group-text">Kg</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><br>
                                                        <div class="row">
                                                            <label for="driverName" class="col-sm-4 col-form-label">Driver Name</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-select js-choice select2" id="driverName" name="driverName">
                                                                    <option selected="-">-</option>
                                                                    <?php while($rowDriver=mysqli_fetch_assoc($drivers)){ ?>
                                                                        <option value="<?=$rowDriver['driver_name'] ?>" data-code="<?=$rowDriver['driver_ic'] ?>"><?=$rowDriver['driver_name'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div><br>
                                                        <div class="row">
                                                            <label for="driverIc" class="col-sm-4 col-form-label">Driver IC</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="driverIc" name="driverIc" placeholder="Driver IC">
                                                            </div>
                                                        </div><br>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="otherRemarks" class="col-4 col-form-label">Remarks</label>
                                                                <div class="col-8">
                                                                    <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                                                                                                                  
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="bypassReason" name="bypassReason">
                                        <input type="hidden" id="finalWeight" name="finalWeight">
                                        <input type="hidden" id="customerCode" name="customerCode">
                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                        <input type="hidden" id="plantCode" name="plantCode">
                                        <input type="hidden" id="agentCode" name="agentCode">
                                        <input type="hidden" id="status" name="status">
                                        <input type="hidden" id="productCode" name="productCode">
                                        <input type="hidden" id="productDescription" name="productDescription">
                                        <input type="hidden" id="productPrice" name="productPrice">
                                        <input type="hidden" id="productHigh" name="productHigh">
                                        <input type="hidden" id="productLow" name="productLow">
                                        <input type="hidden" id="productVariance" name="productVariance">
                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                        <input type="hidden" id="rawMaterialCode" name="rawMaterialCode">
                                        <input type="hidden" id="siteCode" name="siteCode">
                                        <input type="hidden" id="id" name="id">  
                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                        <input type="hidden" id="grossWeightBy1" name="grossWeightBy1">
                                        <input type="hidden" id="tareWeightBy1" name="tareWeightBy1">
                                        <input type="hidden" id="grossWeightBy2" name="grossWeightBy2">
                                        <input type="hidden" id="tareWeightBy2" name="tareWeightBy2">
                                        <input type="hidden" id="transactionDate" name="transactionDate">
                                        <input type="hidden" id="transactionId" name="transactionId">
                                        <input type="hidden" id="deliveryNo" name="deliveryNo">
                                        <input type="hidden" id="reduceWeight" name="reduceWeight">

                                        <div class="row col-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-warning" id="addWeight">New</button>
                                                <button type="button" class="btn btn-success" id="submitWeightPrint"><?=$languageArray['submit_print_code'][$language]?></button>
                                                <button type="button" class="btn btn-primary" id="submitWeight"><?=$languageArray['submit_code'][$language]?></button>
                                            </div>
                                        </div><!--end col--> 
                                    </form>
                                </div>
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="h-100">
                                                <div class="col-xxl-12 col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header fs-5" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch" >
                                                            <i class="mdi mdi-chevron-down pull-right"></i>
                                                            Search Records
                                                        </div>
                                                        <div id="collapseSearch" class="collapse" aria-labelledby="collapseSearch">                                    
                                                            <div class="card-body">
                                                                <form action="javascript:void(0);">
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="fromDateSearch" class="form-label">From Date</label>
                                                                                <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="toDateSearch" class="form-label">To Date</label>
                                                                                <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="batchNoSearch" class="form-label">Status</label>
                                                                                <select id="batchNoSearch" class="form-select select2">
                                                                                    <option value="N" selected>Pending</option>
                                                                                    <option value="Y">Complete</option>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->   
                                                                        <div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="statusSearch" class="form-label">Transaction Status</label>
                                                                                <select id="statusSearch" class="form-select select2">
                                                                                    <option selected>-</option>
                                                                                    <option value="Sales">Sales</option>
                                                                                    <option value="Purchase">Purchase</option>
                                                                                    <option value="Local">Public</option>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3" id="customerSearchDisplay">
                                                                            <div class="mb-3">
                                                                                <label for="customerNoSearch" class="form-label">Customer No</label>
                                                                                <select id="customerNoSearch" class="form-select select2" >
                                                                                    <option selected>-</option>
                                                                                    <?php while($rowPF = mysqli_fetch_assoc($customer2)){ ?>
                                                                                        <option value="<?=$rowPF['customer_code'] ?>"><?=$rowPF['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3" id="supplierSearchDisplay" style="display:none">
                                                                            <div class="mb-3">
                                                                                <label for="supplierSearch" class="form-label">Supplier No</label>
                                                                                <select id="supplierSearch" class="form-select select2" >
                                                                                    <option selected>-</option>
                                                                                    <?php while($rowSF = mysqli_fetch_assoc($supplier2)){ ?>
                                                                                        <option value="<?=$rowSF['supplier_code'] ?>"><?=$rowSF['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="vehicleNo" class="form-label">Vehicle No</label>
                                                                                <input type="text" class="form-control" placeholder="Vehicle No" id="vehicleNo">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <!--div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="invoiceNoSearch" class="form-label">Weighing Type</label>
                                                                                <select id="invoiceNoSearch" class="form-select select2"  >
                                                                                    <option selected>-</option>
                                                                                    <option value="Normal">Normal Weighing</option>
                                                                                    <option value="Container">Primer Mover</option>
                                                                                    <option value="Empty Container">Primer Mover + Container</option>
                                                                                    <option value="Different Container">Primer Mover + Different Bins</option>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->                       
                                                                        <div class="col-3" id="productSearchDisplay">
                                                                            <div class="mb-3">
                                                                                <label for="productSearch" class="form-label">Product</label>
                                                                                <select id="productSearch" class="form-select select2" >
                                                                                    <option selected>-</option>
                                                                                    <?php while($rowProductF=mysqli_fetch_assoc($product2)){ ?>
                                                                                        <option value="<?=$rowProductF['product_code'] ?>"><?=$rowProductF['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3" id="rawMatSearchDisplay" style="display:none">
                                                                            <div class="mb-3">
                                                                                <label for="rawMatSearch" class="form-label">Raw Material</label>
                                                                                <select id="rawMatSearch" class="form-select select2" >
                                                                                    <option selected>-</option>
                                                                                    <?php while($rowRawMatF=mysqli_fetch_assoc($rawMaterial2)){ ?>
                                                                                        <option value="<?=$rowRawMatF['raw_mat_code'] ?>"><?=$rowRawMatF['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <!--div class="col-3">
                                                                            <div class="mb-3">
                                                                                <label for="containerNoSearch" class="form-label">Container No</label>
                                                                                <input type="text" class="form-control" id="containerNoSearch" name="containerNoSearch" placeholder="Container No">                                                                                  
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-3">
                                                                            <div class="text-end">
                                                                                <button type="submit" class="btn btn-success" id="filterSearch"><i class="bx bx-search-alt"></i> <?=$languageArray['search_code'][$language]?></button>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                    </div><!--end row-->
                                                                </form>                                                                        
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--datatable--> 
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="card">
                                                            <div class="card-header" >
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <h5 class="card-title mb-0">Pending Records</h5>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                            <div class="card-body">
                                                                <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No.</th>
                                                                            <th><?=$languageArray['status_code'][$language]?></th>
                                                                            <!--th>Customer/<br>Supplier</th-->
                                                                            <th>Vehicle</th>
                                                                            <th>Incoming</th>
                                                                            <th>Incoming <br>Date</th>
                                                                            <th><?=$languageArray['action_code'][$language]?></th>
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--end row-->
                                            </div> <!-- end .h-100-->
                                        </div> <!-- end col -->
                                    </div><!-- container-fluid -->
                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div class="modal fade" id="gradingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Grading Entry</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="gradingForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Transaction ID</label>
                                                                        <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Vehicle No</label>
                                                                        <input type="text" class="form-control input-readonly" id="vehicleNo" name="vehicleNo" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Ticket/DO</label>
                                                                        <input type="text" class="form-control input-readonly" id="ticketDo" name="ticketDo">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Nett Weight (MT)</label>
                                                                        <input type="text" class="form-control input-readonly" id="nettWeight" name="nettWeight" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Reduce Weight (MT)</label>
                                                                        <input type="text" class="form-control input-readonly" id="reduceWeight" name="reduceWeight">
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Final Weight (MT)</label>
                                                                        <input type="number" class="form-control input-readonly" id="finalWeight" name="finalWeight" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Reject Weight (MT)</label>
                                                                        <input type="text" class="form-control input-readonly" id="rejectWeight" name="rejectWeight" value="0">
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Graded By</label>
                                                                        <select class="form-select select2" id="grader" name="grader" required>
                                                                            <option value="">-</option>
                                                                            <?php while($rowGrader=mysqli_fetch_assoc($graders)){ ?>
                                                                                <option value="<?=$rowGrader['id'] ?>"><?=$rowGrader['grader_name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3 mt-4">
                                                                    <div class="col-xxl-12 col-lg-12">
                                                                        <h6 class="mb-2">MSPO Certification</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Type</th>
                                                                                    <th>Percent (%)</th>
                                                                                    <th>Weight (KG)</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>MSPO</td>
                                                                                    <td><input type="number" class="form-control" id="mspoPerc" name="mspoPerc" value="0.00"></td>
                                                                                    <td><input type="number" class="form-control input-readonly" id="mspoWeight" name="mspoWeight" value="0.00" readonly></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>NON MSPO</td>
                                                                                    <td><input type="number" class="form-control" id="nonMspoPerc" name="nonMspoPerc" value="0.00"></td>
                                                                                    <td><input type="number" class="form-control input-readonly" id="nonMspoWeight" name="nonMspoWeight" value="0.00" readonly></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <div class="row mt-4">
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2">Base Extraction Rate</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="50%">Bunch Size</th>
                                                                                    <th width="50%">Percent (%)</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>> 25</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize25" name="bunchSize25" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 10</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize10" name="bunchSize10" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 9-10</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize9_10" name="bunchSize9_10" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 8-9</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize8_9" name="bunchSize8_9" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 7-8</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize7_8" name="bunchSize7_8" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 6-7</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize6_7" name="bunchSize6_7" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>> 5-6</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize5_6" name="bunchSize5_6" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>5 <</td>
                                                                                    <td><input type="number" class="form-control" id="bunchSize5" name="bunchSize5" value="0.00"></td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th>Total (%)</th>
                                                                                    <th><input type="number" class="form-control" id="totalPercent" name="totalPercent" value="0.00" readonly></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2">Quality Factors</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="50%">Factors</th>
                                                                                    <th width="50%">Percent (%)</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Unripe Bunch (UR)</td>
                                                                                    <td><input type="number" class="form-control" id="unripe" name="unripe" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Underripe (UD)</td>
                                                                                    <td><input type="number" class="form-control" id="underripe" name="underripe" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Empty Bunch (EM)</td>
                                                                                    <td><input type="number" class="form-control" id="emptyBunch" name="emptyBunch" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Rotten Bunch (RB)</td>
                                                                                    <td><input type="number" class="form-control" id="rottenBunch" name="rottenBunch" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Long Stalks (LS)</td>
                                                                                    <td><input type="number" class="form-control" id="longStalks" name="longStalks" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Dirty Bunch (DB)</td>
                                                                                    <td><input type="number" class="form-control" id="dirtyBunch" name="dirtyBunch" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Dura Bunch (DD)</td>
                                                                                    <td><input type="number" class="form-control" id="duraBunch" name="duraBunch" value="0.00"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Old Bunch (OB)</td>
                                                                                    <td><input type="number" class="form-control" id="oldBunch" name="oldBunch" value="0.00"></td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th>Total (%)</th>
                                                                                    <th><input type="number" class="form-control" id="totalQualityFactor" name="totalQualityFactor" value="0.00" readonly></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <input type="hidden" id="id" name="id">
                                                            <input type="hidden" id="submitPrint" name="submitPrint">
                                                            <input type="hidden" id="transactionStatus" name="transactionStatus">
                                                            <input type="hidden" id="isEmptyContainer" name="isEmptyContainer">
                                                        </div>
                                                        
                                                        <div class="col-xxl-12 col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                                <button type="button" class="btn btn-success" id="submitGrading"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div>
                            </div> <!-- end row-->
                        </div> <!-- end .h-100-->

                    </div> <!-- end col -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <div class="modal fade" id="setupModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <form role="form" id="setupForm">
                        <div class="modal-header bg-gray-dark color-palette">
                            <h4 class="modal-title">Setup</h4>
                            <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Indicator</label>
                                        <input class="form-control" type="text" id="indicator" name="indicator" value="<?=$indicator ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Serial Port</label>
                                        <input class="form-control" type="text" id="serialPort" name="serialPort" value="<?=$port ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Baud Rate</label>
                                        <input class="form-control" type="number" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$baudrate ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Data Bits</label>
                                        <input class="form-control" type="text" id="serialPortDataBits" name="serialPortDataBits" value="<?=$databits ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Parity</label>
                                        <input class="form-control" type="text" id="serialPortParity" name="serialPortParity" value="<?=$parity ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Stop bits</label>
                                        <input class="form-control" type="text" id="serialPortStopBits" name="serialPortStopBits" value="<?=$stopbits ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                            <button type="button" class="btn btn-light" data-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="cancelModal">
                <div class="modal-dialog modal-xl" style="max-width: 90%;">
                    <div class="modal-content">
                        <form role="form" id="cancelForm">
                            <div class="modal-header bg-gray-dark color-palette">
                                <h4 class="modal-title">Cancellation Reason</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group">
                                        <label>Cancellation Reason *</label>
                                        <textarea class="form-control" id="cancelReason" name="cancelReason" rows="3"></textarea>
                                    </div>
                                    <input type="hidden" class="form-control" id="id" name="id">                                   
                                    <input type="hidden" class="form-control" id="containerId" name="containerId">                                   
                                    <input type="hidden" class="form-control" id="isEmptyContainer" name="isEmptyContainer">                                   
                                    <input type="hidden" class="form-control" id="isMulti" name="isMulti">                                   
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                <button type="button" class="btn btn-success" id="submitCancel"><?=$languageArray['submit_code'][$language]?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>
    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>
    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
    var table = null;
    var emptyContainerTable = null;
    let clickTimer = null;
    var includeGrading = '<?= $includeGrading ?>';
    var deductionValue = "-0";
    var ind = '<?=$indicator ?>';
    var grossIncomingDatePicker;
    var tareOutgoingDatePicker; 
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2; 

    $(function () {
        var userRole = '<?=$role ?>';
        const dstatus = "<?= $dstatus ?>";
        var autoDataJson = <?= json_encode($autoDataJson) ?>;
        const autoCustomerJson = <?= json_encode($autoCustomerJson) ?>;
        const autoSupplierJson = <?= json_encode($autoSupplierJson) ?>;
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);
        const ws = new WebSocket("ws://localhost:5002/ws");

        // Initialize all Select2 elements in the modal
        $('#weightForm .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#weightForm') // Ensures dropdown is not cut off
        });

        // Apply custom styling to Select2 elements in addModal
        $('.select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('.select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        grossIncomingDatePicker = $('#grossIncomingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
        });

        tareOutgoingDatePicker = $('#tareOutgoingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var supplierI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
        var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
        var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
        var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
        var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
        var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
        var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
        var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            "order": [[1, "asc"]],
            'ajax': {
                'url':'php/filterWeight2.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    customer: '',
                    supplier: '',
                    vehicle: vehicleNoI,
                    invoice: invoiceNoI,
                    batch: batchNoI,
                    product: '',
                    rawMaterial: '',
                    plant: '',
                    transactionId: '',
                    containerNo: containerNoI,
                    sealNo: ''
                }
            },
            'columns': [
                { data: 'no' },   
                { data: 'transaction_status' },
                { data: 'lorry_plate_no1' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { 
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        let buttons = `<div class="row g-1 d-flex">`;
                        buttons += `
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data}, 'Y')" class="btn btn-danger btn-sm">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>`;  
                        
                        buttons += `</div>`;

                        return buttons;
                    }
                }
            ] 
        });

        // Add event listener for double click
        $('#weightTable tbody').on('dblclick', 'tr', function (e) {
            if (clickTimer) {
                clearTimeout(clickTimer); // Cancel single-click
                clickTimer = null;
            }

            var row = table.row(this);
            var id = row.data().id;
            var weightType = row.data().weight_type;

            // run edit function
            if (weightType == 'Empty Container'){
                edit(id, 'Y');
            }else{
                edit(id, 'N');
            }
        });

        $('#submitWeight').on('click', function(){
            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                trueWeight = parseFloat($('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            pass = true;
            var isValid = true;

            // Check if grossIncoming is empty
            if (!$('#grossIncoming').val() || $('#grossIncoming').val() == '0') {
                alert('Please capture Incoming weight before saving.');
                return false;
            }

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight2.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');

                        // If Transaction Status is Purchase, open grading modal
                        if (includeGrading == 'Y' && $('#transactionStatus').val() == "Purchase" && $('#grossIncoming').val() > 0 && $('#tareOutgoing').val() > 0){
                            $.post('php/getWeight.php', { userID: obj.id }, function (data) {
                                var obj = JSON.parse(data);
                                if (obj.status === 'success') {
                                    $('#gradingModal').find('#id').val(obj.message.id);
                                    $('#gradingModal').find('#submitPrint').val('N');
                                    $('#gradingModal').find('#transactionId').val(obj.message.transaction_id);
                                    $('#gradingModal').find('#vehicleNo').val(obj.message.lorry_plate_no1);
                                    $('#gradingModal').find('#ticketDo').val(obj.message.delivery_no);
                                    $('#gradingModal').find('#nettWeight').val(obj.message.nett_weight1 ? (parseFloat(obj.message.nett_weight1)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#reduceWeight').val(obj.message.reduce_weight ? (parseFloat(obj.message.reduce_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#finalWeight').val(obj.message.final_weight ? (parseFloat(obj.message.final_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#rejectWeight').val(obj.message.reject_weight ? (parseFloat(obj.message.reject_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#grader').val(obj.message.grader_id).trigger('change');

                                    var gradingDetail = obj.message.grade_detail ? JSON.parse(obj.message.grade_detail) : {};
                                    $('#gradingModal').find('#mspoPerc').val(gradingDetail.mspo_perc || '0.00');
                                    $('#gradingModal').find('#mspoWeight').val(gradingDetail.mspo_weight || '0.00');
                                    $('#gradingModal').find('#nonMspoPerc').val(gradingDetail.non_mspo_perc || '0.00');
                                    $('#gradingModal').find('#nonMspoWeight').val(gradingDetail.non_mspo_weight || '0.00');
                                    $('#gradingModal').find('#bunchSize25').val(gradingDetail.bunch_size_25 || '0.00');
                                    $('#gradingModal').find('#bunchSize10').val(gradingDetail.bunch_size_10 || '0.00');
                                    $('#gradingModal').find('#bunchSize9_10').val(gradingDetail.bunch_size_9_10 || '0.00');
                                    $('#gradingModal').find('#bunchSize8_9').val(gradingDetail.bunch_size_8_9 || '0.00');
                                    $('#gradingModal').find('#bunchSize7_8').val(gradingDetail.bunch_size_7_8 || '0.00');
                                    $('#gradingModal').find('#bunchSize6_7').val(gradingDetail.bunch_size_6_7 || '0.00');
                                    $('#gradingModal').find('#bunchSize5_6').val(gradingDetail.bunch_size_5_6 || '0.00');
                                    $('#gradingModal').find('#bunchSize5').val(gradingDetail.bunch_size_5 || '0.00');
                                    $('#gradingModal').find('#totalPercent').val(gradingDetail.total_percent || '0.00');
                                    $('#gradingModal').find('#unripe').val(gradingDetail.unripe || '0.00');
                                    $('#gradingModal').find('#underripe').val(gradingDetail.underripe || '0.00');
                                    $('#gradingModal').find('#emptyBunch').val(gradingDetail.empty_bunch || '0.00');
                                    $('#gradingModal').find('#rottenBunch').val(gradingDetail.rotten_bunch || '0.00');
                                    $('#gradingModal').find('#longStalks').val(gradingDetail.long_stalks || '0.00');
                                    $('#gradingModal').find('#dirtyBunch').val(gradingDetail.dirty_bunch || '0.00');
                                    $('#gradingModal').find('#duraBunch').val(gradingDetail.dura_bunch || '0.00');
                                    $('#gradingModal').find('#oldBunch').val(gradingDetail.old_bunch || '0.00');
                                    $('#gradingModal').find('#totalQualityFactor').val(gradingDetail.total_quality_factor || '0.00');

                                    if (obj.message.supplier_detail && obj.message.supplier_detail.mspo_no) {
                                        var mspoPerc = gradingDetail.mspo_perc || 100;
                                        var nonMspoPerc = gradingDetail.non_mspo_perc || 0;
                                    }else{
                                        var mspoPerc = gradingDetail.mspo_perc || 0;
                                        var nonMspoPerc = gradingDetail.non_mspo_perc || 100;
                                    }

                                    $('#gradingModal').find('#mspoPerc').val(mspoPerc).trigger('keyup');
                                    // $('#gradingModal').find('#mspoWeight').val(gradingDetail.mspo_weight || '0.00');
                                    $('#gradingModal').find('#nonMspoPerc').val(nonMspoPerc).trigger('keyup');
                                    // $('#gradingModal').find('#nonMspoWeight').val(gradingDetail.non_mspo_weight || '0.00');

                                    $('#gradingModal').modal('show');
                                }
                            });

                            // <?php
                            //     if(isset($_GET['weight'])){
                            //         echo "window.location = 'simple.php';";
                            //     }
                            // ?>
                            // table.ajax.reload();
                            // window.location = 'simple.php';
                            // $("#successBtn").attr('data-toast-text', obj.message);
                            // $("#successBtn").click();
                        }else{
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = 'simple.php';";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = 'simple.php';
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
            /*else{
                let userChoice = confirm('The final value is out of the acceptable range. Do you want to send for approval (OK) or bypass (Cancel)?');
                if (userChoice) {
                    $('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = 'simple.php';";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = 'simple.php';
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', 'Failed to save');
                            $("#failBtn").click();
                        }
                    });
                } 
                else {
                    $('#bypassModal').find('#passcode').val("");
                    $('#bypassModal').find('#reason').val("");
                    $('#bypassModal').modal('show');
            
                    $('#bypassForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }*/
        });

        $('#submitWeightPrint').on('click', function(){
            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                trueWeight = parseFloat($('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            pass = true;

            var isEmptyContainer = 'N';
            if ($('#weightType').val() == 'Empty Container'){
                isEmptyContainer = 'Y';
            }

            // Check if grossIncoming is empty
            if (!$('#grossIncoming').val() || $('#grossIncoming').val() == '0') {
                alert('Please capture Incoming weight before saving.');
                return false;
            }

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight2.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');

                        // If Transaction Status is Purchase, open grading modal
                        if (includeGrading == 'Y' && $('#transactionStatus').val() == "Purchase" && $('#grossIncoming').val() > 0 && $('#tareOutgoing').val() > 0){
                            $.post('php/getWeight.php', { userID: obj.id }, function (data) {
                                var obj = JSON.parse(data);
                                if (obj.status === 'success') {
                                    $('#gradingModal').find('#id').val(obj.message.id);
                                    $('#gradingModal').find('#submitPrint').val('Y');
                                    $('#gradingModal').find('#transactionStatus').val(transactionStatus);
                                    $('#gradingModal').find('#isEmptyContainer').val(isEmptyContainer);
                                    $('#gradingModal').find('#transactionId').val(obj.message.transaction_id);
                                    $('#gradingModal').find('#vehicleNo').val(obj.message.lorry_plate_no1);
                                    $('#gradingModal').find('#ticketDo').val(obj.message.delivery_no);
                                    $('#gradingModal').find('#nettWeight').val(obj.message.nett_weight1 ? (parseFloat(obj.message.nett_weight1)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#reduceWeight').val(obj.message.reduce_weight ? (parseFloat(obj.message.reduce_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#finalWeight').val(obj.message.final_weight ? (parseFloat(obj.message.final_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#rejectWeight').val(obj.message.reject_weight ? (parseFloat(obj.message.reject_weight)/1000).toFixed(2) : '0.00');
                                    $('#gradingModal').find('#grader').val(obj.message.grader_id).trigger('change');

                                    var gradingDetail = obj.message.grade_detail ? JSON.parse(obj.message.grade_detail) : {};
                                    $('#gradingModal').find('#mspoPerc').val(gradingDetail.mspo_perc || '0.00');
                                    $('#gradingModal').find('#mspoWeight').val(gradingDetail.mspo_weight || '0.00');
                                    $('#gradingModal').find('#nonMspoPerc').val(gradingDetail.non_mspo_perc || '0.00');
                                    $('#gradingModal').find('#nonMspoWeight').val(gradingDetail.non_mspo_weight || '0.00');
                                    $('#gradingModal').find('#bunchSize25').val(gradingDetail.bunch_size_25 || '0.00');
                                    $('#gradingModal').find('#bunchSize10').val(gradingDetail.bunch_size_10 || '0.00');
                                    $('#gradingModal').find('#bunchSize9_10').val(gradingDetail.bunch_size_9_10 || '0.00');
                                    $('#gradingModal').find('#bunchSize8_9').val(gradingDetail.bunch_size_8_9 || '0.00');
                                    $('#gradingModal').find('#bunchSize7_8').val(gradingDetail.bunch_size_7_8 || '0.00');
                                    $('#gradingModal').find('#bunchSize6_7').val(gradingDetail.bunch_size_6_7 || '0.00');
                                    $('#gradingModal').find('#bunchSize5_6').val(gradingDetail.bunch_size_5_6 || '0.00');
                                    $('#gradingModal').find('#bunchSize5').val(gradingDetail.bunch_size_5 || '0.00');
                                    $('#gradingModal').find('#totalPercent').val(gradingDetail.total_percent || '0.00');
                                    $('#gradingModal').find('#unripe').val(gradingDetail.unripe || '0.00');
                                    $('#gradingModal').find('#underripe').val(gradingDetail.underripe || '0.00');
                                    $('#gradingModal').find('#emptyBunch').val(gradingDetail.empty_bunch || '0.00');
                                    $('#gradingModal').find('#rottenBunch').val(gradingDetail.rotten_bunch || '0.00');
                                    $('#gradingModal').find('#longStalks').val(gradingDetail.long_stalks || '0.00');
                                    $('#gradingModal').find('#dirtyBunch').val(gradingDetail.dirty_bunch || '0.00');
                                    $('#gradingModal').find('#duraBunch').val(gradingDetail.dura_bunch || '0.00');
                                    $('#gradingModal').find('#oldBunch').val(gradingDetail.old_bunch || '0.00');
                                    $('#gradingModal').find('#totalQualityFactor').val(gradingDetail.total_quality_factor || '0.00');

                                    if (obj.message.supplier_detail && obj.message.supplier_detail.mspo_no) {
                                        var mspoPerc = gradingDetail.mspo_perc || 100;
                                        var nonMspoPerc = gradingDetail.non_mspo_perc || 0;
                                    }else{
                                        var mspoPerc = gradingDetail.mspo_perc || 0;
                                        var nonMspoPerc = gradingDetail.non_mspo_perc || 100;
                                    }

                                    $('#gradingModal').find('#mspoPerc').val(mspoPerc).trigger('keyup');
                                    // $('#gradingModal').find('#mspoWeight').val(gradingDetail.mspo_weight || '0.00');
                                    $('#gradingModal').find('#nonMspoPerc').val(nonMspoPerc).trigger('keyup');
                                    // $('#gradingModal').find('#nonMspoWeight').val(gradingDetail.non_mspo_weight || '0.00');

                                    $('#gradingModal').modal('show');
                                }
                            });
                        }else{
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();

                            var transactionStatus = $('#transactionStatus').val();
                            print(obj.id, transactionStatus, isEmptyContainer);
                        }

                        // $.post('php/print2.php', {userID: obj.id, file: 'weight', isEmptyContainer: isEmptyContainer}, function(data){
                        //     var obj2 = JSON.parse(data);

                        //     if(obj2.status === 'success'){
                        //         var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                        //         printWindow.document.write(obj2.message);
                        //         printWindow.document.close();
                        //         setTimeout(function(){
                        //             printWindow.print();
                        //             printWindow.close();
                        //             table.ajax.reload();
                        //             window.location = 'simple.php';
                                    
                        //             /*setTimeout(function () {
                        //                 if (confirm("Do you need to reprint?")) {
                        //                     $.post('php/print2.php', { userID: obj.id, file: 'weight' }, function (data) {
                        //                         var obj = JSON.parse(data);
                        //                         if (obj.status === 'success') {
                        //                             var reprintWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                        //                             reprintWindow.document.write(obj.message);
                        //                             reprintWindow.document.close();
                        //                             setTimeout(function () {
                        //                                 reprintWindow.print();
                        //                                 reprintWindow.close();
                        //                             }, 500);
                        //                         } 
                        //                         else {
                        //                             window.location = 'simple.php';
                        //                         }
                        //                     });
                        //                 }
                        //             }, 500);*/
                        //         }, 500);
                        //     }
                        //     else if(obj.status === 'failed'){
                        //         $("#failBtn").attr('data-toast-text', obj.message );
                        //         $("#failBtn").click();
                        //     }
                        //     else{
                        //         $("#failBtn").attr('data-toast-text', "Something wrong when print");
                        //         $("#failBtn").click();
                        //     }
                        // });
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
            /*else{
                let userChoice = confirm('The final value is out of the acceptable range. Do you want to send for approval (OK) or bypass (Cancel)?');
                if (userChoice) {
                    $('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = 'simple.php';";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = 'simple.php';
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', 'Failed to save');
                            $("#failBtn").click();
                        }
                    });
                } 
                else {
                    $('#bypassModal').find('#passcode').val("");
                    $('#bypassModal').find('#reason').val("");
                    $('#bypassModal').modal('show');
            
                    $('#bypassForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }*/
        });

        $('#submitGrading').on('click', function(){
            if($('#gradingForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/updateWeightGrade.php', $('#gradingForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        var printSlip = $('#gradingModal').find('#submitPrint').val();
                        var id = $('#gradingModal').find('#id').val();
                        var transactionStatus = $('#gradingModal').find('#transactionStatus').val();
                        var isEmptyContainer = $('#gradingModal').find('#isEmptyContainer').val();

                        $('#spinnerLoading').hide();
                        $('#gradingModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        if (printSlip == 'Y'){
                            print(id, transactionStatus, isEmptyContainer);
                        }else{
                            window.location.reload();
                        }
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#submitWeights').on('click', function(){
            $('#spinnerLoading').show();
            var formData = $('#uploadForm').serializeArray();
            var data = [];
            var rowIndex = -1;
            formData.forEach(function(field) {
            var match = field.name.match(/([a-zA-Z0-9]+)\[(\d+)\]/);
            if (match) {
                var fieldName = match[1];
                var index = parseInt(match[2], 10);
                if (index !== rowIndex) {
                rowIndex = index;
                data.push({});
                }
                data[index][fieldName] = field.value;
            }
            });

            // Send the JSON array to the server
            $.ajax({
                url: 'php/uploadWeights.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj.status === 'success') {
                        $('#spinnerLoading').hide();
                        $('#uploadModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                        $('#customerTable').DataTable().ajax.reload(null, false);
                    } 
                    else if (obj.status === 'failed') {
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    } 
                    else {
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                }
            });
        });

        $('#submitCancel').on('click', function(){
            if($('#cancelForm').valid()){
                $('#spinnerLoading').show();
                var id = $('#cancelModal').find('#id').val();
                $.post('php/deleteWeight.php', $('#cancelForm').serialize(), function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        emptyContainerTable.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#cancelModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }
        });

        $.post('http://127.0.0.1:5002/', $('#setupForm').serialize(), function(data){
            if(data == "true"){
                $('#indicatorConnected').addClass('bg-primary');
                $('#checkingConnection').removeClass('bg-danger');
                //$('#captureWeight').removeAttr('disabled');
            }
            else{
                $('#indicatorConnected').removeClass('bg-primary');
                $('#checkingConnection').addClass('bg-danger');
                //$('#captureWeight').attr('disabled', true);
            }
        });

        ws.onmessage = function(event){
            var data = event.data;
            console.log("Data:", data);
            var reading = parseWeight(data);
            var autoData = JSON.parse(autoDataJson);

            if(dstatus === "Auto"){
                for (const item of autoData) {
                    if (reading >= item.rangeFrom && reading <= item.rangeTo) {
                        console.log("Matched Range:", item);
                        const msg = buildMessageAuto(item);
                
                        if (msg){
                            postMessage(msg);
                        } 
                        break;
                    }
                }
            }
            else if(dstatus === "Customer_Supplier"){
                for (const item of autoData) {
                    if (reading >= item.rangeFrom && reading <= item.rangeTo) {
                        console.log("Matched Range:", item);
                        const msg = buildMessageAuto(item);
                
                        if (msg){
                            postMessage(msg);
                        } 
                        break;
                    }
                }
            }

            setConnectedUI(true);
        };

        ws.onclose = function(){
            setConnectedUI(false);
        };

        if(dstatus === "Manual"){
            $(document).on('keydown', function(e) {
                const k = e.key; // 'F1'...'F12', 'Escape'
                if (!k) return;

                const accepted = new Set(['F1','F2','F3','F4','F5','F6','F7','F8','F9','F10','F11','F12','Escape']);
                if (!accepted.has(k)) return;

                // prevent browser defaults (F1 help, F11 fullscreen, etc.)
                e.preventDefault();

                // map Escape → ESC
                const action = (k === 'Escape') ? 'ESC' : k;

                // optionally include context
                const payload = {};
                payload.focused = document.activeElement
                    ? (document.activeElement.name || document.activeElement.id || document.activeElement.tagName)
                    : null;

                // construct message and send
                const msg = buildMessage(action);
                
                if (msg){
                    //deductionValue = msg;
                    postMessage(msg);
                } 
            });
        }
        /*else if(dstatus === "Default"){

        }*/

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
            var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
            var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
            var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';

            //Destroy the old Datatable
            $("#weightTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                "order": [[1, "asc"]],
                'ajax': {
                    'url':'php/filterWeight2.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: '',
                        supplier: '',
                        vehicle: vehicleNoI,
                        invoice: invoiceNoI,
                        batch: batchNoI,
                        product: '',
                        rawMaterial: '',
                        plant: '',
                        transactionId: '',
                        containerNo: containerNoI,
                        sealNo: ''
                    }
                },
                'columns': [
                    { data: 'no' },   
                    { data: 'transaction_status' },
                    { data: 'lorry_plate_no1' },
                    { data: 'gross_weight1' },
                    { data: 'gross_weight1_date' },
                    { 
                        data: 'id',
                        class: 'action-button',
                        render: function (data, type, row) {
                            let buttons = `<div class="row g-1 d-flex">`;
                            buttons += `
                                    <div class="col-auto">
                                        <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data}, 'Y')" class="btn btn-danger btn-sm">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>`;  
                            
                            buttons += `</div>`;

                            return buttons;
                        }
                    }
                ] 
            });
        });

        $('#addWeight').on('click', function(){
            // Show Capture Buttons When Add New
            const msg = buildMessage('ESC');
                
            if (msg){
                //deductionValue = msg;
                postMessage(msg);
            }

            $('#grossCapture').show();
            $('#tareCapture').show();
            $('#id').val("");
            $('#currentWeight').text("0");
            $('#transactionId').val("");
            $('#transactionStatus').val("Purchase").trigger('change');
            $('#emptyContainerNo').val("").trigger('change');
            $('#weightType').val("Normal").trigger('change');
            $('#customerType').val("Normal").trigger('change');
            $('#transactionDate').val(formatDate2(today));
            $('#vehiclePlateNo1').val("").trigger('change');
            $('#vehicleNoTxt').val("");
            $('#vehiclePlateNo2').val("").trigger('change');
            $('#supplierWeight').val("");
            $('#bypassReason').val("");
            $('#customerCode').val("");
            $('#customerName').val("-").trigger('change');
            $('#supplierCode').val("");
            $('#supplierName').val("-").trigger('change');
            $('#productCode').val("");
            $('#productName').val("-").trigger('change');
            $("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
            $('#rawMaterialCode').val("");
            $('#rawMaterialName').val("-").trigger('change');
            $('#siteCode').val("");
            $('#siteName').val("").trigger('change');
            $('#plantCode').val("");
            $('#sealNo').val("");
            $('#invoiceNo').val("");
            $('#purchaseOrder').val("").trigger('change');
            $('#salesOrder').val("").trigger('change');
            $('#deliveryNo').val("");
            $('#transporterCode').val("");
            $('#transporter').val("-").trigger('change');
            $('#destinationCode').val("");
            $('#agent').val("").trigger('change');
            $('#agentCode').val("");
            $('#plantCode').val("");
            $('#plant').val("<?=$plantName ?>").trigger('change');
            $('#destination').val("-").trigger('change');
            $('#replacementContainer').val('').trigger('keyup');
            $('#otherRemarks').val("");
            $('#manualVehicle').prop('checked', true).trigger('change');
            $('#manualVehicle2').prop('checked', false).trigger('change');
            $('#grossIncoming').val("");
            grossIncomingDatePicker.clear();
            $('#tareOutgoing').val("");
            tareOutgoingDatePicker.clear();
            $('#nettWeight').val("");
            $('#vehicleWeight2').val("");
            $('#emptyContainerWeight2').val("");
            $('#grossIncoming2').val("");
            $('#status').val("");
            $('#nettWeight2').val("");
            $('#reduceWeight').val("");
            // $('#vehicleNo').val(obj.message.final_weight);
            $('#weightDifference').val("");
            // $('#id').val(obj.message.is_complete);
            // $('#vehicleNo').val(obj.message.is_cancel);
            // $("#manualWeightNo").prop("checked", true);
            // $("#manualWeightYes").prop("checked", false);
            $('#manualWeightNo').trigger('click');
            //$('input[name="manualWeight"]').val("false");
            //$('#indicatorId').val("");
            $('#weighbridge').val("");
            //$('#indicatorId2').val("");
            $('#productDescription').val("");
            $('#productHigh').val("");
            $('#productLow').val("");
            $('#productVariance').val("");
            $('#orderWeight').val("0");
            $('#unitPrice').val("0.00");
            $('#subTotalPrice').val("0.00");
            $('#sstPrice').val("0.00");
            $('#productPrice').val("0.00");
            $('#totalPrice').val("0.00");
            $('#finalWeight').val("");
            $("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
            $('#noOfDrum').val("");
            $('#balance').val("");
            $('#insufficientBalDisplay').hide();
            $('#containerNoInput').val("");
            $('#containerNo').val("");
            $('#containerNo2').val("");
            $('#sealNo2').val("");
            $('#driverName').val("").trigger('change');

            // Show select and hide input readonly
            $('#salesOrderEdit').val("").hide();
            $('#purchaseOrderEdit').val("").hide();
            $('#salesOrder').next('.select2-container').show();

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            $('#addModal').modal('show');
            
            $('#weightForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        $('#uploadExccl').on('click', function(){
            $('#uploadModal').modal('show');

            $('#uploadForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        $('#uploadModal').find('#previewButton').on('click', function(){
            var fileInput = document.getElementById('fileInput');
            var file = fileInput.files[0];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var data = e.target.result;
                // Process data and display preview
                displayPreview(data);
            };

            reader.readAsBinaryString(file);
        });

        $('#exportPdf').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
            var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

            if (batchNoI == 'N'){
                batchNoI = 'Pending';
            }else if (batchNoI == 'Y'){
                batchNoI = 'Complete';
            }

            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    var type = $(this).data('type'); // Get data-type attribute
                    if (type == 'Lorry'){
                        selectedIds.push($(this).val());
                    }
                }
            });

            if (selectedIds.length > 0) {
                $.post('php/exportPdf.php', {
                    fromDate : fromDateI,
                    toDate : fromDateI,
                    transactionStatus : statusI,
                    customer : customerNoI,
                    supplier : supplierNoI,
                    vehicle : vehicleNoI,
                    weighingType : invoiceNoI,
                    status : batchNoI,
                    product : productSearchI,
                    rawMat : rawMaterialI,
                    plant : plantNoI,
                    isMulti : 'Y',
                    ids : selectedIds,
                    file : 'weight'
                }, function(response){
                    var obj = JSON.parse(response);

                    if(obj.status === 'success'){
                        var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                        printWindow.document.write(obj.message);
                        printWindow.document.close();
                        setTimeout(function(){
                            printWindow.print();
                            printWindow.close();
                        }, 500);
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                    }
                }).fail(function(error){
                    console.error("Error exporting PDF:", error);
                    alert("An error occurred while generating the PDF.");
                });
            }else{
                $.post('php/exportPdf.php', {
                    fromDate : fromDateI,
                    toDate : fromDateI,
                    transactionStatus : statusI,
                    customer : customerNoI,
                    supplier : supplierNoI,
                    vehicle : vehicleNoI,
                    weighingType : invoiceNoI,
                    status : batchNoI,
                    product : productSearchI,
                    rawMat : rawMaterialI,
                    plant : plantNoI,
                    isMulti : 'N',
                    file : 'weight'
                }, function(response){
                    var obj = JSON.parse(response);

                    if(obj.status === 'success'){
                        var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                        printWindow.document.write(obj.message);
                        printWindow.document.close();
                        setTimeout(function(){
                            printWindow.print();
                            printWindow.close();
                        }, 500);
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                    }
                }).fail(function(error){
                    console.error("Error exporting PDF:", error);
                    alert("An error occurred while generating the PDF.");
                });
            }
        });

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
            var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            
            if (batchNoI == 'N'){
                batchNoI = 'Pending';
            }else if (batchNoI == 'Y'){
                batchNoI = 'Complete';
            }

            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    var type = $(this).data('type'); // Get data-type attribute
                    if (type == 'Lorry'){
                        selectedIds.push($(this).val());
                    }
                }
            });

            if (selectedIds.length > 0) {
                window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+invoiceNoI+"&product="+productSearchI+"&rawMat="+rawMaterialI+"&plant="+plantNoI+"&status="+batchNoI+"&isMulti=Y&ids="+selectedIds);
            }else{
                window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+invoiceNoI+"&product="+productSearchI+"&rawMat="+rawMaterialI+"&plant="+plantNoI+"&status="+batchNoI+"&isMulti=N");
            }
        });

        $('#multiDeleteLorry').on('click', function(){
            var selectedLorryIds = []; // An array to store the selected 'id' values
            var selectedEmptyContainerIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    var type = $(this).data('type'); // Get data-type attribute
                    if (type == 'Lorry'){
                        selectedLorryIds.push($(this).val());
                    }else{
                        selectedEmptyContainerIds.push($(this).val());
                    }
                }
            });

            if (selectedLorryIds.length > 0 || selectedEmptyContainerIds.length > 0) {
                if (confirm('Are you sure you want to cancel these items?')) {
                    $('#cancelModal').find('#id').val(selectedLorryIds);
                    $('#cancelModal').find('#containerId').val(selectedEmptyContainerIds);
                    $('#cancelModal').find('#isEmptyContainer').val('N');
                    $('#cancelModal').find('#isMulti').val('Y');
                    $('#cancelModal').modal('show');

                    $('#cancelForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }else{
                alert("Please select at least one weighing record to delete.");
            }
        });

        $('#multiDeleteContainer').on('click', function(){
            var selectedIds = []; // An array to store the selected 'id' values

            $("#emptyContainerTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to cancel these items?')) {
                    $('#cancelModal').find('#id').val(selectedIds);
                    $('#cancelModal').find('#isEmptyContainer').val('Y');
                    $('#cancelModal').find('#isMulti').val('Y');
                    $('#cancelModal').modal('show');

                    $('#cancelForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }else{
                alert("Please select at least one weighing record to delete.");
            }
        });

        $('#weightType').on('change', function(){
            var weightType = $(this).val();
            var transaType = $('#transactionStatus').val();

            if (weightType == 'Container'){
                $.post('php/getContainers.php', {userID: transaType}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#emptyContainerNo').empty();
                            $('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#emptyContainerNo').append(
                                    '<option value="'+container_no+'">'+container_no+'</option>'
                                );  
                            }
                        }
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });

                handleWeightType(weightType);
                $('#containerNo1Label').text("Container No 1");
                $('#emptyContainerDisplay').show();
                $('#replacementContainerDisplay').hide();
                $('#vehicleWeight2Display').hide();
                $('#container2WeightDisplay').hide();
                $('#containerNo2Display').show();
                $('#containerNo2ReplaceDisplay').hide();
                $('#sealNoDisplay').show();
                $('#sealNoReplaceDisplay').hide();
                $('#sealNo2Display').show();
                $('#sealNo2ReplaceDisplay').hide();
                $('#containerDisplay').hide();
                $('#containerNoInput').attr('required', false);
                $('#emptyContainerNo').attr('required', true);
            }else if (weightType == 'Empty Container'){
                handleWeightType(weightType);
                $('#containerNo1Label').text("Container No 1");
                $('#emptyContainerDisplay').hide();
                $('#replacementContainerDisplay').hide();
                $('#vehicleWeight2Display').hide();
                $('#container2WeightDisplay').hide();
                $('#containerNo2Display').show();
                $('#containerNo2ReplaceDisplay').hide();
                $('#sealNoDisplay').show();
                $('#sealNoReplaceDisplay').hide();
                $('#sealNo2Display').show();
                $('#sealNo2ReplaceDisplay').hide();
                $('#containerDisplay').show();
                $('#containerNoInput').attr('required', true);
                $('#emptyContainerNo').attr('required', false);
            }else if (weightType == 'Different Container') {
                $.post('php/getContainers.php', {userID: transaType}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#emptyContainerNo').empty();
                            $('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#emptyContainerNo').append(
                                    '<option value="'+container_no+'">'+container_no+'</option>'
                                );  
                            }
                        }
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
                handleWeightType(weightType);
                $('#containerNo1Label').text("Pending Dispatch Bin");
                $('#emptyContainerDisplay').show();
                $('#replacementContainerDisplay').show();
                $('#vehicleWeight2Display').show();
                $('#container2WeightDisplay').show();
                $('#containerNo2Display').hide();
                $('#containerNo2ReplaceDisplay').show();
                $('#sealNoDisplay').hide();
                $('#sealNoReplaceDisplay').show();
                $('#sealNo2Display').hide();
                $('#sealNo2ReplaceDisplay').show();
                $('#containerDisplay').hide();
                $('#containerNoInput').attr('required', false);
                $('#emptyContainerNo').attr('required', true);
            }else{
                handleWeightType(weightType);
                $('#containerNo1Label').text("Container No 1");
                $('#emptyContainerDisplay').hide();
                $('#replacementContainerDisplay').hide();
                $('#vehicleWeight2Display').hide();
                $('#container2WeightDisplay').hide();
                $('#containerNo2Display').show();
                $('#containerNo2ReplaceDisplay').hide();
                $('#sealNoDisplay').show();
                $('#sealNoReplaceDisplay').hide();
                $('#sealNo2Display').show();
                $('#sealNo2ReplaceDisplay').hide();
                $('#containerDisplay').show();
                $('#containerNoInput').attr('required', false);
                $('#emptyContainerNo').attr('required', false);
            }
        });

        $('#replacementContainer').on('keyup', function(){
            var replacementContainer = $(this).val();
            $('#replaceContainerText').text(replacementContainer);
        });

        $('#manualVehicle').on('change', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo1').val('-').trigger('change');
                $('.index-vehicle').hide();
                $('#vehicleNoTxt').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt').hide();
                $('#vehicleNoTxt').val('');
                $('.index-vehicle').show();
            }
        });

        $('#vehicleNoTxt').on('keyup', function(){
            var x = $('#vehicleNoTxt').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt').val(x);
            var transactionStatus = $('#transactionStatus').val();

            if (x){
                $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var customerName = obj.message.customer_name;
                        var customerCode = obj.message.customer_code;
                        var supplierName = obj.message.supplier_name;
                        var supplierCode = obj.message.supplier_code;

                        if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                            $('#customerName').val(customerName).trigger('change');
                            $('#customerCode').val(customerCode);
                        }else{
                            $('#supplierName').val(supplierName).trigger('change');
                            $('#supplierCode').val(supplierCode);
                        }

                        if (obj.message.vehicle_weight){
                            $('#grossIncoming').val(obj.message.vehicle_weight).trigger('keyup');
                        }
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        //$('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }

            /*if (x){
                $.post('php/getVehicle.php', {userID: x, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var customerName = obj.message.customer_name;
                        var customerCode = obj.message.customer_code;
                        var supplierName = obj.message.supplier_name;
                        var supplierCode = obj.message.supplier_code;

                        if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                            $('#customerName').val(customerName).trigger('change');
                            $('#customerCode').val(customerCode);
                        }else{
                            $('#supplierName').val(supplierName).trigger('change');
                            $('#supplierCode').val(supplierCode);
                        }
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        $('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }*/
        });

        $('#vehiclePlateNo1').on('change', function(){
            var vehiclePlateNo1 = $(this).val();
            var transactionStatus = $('#transactionStatus').val();

            if (vehiclePlateNo1){
                $.post('php/getVehicle.php', {userID: vehiclePlateNo1, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var customerName = obj.message.customer_name;
                        var customerCode = obj.message.customer_code;
                        var supplierName = obj.message.supplier_name;
                        var supplierCode = obj.message.supplier_code;

                        if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                            $('#customerName').val(customerName).trigger('change');
                            $('#customerCode').val(customerCode);
                        }else{
                            $('#supplierName').val(supplierName).trigger('change');
                            $('#supplierCode').val(supplierCode);
                        }
                        
                        if (obj.message.vehicle_weight){
                            $('#grossIncoming').val(obj.message.vehicle_weight).trigger('keyup');
                        }
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        $('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }

            /*if (vehiclePlateNo1){
                $.post('php/getVehicle.php', {userID: vehiclePlateNo1, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var customerName = obj.message.customer_name;
                        var customerCode = obj.message.customer_code;
                        var supplierName = obj.message.supplier_name;
                        var supplierCode = obj.message.supplier_code;

                        if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                            $('#customerName').val(customerName).trigger('change');
                            $('#customerCode').val(customerCode);
                        }else{
                            $('#supplierName').val(supplierName).trigger('change');
                            $('#supplierCode').val(supplierCode);
                        }
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        $('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }*/
        });

        $('#manualVehicle2').on('change', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo2').val('-');
                $('.index-vehicle2').hide();
                $('#vehicleNoTxt2').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt2').hide();
                $('#vehicleNoTxt2').val('');
                $('.index-vehicle2').show();
            }
        });

        $('#vehicleNoTxt2').on('keyup', function(){
            var x = $('#vehicleNoTxt2').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt2').val(x);
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container' && x) {
                $.post('php/getVehicle.php', {userID: x, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var vehicleWeight = obj.message.vehicle_weight;
                        $('#vehicleWeight2').val(vehicleWeight);
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        $('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#vehiclePlateNo2').on('change', function(){
            var vehiclePlateNo2 = $(this).val();
            var weightType = $('#weightType').val();
            if (weightType == 'Different Container' && vehiclePlateNo2){
                $.post('php/getVehicle.php', {userID: vehiclePlateNo2, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        var vehicleWeight = obj.message.vehicle_weight;
                        $('#vehicleWeight2').val(vehicleWeight);
                    }
                    else if(obj.status === 'error'){
                        alert(obj.message);
                        $('#vehicleNoTxt').val('');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('.radio-manual-weight').on('click', function(){
            if($('input[name="manualWeight"]:checked').val() == "true"){
                $('#tareOutgoing').removeAttr('readonly');
                $('#grossIncoming').removeAttr('readonly');
                $('#tareOutgoing2').removeAttr('readonly');
                $('#grossIncoming2').removeAttr('readonly');
            }
            else{
                $('#grossIncoming').attr('readonly', 'readonly');
                $('#tareOutgoing').attr('readonly', 'readonly');
                $('#grossIncoming2').attr('readonly', 'readonly');
                $('#tareOutgoing2').attr('readonly', 'readonly');
            }
        });

        $('#grossIncoming').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');
            $('#grossWeightBy1').val('<?php echo $username; ?>');

            // Update the Flatpickr instance
            grossIncomingDatePicker.setDate(new Date()); // sets it to current date/time
            $('#grossIncomingDate').trigger('change');
        });

        $('#grossCapture').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
        });

        $('#tareOutgoing').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');
            $('#tareWeightBy1').val('<?php echo $username; ?>');

            // Update the Flatpickr instance
            tareOutgoingDatePicker.setDate(new Date()); // sets it to current date/time
            $('#tareOutgoingDate').trigger('change');

        });

        $('#tareCapture').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#nettWeight').on('change', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            }else{
                var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
                var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }

            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#reduceWeight').trigger('change');
            //$('#finalWeight').trigger('change');
        });
        
        $('#reduceWeight').on('change', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            }else{
                var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
                var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }
            var reduce = $(this).val() ? parseFloat($(this).val()) : 0;
            //var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var final = Math.abs(current - reduce);
            $('#currentWeight').text(final.toFixed(0));
            $('#finalWeight').val(final.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#finalWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = 0;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                nett2 = parseFloat($('#supplierWeight').val());
            }
            else{
                nett2 = parseFloat($('#orderWeight').val());
            }
            
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
        });

        $('#orderWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));

            var previousRecordsTag = $('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#insufficientBalDisplay').hide();
                } else {
                    $('#insufficientBalDisplay').show();
                }
            }
        });

        $('#supplierWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
            
            var previousRecordsTag = $('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#insufficientBalDisplay').hide();
                } else {
                    $('#insufficientBalDisplay').show();
                }
            }
        });

        $('#grossIncoming2').on('keyup', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var gross2 = $(this).val() ? parseFloat($(this).val()) : 0;
                var tare2 = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
                var vehicleWeight2 = $('#vehicleWeight2').val() ? parseFloat($('#vehicleWeight2').val()) : 0;
                var emptyContainerWeight2 = Math.abs(gross2 - vehicleWeight2);

                // Container 1 weights
                var emptyContainer1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var nett = Math.abs(tare2 - vehicleWeight2 - emptyContainer1); console.log(nett);

                $('#emptyContainerWeight2').val(emptyContainerWeight2);
            }else{
                var gross = $(this).val() ? parseFloat($(this).val()) : 0;
                var tare = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
                var nett = Math.abs(gross - tare);
            }

            $('#nettWeight2').val(nett.toFixed(0));
            $('#nettWeight2').trigger('change');
            $('#grossWeightBy2').val('<?php echo $username; ?>');

            // Update the Flatpickr instance
            grossIncomingDatePicker2.setDate(new Date()); // sets it to current date/time
            $('#grossIncomingDate2').trigger('change');
        });

        $('#grossCapture2').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#grossIncoming2').val(parseFloat(text).toFixed(0));
            $('#grossIncoming2').trigger('keyup');
        });

        $('#tareOutgoing2').on('keyup', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var gross2 = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
                var tare2 = $(this).val() ? parseFloat($(this).val()) : 0;
                var vehicleWeight2 = $('#vehicleWeight2').val() ? parseFloat($('#vehicleWeight2').val()) : 0;
                var emptyContainerWeight2 = Math.abs(gross2 - vehicleWeight2);
                console.log($('#grossIncoming2').val());
                console.log(vehicleWeight2);
                console.log(emptyContainerWeight2);
                $('#emptyContainerWeight2').val(emptyContainerWeight2);

                // Container 1 weights
                var emptyContainer1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var nett = Math.abs(tare2 - vehicleWeight2 - emptyContainer1);
            }else{
                var tare = $(this).val() ? parseFloat($(this).val()) : 0;
                var gross = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
                var nett = Math.abs(gross - tare);
            }

            $('#nettWeight2').val(nett.toFixed(0));
            $('#nettWeight2').trigger('change');
            $('#tareWeightBy2').val('<?php echo $username; ?>');

            // Update the Flatpickr instance
            tareOutgoingDatePicker2.setDate(new Date()); // sets it to current date/time
            $('#tareOutgoingDate2').trigger('change');

        });

        $('#tareCapture2').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing2').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing2').trigger('keyup');
        });

        $('#nettWeight2').on('change', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $(this).val() ? parseFloat($(this).val()) : 0;
            }else{
                var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
                var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }

            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#reduceWeight').trigger('change');
            //$('#finalWeight').trigger('change');
        });

        $('#currentWeight').on('change', function(){
            var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;
            var subTotalPrice = price * weight;
            var sstPrice = subTotalPrice * 0.08;
            var totalPrice = subTotalPrice + sstPrice;
            $('#subTotalPrice').val(subTotalPrice.toFixed(2));
            $('#sstPrice').val(sstPrice.toFixed(2));
            $('#totalPrice').val(totalPrice.toFixed(2));
        });

        $('#transactionStatus').on('change', function(){
            var customerType = $('#customerType').val();
            var weightType = $('#weightType').val();

            if(weightType == 'Container'){
                $.post('php/getContainers.php', {userID: $(this).val()}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#emptyContainerNo').empty();
                            $('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#emptyContainerNo').append(
                                    '<option value="'+container_no+'">'+container_no+'</option>'
                                );  
                            }
                        }
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }

            if($(this).val() == "Purchase" || $(this).val() == "Local"){
                $('#divWeightDifference').show();
                $('#divSupplierWeight').show();
                $('#orderWeight').val("");
                $('#supplierWeight').val("0");
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
                $('#rawMaterialDisplay').show();
                $('#productNameDisplay').hide();
                $('#divPoSupplyWeight').show();
                
                if ($(this).val() == "Purchase"){
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                }else{
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                }
            }
            else{
                $('#divOrderWeight').show();
                $('#orderWeight').val("0");
                $('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#divPoSupplyWeight').hide();
            }
        });

        //productName
        $('#productName').on('change', function(){
            $('#productCode').val($('#productName :selected').data('code'));
            $('#productDescription').val($('#productName :selected').data('description'));
            $('#productPrice').val($('#productName :selected').data('price'));
            $('#productHigh').val($('#productName :selected').data('high'));
            $('#productLow').val($('#productName :selected').data('low'));
            $('#productVariance').val($('#productName :selected').data('variance'));

            var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;
            var subTotalPrice = price * weight;
            var sstPrice = subTotalPrice * 0.08;
            var totalPrice = subTotalPrice + sstPrice;

            $('#unitPrice').val(price);
            $('#subTotalPrice').val(subTotalPrice.toFixed(2));
            $('#sstPrice').val(sstPrice.toFixed(2));
            $('#totalPrice').val(totalPrice.toFixed(2));
        });

        //supplierName
        $('#supplierName').on('change', function(){
            var supplierId = $('#supplierName :selected').data('id');
            $('#supplierCode').val($('#supplierName :selected').data('code'));

            // Check if selected supplier is cash or term. If cash, show price fields. If term, hide price fields.
            $.post('php/getSupplier.php', {userID: supplierId}, function(data){
                var obj = JSON.parse(data);

                if (obj.status == 'success'){
                    if (obj.message.payment_term == 'Cash'){
                        $('#unitPriceDisplay').show();
                        $('#unitPriceBr').show();
                        // $('#subTotalPriceDisplay').show();
                        // $('#sstDisplay').show();
                        // $('#totalPriceDisplay').show();
                    }else{
                        $('#unitPriceDisplay').hide();
                        $('#unitPriceBr').hide();
                        // $('#subTotalPriceDisplay').hide();
                        // $('#sstDisplay').hide();
                        // $('#totalPriceDisplay').hide();
                    }

                    if(dstatus === "Customer_Supplier"){

                    }
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });
        });

        //transporter
        $('#transporter').on('change', function(){
            $('#transporterCode').val($('#transporter :selected').data('code'));
        });

        //destination
        $('#destination').on('change', function(){
            $('#destinationCode').val($('#destination :selected').data('code'));
        });

        //plant
        $('#plant').on('change', function(){
            $('#plantCode').val($('#plant :selected').data('code'));
        });

        // SRP
        $('#agent').on('change', function(){
            $('#agentCode').val($('#agent :selected').data('code'));
        });

        //customerName
        $('#customerName').on('change', function(){
            var customerId = $('#customerName :selected').data('id');
            $('#customerCode').val($('#customerName :selected').data('code'));

            // Check if selected customer is cash or term. If cash, show price fields. If term, hide price fields.
            $.post('php/getCustomer.php', {userID: customerId}, function(data){
                var obj = JSON.parse(data);

                if (obj.status == 'success'){
                    if (obj.message.payment_term == 'Cash'){
                        $('#unitPriceDisplay').show();
                        $('#unitPriceBr').show();
                        // $('#subTotalPriceDisplay').show();
                        // $('#sstDisplay').show();
                        // $('#totalPriceDisplay').show();
                    }else{
                        $('#unitPriceDisplay').hide();
                        $('#unitPriceBr').hide();
                        // $('#subTotalPriceDisplay').hide();
                        // $('#sstDisplay').hide();
                        // $('#totalPriceDisplay').hide();
                    }

                    if(dstatus === "Customer_Supplier"){

                    }
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });
        });

        $('input[name="exDel"]').change(function() {
            var vehicleNo1 = $('#vehiclePlateNo1').val();
            var exDel = $('input[name="exDel"]:checked').val();
            if (exDel == 'true'){
                // $('#transporter').val('Own Transportation').trigger('change');
                // $('#transporterCode').val('T01');
                $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function(data){
                    var obj = JSON.parse(data);
                    if(obj.status === 'success'){
                        // var customerName = obj.message.customer_name;
                        // var customerCode = obj.message.customer_code;

                        // $('#customerName').val(customerName).trigger('change');
                        // $('#customerCode').val(customerCode);
                    }   
                    else if(obj.status === 'failed'){
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }else{
                // $('#customerName').val('').trigger('change');
                // $('#customerCode').val('');

                $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        // var transporterName = obj.message.transporter_name;
                        // var transporterCode = obj.message.transporter_code;

                        // $('#transporter').val(transporterName).trigger('change');
                        // $('#transporterCode').val(transporterCode);
                    }
                    else if(obj.status === 'failed'){
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }
        });

        //rawMaterialName
        $('#rawMaterialName').on('change', function(){
            $('#rawMaterialCode').val($('#rawMaterialName :selected').data('code'));
        });

        //siteName
        $('#siteName').on('change', function(){
            $('#siteCode').val($('#siteName :selected').data('code'));
        });

        $('input[name="loadDrum"]').change(function() {
            var selected = $(this).val();
            if (selected == 'true'){
                $("#noOfDrumDisplay").hide();
            }else{
                $("#noOfDrumDisplay").show();
            }
        });

        //Empty Container No
        $('#emptyContainerNo').on('change', function (){
            var emptyContainerNo = $(this).val();
            var weightType = $('#weightType').val();
            $('#containerNo').val(emptyContainerNo);

            if (emptyContainerNo == '-'){
                $('#manualVehicle').prop('checked', false).trigger('change');
                $('#grossIncoming').val(0);
                $('#grossIncomingDate').val("");
                $('#tareOutgoing').val(0);
                $('#tareOutgoingDate').val("");
                $('#nettWeight').val(0);
                $('#tareOutgoing2').trigger('keyup');
                $('#normalCard').hide();
            } else if (emptyContainerNo) { 
                $.post('php/getEmptyContainer.php', {userID: emptyContainerNo}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){ 
                        $('#invoiceNo').val(obj.message.invoice_no);
                        $('#deliveryNo').val(obj.message.delivery_no);
                        $('#purchaseOrder').val(obj.message.purchase_order);
                        $('#sealNo').val(obj.message.seal_no);

                        if (weightType != 'Different Container'){
                            $('#containerNo2').val(obj.message.container_no2);
                            $('#sealNo2').val(obj.message.seal_no2);
                        }

                        if (obj.message.transaction_status == 'Sales' || obj.message.transaction_status == 'Misc'){
                            $('#customerName').val(obj.message.customer_name).trigger('change');
                            $('#productName').val(obj.message.product_name).trigger('change');
                        }else{
                            $('#supplierName').val(obj.message.supplier_name).trigger('change');
                            $('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                        }
                        $('#plant').val(obj.message.plant_name).trigger('change');
                        $('#transporter').val(obj.message.transporter).trigger('change');
                        $('#destination').val(obj.message.destination).trigger('change');

                        
                        $('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                        $('#grossIncoming').val(obj.message.gross_weight1); console.log(obj.message.gross_weight1_date);
                        grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date)); 
                        // $('#grossIncomingDate').val(obj.message.gross_weight1_date);
                        $('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#tareOutgoing').val(obj.message.tare_weight1);
                        tareOutgoingDatePicker.setDate(new Date(obj.message.tare_weight1_date));
                        // $('#tareOutgoingDate').val(obj.message.tare_weight1_date);
                        $('#tareWeightBy1').val(obj.message.tare_weight_by1);
                        $('#nettWeight').val(obj.message.nett_weight1);

                        if(obj.message.vehicleNoTxt != null){
                            $('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                            $('#manualVehicle').val(1);
                            $('#manualVehicle').prop("checked", true);
                            $('.index-vehicle').hide();
                            $('#vehicleNoTxt').show();
                        }
                        else{
                            $('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                            $('#manualVehicle').val(0);
                            $('#manualVehicle').prop("checked", false);
                            $('.index-vehicle').show();
                            $('#vehicleNoTxt').hide();
                        }
                        
                        $('#tareOutgoing2').trigger('keyup');
                        
                        $('#normalCard').show();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }else{
                $('#manualVehicle').prop('checked', false).trigger('change');
                $('#grossIncoming').val(0);
                $('#grossIncomingDate').val("");
                $('#tareOutgoing').val(0);
                $('#tareOutgoingDate').val("");
                $('#nettWeight').val(0);
                $('#tareOutgoing2').trigger('keyup');
                $('#normalCard').hide();
            }
        });

        //Container No
        $('#containerNoInput').on('keyup', function(){
            var x = $('#containerNoInput').val();
            x = x.toUpperCase();
            $('#containerNoInput').val(x);
            $('#containerNo').val(x);
        });
        
        $('#containerNoInput').on('change', function () {
            $('#containerNo').val($(this).val());
        });

        //Seal No
        $('#sealNo').on('keyup', function(){
            var x = $('#sealNo').val();
            x = x.toUpperCase();
            $('#sealNo').val(x);
        });

        //Container No 2
        $('#containerNo2').on('keyup', function(){
            var x = $('#containerNo2').val();
            x = x.toUpperCase();
            $('#containerNo2').val(x);
        });

        //Seal No 2
        $('#sealNo2').on('keyup', function(){
            var x = $('#sealNo2').val();
            x = x.toUpperCase();
            $('#sealNo2').val(x);
        });

        //Container No Search
        $('#containerNoSearch').on('keyup', function(){
            var x = $('#containerNoSearch').val();
            x = x.toUpperCase();
            $('#containerNoSearch').val(x);
        });

        //Seal No Search
        $('#sealNoSearch').on('keyup', function(){
            var x = $('#sealNoSearch').val();
            x = x.toUpperCase();
            $('#sealNoSearch').val(x);
        });

        $('#driverName').on('change', function(){
            $('#driverIc').val($('#driverName :selected').data('code'));
        });

        // Grading Modal Trigger Start //
        $('#gradingModal').find('#reduceWeight').on('keyup', function(){
            var reduceWeight = $(this).val() ? parseFloat($(this).val()) : 0;
            var nettWeight = $('#gradingModal').find('#nettWeight').val() ? parseFloat($('#gradingModal').find('#nettWeight').val()) : 0;
            var finalWeight = Math.abs(nettWeight - reduceWeight);
            $('#gradingModal').find('#finalWeight').val(finalWeight.toFixed(2));
        });

        $('#bunchSize25, #bunchSize10, #bunchSize9_10, #bunchSize8_9, #bunchSize7_8, #bunchSize6_7, #bunchSize5_6, #bunchSize5').on('keyup', function(){
            var total = 0;
            $('#bunchSize25, #bunchSize10, #bunchSize9_10, #bunchSize8_9, #bunchSize7_8, #bunchSize6_7, #bunchSize5_6, #bunchSize5').each(function(){
                total += parseFloat($(this).val()) || 0;
            });
            
            if(total > 100){
                alert('Total percentage cannot exceed 100%');
                $(this).val(0);
                total = 0;
                $('#bunchSize25, #bunchSize10, #bunchSize9_10, #bunchSize8_9, #bunchSize7_8, #bunchSize6_7, #bunchSize5_6, #bunchSize5').each(function(){
                    total += parseFloat($(this).val()) || 0;
                });
            }
            
            $('#totalPercent').val(total.toFixed(2));
        });

        $('#unripe, #underripe, #emptyBunch, #rottenBunch, #longStalks, #dirtyBunch, #duraBunch, #oldBunch').on('keyup', function(){
            var total = 0;
            $('#unripe, #underripe, #emptyBunch, #rottenBunch, #longStalks, #dirtyBunch, #duraBunch, #oldBunch').each(function(){
                total += parseFloat($(this).val()) || 0;
            });
            
            if(total > 100){
                alert('Total percentage cannot exceed 100%');
                $(this).val(0);
                total = 0;
                $('#unripe, #underripe, #emptyBunch, #rottenBunch, #longStalks, #dirtyBunch, #duraBunch, #oldBunch').each(function(){
                    total += parseFloat($(this).val()) || 0;
                });
            }
            
            $('#totalQualityFactor').val(total.toFixed(2));
        });

        $('#mspoPerc').on('keyup', function(){
            var percentage = parseFloat($(this).val()) || 0;
            if (percentage < 0){
                alert('Percentage cannot be negative');
                $(this).val(0);
                percentage = 0;
            }else if (percentage > 100){
                alert('Percentage cannot exceed 100%');
                $(this).val(0);
                percentage = 0;
            }

            var nettWeight = $('#weightForm').find('#nettWeight').val() ? parseFloat($('#weightForm').find('#nettWeight').val()) : 0;
            var mspoWeight = (percentage / 100) * nettWeight;

            $('#mspoWeight').val(mspoWeight.toFixed(2));
        });

        $('#nonMspoPerc').on('keyup', function(){
            var percentage = parseFloat($(this).val()) || 0;
            if (percentage < 0){
                alert('Percentage cannot be negative');
                $(this).val(0);
                percentage = 0;
            }else if (percentage > 100){
                alert('Percentage cannot exceed 100%');
                $(this).val(0);
                percentage = 0;
            }

            var nettWeight = $('#weightForm').find('#nettWeight').val() ? parseFloat($('#weightForm').find('#nettWeight').val()) : 0;
            var nonMspoWeight = (percentage / 100) * nettWeight;

            $('#nonMspoWeight').val(nonMspoWeight.toFixed(2));
        });
        // Grading Modal Trigger End //

        <?php
            if(isset($_GET['weight'])){
                echo 'edit('.$_GET['weight'].');';
            }
        ?>

        <?php
            if(isset($_GET['approve'])){
                echo 'approve('.$_GET['approve'].');';
            }
        ?>
    });

    function handleWeightType(weightType){
        if (weightType == 'Container'){
            $('#manualVehicle').prop('checked', false).trigger('change');
            $('#grossIncoming').val(0);
            $('#grossIncomingDate').val("");
            $('#tareOutgoing').val(0);
            $('#tareOutgoingDate').val("");
            $('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#containerCard').show();
        }else if(weightType == 'Empty Container'){
            $('#manualVehicle2').prop('checked', false).trigger('change');
            $('#grossIncoming2').val(0);
            $('#grossIncomingDate2').val("");
            $('#tareOutgoing2').val(0);
            $('#tareOutgoingDate2').val("");
            $('#nettWeight2').val(0);
            $('#containerCard').hide();
            $('#normalCard').show();
        }else if(weightType == 'Different Container'){
            $('#manualVehicle').prop('checked', false).trigger('change');
            $('#grossIncoming').val(0);
            $('#grossIncomingDate').val("");
            $('#tareOutgoing').val(0);
            $('#tareOutgoingDate').val("");
            $('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#containerCard').show();
        }else{
            $('#manualVehicle2').prop('checked', false).trigger('change');
            $('#grossIncoming2').val(0);
            $('#grossIncomingDate2').val("");
            $('#tareOutgoing2').val(0);
            $('#tareOutgoingDate2').val("");
            $('#nettWeight2').val(0);
            $('#normalCard').show();
            $('#containerCard').hide();
        }
    }

    function format (row) {
        var transactionStatus = '';
        var weightType = '';

        if (row.transaction_status == 'Sales') {
            transactionStatus = 'Sales';
        } else if (row.transaction_status == 'Purchase') {
            transactionStatus = 'Purchase';
        } else if (row.transaction_status == 'Local') {
            transactionStatus = 'Public';
        } else {
            transactionStatus = 'Miscellaneous';
        }

        if(row.weight_type == 'Container'){
            weightType = 'Primer Mover';
        }else if(row.weight_type == 'Empty Container'){
            weightType = 'Primer Mover + Container';
        }else if(row.weight_type == 'Normal'){
            weightType = 'Normal Weighing';
        }else if(row.weight_type == 'Different Container'){
            weightType = 'Primer Mover + Different Container';
        }else{
            weightType = row.weight_type;
        }

        var returnString = `
        <!-- Customer Section -->
        <div class="row">
            <div class="col-6">
                <p><span><strong style="font-size:120%; text-decoration: underline;">Customer/Supplier</strong></span><br>
                <p><strong>${row.name}</strong></p>
                <p>${row.address_line_1}</p>
                <p>${row.address_line_2}</p>
                <p>${row.address_line_3}</p>
                <p>TEL: ${row.phone_no} FAX: ${row.fax_no}</p>
            </div>
        </div>
        <hr>
        <!-- Delivery Order Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Delivery Order Information</strong></span><br>
            <div class="col-6">
                <p><strong>TRANSPORTER NAME:</strong> ${row.transporter}</p>
                <p><strong>DESTINATION NAME:</strong> ${row.destination}</p>
                <p><strong>SITE NAME:</strong> ${row.site_name}</p>
                <p><strong>PLANT NAME:</strong> ${row.plant_name}</p>`;
                if (row.transaction_status == 'Purchase' || row.transaction_status == 'Local'){
                    returnString += `<p><strong>PURCHASE PRODUCT:</strong> ${row.product_rawmat_name}</p>`;
                }else{
                    returnString += `<p><strong>SALES PRODUCT:</strong> ${row.product_rawmat_name}</p>`;
                }
        
            returnString += `
                <p><strong>CONTAINER NO:</strong> ${row.container_no}</p>
                <p><strong>SEAL NO:</strong> ${row.seal_no}</p>
            </div>
            <div class="col-6">
                <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>WEIGHT STATUS:</strong> ${transactionStatus}</p>
                <p><strong>WEIGHT TYPE:</strong> ${weightType}</p>
                <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
                <p><strong>PURCHASE ORDER:</strong> ${row.purchase_order}</p>
                <p><strong>CONTAINER NO 2:</strong> ${row.container_no2}</p>
                <p><strong>SEAL NO 2:</strong> ${row.seal_no2}</p>
            </div>
        </div>
        <hr>

        <!-- Weighing Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Weighing Information</strong></span><br>
            <!-- Normal -->
            <div class="col-6">
                <p><strong>VEHICLE PLATE:</strong> ${row.lorry_plate_no1}</p>
                <p><strong>IN WEIGHT:</strong> ${row.gross_weight1}</p>
                <p><strong>IN DATE / TIME:</strong> ${row.gross_weight1_date}</p>
                <p><strong>IN WEIGH BY:</strong> ${row.gross_weight_by1}</p>
                <p><strong>OUT WEIGHT:</strong> ${row.tare_weight1}</p>
                <p><strong>OUT DATE / TIME:</strong> ${row.tare_weight1_date}</p>
                <p><strong>OUT WEIGH BY:</strong> ${row.tare_weight_by1}</p>
                <p><strong>NETT WEIGHT:</strong> ${row.nett_weight1}</p>
                <p><strong>SUB TOTAL WEIGHT:</strong> ${row.final_weight}</p>
            </div>
            <!-- Container -->
            <div class="col-6">
                <p><strong>VEHICLE PLATE 2:</strong> ${row.lorry_plate_no2}</p>
                <p><strong>IN WEIGHT 2:</strong> ${row.gross_weight2}</p>
                <p><strong>IN DATE / TIME 2:</strong> ${row.gross_weight2_date}</p>
                <p><strong>IN WEIGH BY 2:</strong> ${row.gross_weight_by2}</p>
                <p><strong>OUT WEIGHT 2:</strong> ${row.tare_weight2}</p>
                <p><strong>OUT DATE / TIME 2:</strong> ${row.tare_weight2_date}</p>
                <p><strong>OUT WEIGH BY 2:</strong> ${row.tare_weight_by2}</p>
                <p><strong>NETT WEIGHT 2:</strong> ${row.nett_weight2}</p>            
                </div>
        </div>
        `;
        
        return returnString;
    }

    function displayPreview(data) {
        // Parse the Excel data
        var workbook = XLSX.read(data, { type: 'binary' });

        // Get the first sheet
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];

        // Convert the sheet to an array of objects
        var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        // Get the headers
        var headers = jsonData[0];

        // Ensure we handle cases where there may be less than 15 columns
        while (headers.length < 18) {
            headers.push(''); // Adding empty headers to reach 15 columns
        }

        // Create HTML table headers
        var htmlTable = '<table style="width:100%;"><thead><tr>';
        headers.forEach(function(header) {
            htmlTable += '<th>' + header + '</th>';
        });
        htmlTable += '</tr></thead><tbody>';

        // Iterate over the data and create table rows
        for (var i = 1; i < jsonData.length; i++) {
            htmlTable += '<tr>';
            var rowData = jsonData[i];

            // Ensure we handle cases where there may be less than 15 cells in a row
            while (rowData.length < 18) {
                rowData.push(''); // Adding empty cells to reach 15 columns
            }

            for (var j = 0; j < 18; j++) {
                var cellData = rowData[j];
                var formattedData = cellData;

                // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
                if (typeof cellData === 'number' && cellData > 0) {
                    var excelDate = XLSX.SSF.parse_date_code(cellData);
                    if (excelDate) {
                        formattedData = formatDate2(new Date(excelDate.y, excelDate.m - 1, excelDate.d));
                    }
                }

                htmlTable += '<td><input type="text" id="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+(i-1)+'" name="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
            }
            htmlTable += '</tr>';
        }

        htmlTable += '</tbody></table>';

        var previewTable = document.getElementById('previewTable');
        previewTable.innerHTML = htmlTable;
    }

    function edit(id, isContainer){
        $('#spinnerLoading').show();
        var type = 'Weight';

        $.post('php/getWeight.php', {userID: id, type: type}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                if(obj.message.is_complete == 'Y'){
                    // Hide Capture Button When Edit
                    $('#grossCapture').hide();
                    $('#tareCapture').hide();
                }
                else{
                    // Show Capture Button When Edit
                    $('#grossCapture').show();
                    $('#tareCapture').show();
                }

                $('#id').val(obj.message.id);
                $('#transactionId').val(obj.message.transaction_id);
                $('#transactionStatus').val(obj.message.transaction_status).trigger('change');
                $('#weightType').val(obj.message.weight_type).trigger('change');
                $('#customerType').val(obj.message.customer_type).trigger('change');
                $('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));

                if(obj.message.transaction_status == "Purchase" || obj.message.transaction_status == "Local"){
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').show();
                    $('#divSupplierName').show();
                    $('#divOrderWeight').hide();
                    $('#divCustomerName').hide();
                }
                else{
                    $('#divOrderWeight').show();
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').hide();
                    $('#divSupplierName').hide();
                    $('#divCustomerName').show();
                }

                if(obj.message.vehicleNoTxt != null){
                    $('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                    $('#manualVehicle').val(1);
                    $('#manualVehicle').prop("checked", true);
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $('#vehiclePlateNo1Edit').val('EDIT');
                    $('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                    $('#manualVehicle').val(0);
                    $('#manualVehicle').prop("checked", false);
                    $('.index-vehicle').show();
                    $('#vehicleNoTxt').hide();
                }

                if(obj.message.vehicleNoTxt2 != null){
                    $('#vehicleNoTxt2').val(obj.message.vehicleNoTxt2);
                    $('#manualVehicle2').val(1);
                    $('#manualVehicle2').prop("checked", true);
                    $('.index-vehicle2').hide();
                    $('#vehicleNoTxt2').show();
                }
                else{
                    $('#vehiclePlateNo2').val(obj.message.lorry_plate_no2).trigger('change');
                    $('#manualVehicle2').val(0);
                    $('#manualVehicle2').prop("checked", false);
                    $('.index-vehicle2').show();
                    $('#vehicleNoTxt2').hide();
                }
                
                $('#productCode').val(obj.message.product_code);
                if (obj.message.ex_del == 'EX'){
                    $("input[name='exDel'][value='true']").prop("checked", true);
                }else{
                    $("input[name='exDel'][value='false']").prop("checked", true);
                }
                
                $('#driverName').val(obj.message.driver_name).trigger('change');
                $('#purchaseOrder').val(obj.message.purchase_order);
                $('#invoiceNo').val(obj.message.invoice_no);
                $('#deliveryNo').val(obj.message.delivery_no);
                $('#transporterCode').val(obj.message.transporter_code);
                $('#transporter').val(obj.message.transporter).trigger('change');
                $('#customerName').val(obj.message.customer_name).trigger('change');
                $('#supplierCode').val(obj.message.supplier_code);
                $('#supplierName').val(obj.message.supplier_name).trigger('change')
                $('#siteCode').val(obj.message.site_code);
                $('#siteName').val(obj.message.site_name).trigger('change');
                $('#agent').val(obj.message.agent_name).trigger('change');
                $('#agentCode').val(obj.message.agent_code);
                $('#rawMaterialCode').val(obj.message.raw_mat_code);
                $('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                $('#productName').val(obj.message.product_name).trigger('change');
                $('#productCode').val(obj.message.product_code);
                $('#supplierWeight').val(obj.message.supplier_weight);
                $('#orderWeight').val(obj.message.order_weight);
                $('#destinationCode').val(obj.message.destination_code);
                $('#destination').val(obj.message.destination).trigger('change');
                $('#plant').val(obj.message.plant_name).trigger('change');
                $('#plantCode').val(obj.message.plant_code);
                
                $('#otherRemarks').val(obj.message.remarks);
                $('#grossIncoming').val(obj.message.gross_weight1);
                grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date));
                $('#grossWeightBy1').val(obj.message.gross_weight_by1);
                $('#tareOutgoing').val(obj.message.tare_weight1);
                tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                $('#tareWeightBy1').val(obj.message.tare_weight_by1);
                $('#nettWeight').val(obj.message.nett_weight1);
                $('#vehicleWeight2').val(obj.message.lorry_no2_weight);
                $('#emptyContainerWeight2').val(obj.message.empty_container2_weight);
                $('#replacementContainer').val(obj.message.replacement_container).trigger('keyup');
                $('#grossIncoming2').val(obj.message.gross_weight2);
                $('#grossWeightBy2').val(obj.message.gross_weight_by2);
                $('#tareOutgoing2').val(obj.message.tare_weight2);
                $('#tareWeightBy2').val(obj.message.tare_weight_by2);
                $('#nettWeight2').val(obj.message.nett_weight2);
                $('#reduceWeight').val(obj.message.reduce_weight);
                $('#weightDifference').val(obj.message.weight_different);
                $('#currentWeight').text(obj.message.final_weight);

                if(obj.message.manual_weight == 'true'){
                    $("#manualWeightYes").prop("checked", true);
                    $("#manualWeightNo").prop("checked", false);
                    $('#manualWeightYes').trigger('click');
                }
                else{
                    $("#manualWeightYes").prop("checked", false);
                    $("#manualWeightNo").prop("checked", true);
                    $('#manualWeightNo').trigger('click');
                }

                $('#indicatorId').val(obj.message.indicator_id);
                $('#weighbridge').val(obj.message.weighbridge_id);
                $('#indicatorId2').val(obj.message.indicator_id_2);
                $('#productDescription').val(obj.message.product_description);
                $('#unitPrice').val(obj.message.unit_price);
                $('#subTotalPrice').val(obj.message.sub_total);
                $('#sstPrice').val(obj.message.sst);
                $('#totalPrice').val(obj.message.total_price);
                $('#finalWeight').val(obj.message.final_weight);

                if (obj.message.load_drum == 'LOAD'){
                    $("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
                }else{
                    $("input[name='loadDrum'][value='false']").prop("checked", true).trigger('change');
                }
                
                $('#noOfDrum').val(obj.message.no_of_drum);
                $('#containerNoInput').val(obj.message.container_no);
                $('#containerNo').val(obj.message.container_no);
                $('#containerNo2').val(obj.message.container_no2);
                $('#sealNo').val(obj.message.seal_no);
                $('#sealNo2').val(obj.message.seal_no2);

                // Load container data and update the emptyContainerNo field if it's a container
                if((obj.message.weight_type == 'Container' || obj.message.weight_type == 'Different Container') && obj.message.container_no){
                    loadContainerData(function() {
                        $('#normalCard').show();

                        // Check if container value exist in the select tag
                        var emptyContainerExists = $('#emptyContainerNo option').filter(function() {
                            return $(this).val() === obj.message.container_no;
                        }).length > 0;

                        if (!emptyContainerExists){
                            // Append missing empty container no
                            $('#emptyContainerNo').append(
                                '<option value="'+obj.message.container_no+'">'+obj.message.container_no+'</option>'
                            );
                        }

                        // Callback to ensure the dropdown is updated before setting the value
                        $('#emptyContainerNo').val(obj.message.container_no).select2('destroy').select2();

                        // Initialize all Select2 elements in the modal
                        $('#addModal .select2').select2({
                            allowClear: true,
                            placeholder: "Please Select",
                            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                        });

                        // Apply custom styling to Select2 elements in addModal
                        $('#addModal .select2-container .select2-selection--single').css({
                            'padding-top': '4px',
                            'padding-bottom': '4px',
                            'height': 'auto'
                        });

                        $('#addModal .select2-container .select2-selection__arrow').css({
                            'padding-top': '33px',
                            'height': 'auto'
                        });
                    });
                }

                // Initialize all Select2 elements in the modal
                $('#addModal .select2').select2({
                    allowClear: true,
                    placeholder: "Please Select",
                    dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                });

                // Apply custom styling to Select2 elements in addModal
                $('#addModal .select2-container .select2-selection--single').css({
                    'padding-top': '4px',
                    'padding-bottom': '4px',
                    'height': 'auto'
                });

                $('#addModal .select2-container .select2-selection__arrow').css({
                    'padding-top': '33px',
                    'height': 'auto'
                });

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

                $('#addModal').modal('show');
            
                $('#weightForm').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    function loadContainerData(callback) {
        var transactionStatus = $('#transactionStatus').val();
        $.post('php/getContainers.php', {userID: transactionStatus}, function (data){
            var obj = JSON.parse(data);

            if (obj.status == 'success'){
                if (obj.message.length > 0){
                    $('#emptyContainerNo').empty();
                    $('#emptyContainerNo').append('<option selected="-">-</option>');

                    // Populate container numbers
                    for (var i = 0; i < obj.message.length; i++) {
                        var id = obj.message[i].id;
                        var container_no = obj.message[i].container_no;

                        $('#emptyContainerNo').append(
                            '<option value="'+container_no+'">'+container_no+'</option>'
                        );
                    }

                    // Execute the callback to finalize the process
                    if (callback) {
                        callback();
                    }
                }
            } else {
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }

    function approve(id){
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#approvalModal').find('#id').val(obj.message.id);
                $('#approvalModal').find('#statusA').val('');
                $('#approvalModal').find('#reasons').val('');
                $('#approvalModal').modal('show');
            
                $('#approvalForm').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id, isEmptyContainer) {
        if (confirm('Are you sure you want to cancel this item?')) {
            $.post('php/deleteWeight.php', {id: id, cancelReason: 'Cancelled', isEmptyContainer:'N'}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    table.ajax.reload();
                    //emptyContainerTable.ajax.reload();
                    $('#spinnerLoading').hide();
                    $('#cancelModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });
        }
    }

    function print(id, transactionStatus, isEmptyContainer = 'N') {
        var prePrintStatus = 'N';
        
        if(includeGrading == 'Y'){
            $.post('php/print_pws.php', {userID: id, file: 'weight', prePrint: prePrintStatus, isEmptyContainer: isEmptyContainer}, function(data){
                var obj = JSON.parse(data);

                if(obj.status === 'success'){
                    var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                    printWindow.document.write(obj.message);
                    printWindow.document.close();
                    setTimeout(function(){
                        printWindow.print();
                        printWindow.close();
                    }, 500);

                    $('#spinnerLoading').hide();
                }
                else if(obj.status === 'failed'){
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $("#failBtn").attr('data-toast-text', "Something wrong when print");
                    $("#failBtn").click();
                }
            }); 
        }
        else{
            $.post('php/print.php', {userID: id, file: 'weight', prePrintHeader: prePrintStatus, isEmptyContainer: isEmptyContainer}, function(data){
                var obj = JSON.parse(data);

                if(obj.status === 'success'){
                    var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                    printWindow.document.write(obj.message);
                    printWindow.document.close();
                    setTimeout(function(){
                        printWindow.print();
                        printWindow.close();
                    }, 500);

                    $('#spinnerLoading').hide();
                }
                else if(obj.status === 'failed'){
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $("#failBtn").attr('data-toast-text', "Something wrong when print");
                    $("#failBtn").click();
                }
            }); 
        }
    }

    // Indicator Functions
    function parseWeight(data){
        var reading = "0";

        if(ind == 'X2S' || ind == 'X722'){
            var text = data.split(" ");
            var val = text[text.length - 1].replace(/kg|KG|Kg/g,"");
            reading = val.trim();
            $('#indicatorWeight').html(val);
        }
        else if(ind == 'BX23' || ind == '205'){
            var text = data.split(" ");
            var newtext = text.slice(1, -1).join("");
            reading = newtext.trim();
            $('#indicatorWeight').html(newtext.trim());
        }

        return reading;
    }

    function setConnectedUI(state){
        if(state){
            $('#indicatorConnected').addClass('bg-primary');
            $('#checkingConnection').removeClass('bg-danger');
            $('#indicatorActive').removeClass('d-none').addClass('d-flex');
            $('#indicatorInactive').addClass('d-none');
        }else{
            $('#indicatorWeight').html('0');
            $('#indicatorConnected').removeClass('bg-primary');
            $('#checkingConnection').addClass('bg-danger');
            $('#indicatorInactive').removeClass('d-none').addClass('d-flex');
            $('#indicatorActive').addClass('d-none');
        }
    }

    function buildMessage(action) {
        // Bootstrap values from PHP
        const deductions = {
            F1: <?= (int)$F1 ?>,
            F2: <?= (int)$F2 ?>,
            F3: <?= (int)$F3 ?>,
            F4: <?= (int)$F4 ?>,
            F5: <?= (int)$F5 ?>,
            F6: <?= (int)$F6 ?>,
            F7: <?= (int)$F7 ?>,
            F8: <?= (int)$F8 ?>,
            F9: <?= (int)$F9 ?>,
            F10: <?= (int)$F10 ?>,
            F11: <?= (int)$F11 ?>,
            F12: <?= (int)$F12 ?>
        };

        // Map F key → input name + format
        const mapping = {
            F1: { field: 'F1', sign: '-', suffix: '#' },
            F2: { field: 'F2', sign: '-', suffix: '#' },
            F3: { field: 'F3', sign: '-', suffix: '#' },
            F4: { field: 'F4', sign: '+', suffix: '#' },
            F5: { field: 'F5', sign: '+', suffix: '#' },
            F6: { field: 'F6', sign: '+', suffix: '#' },
            F7: { field: 'F7', sign: '-', suffix: '%' },
            F8: { field: 'F8', sign: '-', suffix: '%' },
            F9: { field: 'F9', sign: '-', suffix: '%' },
            F10: { field: 'F10', sign: '+', suffix: '%' },
            F11: { field: 'F11', sign: '+', suffix: '%' },
            F12: { field: 'F12', sign: '+', suffix: '%' }
        };

        const cfg = mapping[action];
        if (!cfg && action != 'ESC') {
            return null;
        }
        else if (action == 'ESC') {
            return "JQ-------$"; // Special case for ESC
        }

        const val = deductions[action] || 0;
        const padded = String(val).padStart(6, '0');
        return "JS" + cfg.sign + padded + cfg.suffix;
    }

    function buildMessageAuto(config) {
        if (!config) return null;

        // priority order (you can change if needed)
        let sign = null;
        let value = 0;
        let suffix = null;

        if (config.negativeKg > 0) { // KG deduction
            sign = '-';
            value = config.negativeKg;
            suffix = '#';
        }
        else if (config.positiveKg > 0) {
            sign = '+';
            value = config.positiveKg;
            suffix = '#';
        }
        else if (config.negativePerc > 0) { // Percentage deduction
            sign = '-';
            value = config.negativePerc;
            suffix = '%';
        }
        else if (config.positivePerc > 0) {
            sign = '+';
            value = config.positivePerc;
            suffix = '%';
        }
        else {
            return null; // nothing to send
        }

        // device requires 6 digit padding
        const padded = String(value).padStart(6, '0');

        return "JS" + sign + padded + suffix;
    }

    function postMessage(message) {
        $.ajax({
        url: 'http://127.0.0.1:5002/deduction',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ message: message }),
            success: function (res) {
                console.log("Sent:", message, "→", res);
            },
            error: function (xhr, status, err) {
                console.warn("Error posting:", message, status, err);
            }
        });
    }
    </script>
</body>
</html>