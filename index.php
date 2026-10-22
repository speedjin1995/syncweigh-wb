<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
if (!hasPermission('Weighing', ['view'])){
    header('Location: no-permission.php');
    exit;
}
?>

<?php
require_once 'php/classes/BaseController.php';
require_once 'php/requires/lookup.php';
$baseController = new BaseController($db);
$containerWeighingEnabled = $baseController->isFeatureEnabled('container_weighing');
$differentBinWeighingEnabled = $baseController->isFeatureEnabled('different_bin_weighing');
$transactionDispatchEnabled = $baseController->isFeatureEnabled('transaction_dispatch');
$transactionReceivingEnabled = $baseController->isFeatureEnabled('transaction_receiving');
$transactionInternalTransferEnabled = $baseController->isFeatureEnabled('transaction_internal_transfer');
$transactionMiscellaneousEnabled = $baseController->isFeatureEnabled('transaction_miscellaneous');
$defaultTransactionStatus = $transactionReceivingEnabled ? 'Purchase' : ($transactionDispatchEnabled ? 'Sales' : ($transactionInternalTransferEnabled ? 'Local' : 'Misc'));
$user = $_SESSION['id'];
$username = $_SESSION["username"];
$plantId = $_SESSION['plant'];
$locationId = $_SESSION['location_id'];
$stmt = $db->prepare("SELECT * from Location WHERE id = ?");
$stmt->bind_param('s', $locationId);
$stmt->execute();
$result = $stmt->get_result();
$port = 'COM5';
$baudrate = 9600;
$databits = "8";
$parity = "N";
$stopbits = '1';
$indicator = 'X722';

if(($row = $result->fetch_assoc()) !== null){
    $portId = $row['port_id'];
    $weighingCount = $row['weighing_count'];
}

$stmt2 = $db->prepare("SELECT * from Port WHERE id = ?");
$stmt2->bind_param('s', $portId);
$stmt2->execute();
$result2 = $stmt2->get_result();

if(($row2 = $result2->fetch_assoc()) !== null){
    $port = $row2['com_port'];
    $baudrate = $row2['bits_per_second'];
    $databits = $row2['data_bits'];
    $parity = $row2['parity'];
    $stopbits = $row2['stop_bits'];
    $indicator = $row2['indicator'];
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

$role = $_SESSION["roles"];

//$lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$product = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0' ORDER BY name ASC");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$contractor = $db->query("SELECT * FROM Contractor WHERE status = '0' ORDER BY name ASC");
$binNoList = $db->query("SELECT * FROM Bin_No WHERE status = '0' ORDER BY name ASC");
$binTypeList = $db->query("SELECT * FROM Bin_Type WHERE status = '0' ORDER BY name ASC");
$purchaseOrder = $db->query("SELECT * FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$salesOrder = $db->query("SELECT * FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$container = $db->query("SELECT * FROM Weight_Container WHERE status = '0' AND is_complete = 'Y' AND is_cancel = 'N'");

if(!hasPermission('Weighing', ['view_all_plants'])){
    // Only the plant picked on selectLocation.php
    $selectedPlantId = intval($_SESSION['selected_plant_id'] ?? 0);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and id = $selectedPlantId");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and id = $selectedPlantId");
    $location = $db->query("SELECT * FROM Location WHERE status = '0' and plant_id = $selectedPlantId ORDER BY location_name ASC");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $location = $db->query("SELECT * FROM Location WHERE status = '0' ORDER BY location_name ASC");
}
?>

<head>

    <title>Weighing | Synctronix - Weighing System</title>
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

    <?php include 'layouts/head-css.php'; ?>
    <style>
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .modal-header {
            padding: var(1rem, 1rem) !important;
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

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header fs-5 text-white" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch" style="background-color: #405189;">
                                        <i class="mdi mdi-chevron-down pull-right"></i>
                                        <?=$languageArray['search_records_code'][$language]?>
                                    </div>
                                    <div id="collapseSearch" class="collapse" aria-labelledby="collapseSearch">                                    
                                        <div class="card-body">
                                            <form action="javascript:void(0);">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="fromDateSearch" class="form-label"><?=$languageArray['from_date_code'][$language]?></label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="toDateSearch" class="form-label"><?=$languageArray['to_date_code'][$language]?></label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="statusSearch" class="form-label"><?=$languageArray['transaction_status_code'][$language]?></label>
                                                            <select id="statusSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php if($transactionDispatchEnabled && statusPermitted('Weighing', 'Sales')){ ?>
                                                                <option value="Sales"><?=$languageArray['dispatch_code'][$language]?></option>
                                                                <?php } ?>
                                                                <?php if($transactionReceivingEnabled && statusPermitted('Weighing', 'Purchase')){ ?>
                                                                <option value="Purchase"><?=$languageArray['receiving_code'][$language]?></option>
                                                                <?php } ?>
                                                                <?php if($transactionInternalTransferEnabled && statusPermitted('Weighing', 'Local')){ ?>
                                                                <option value="Local"><?=$languageArray['internal_transfer_code'][$language]?></option>
                                                                <?php } ?>
                                                                <?php if($transactionMiscellaneousEnabled && statusPermitted('Weighing', 'Misc')){ ?>
                                                                <option value="Misc"><?=$languageArray['miscellaneous_code'][$language]?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="customerSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label"><?=$languageArray['customer_code'][$language]?></label>
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
                                                            <label for="supplierSearch" class="form-label"><?=$languageArray['supplier_code'][$language]?></label>
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
                                                            <label for="vehicleNo" class="form-label"><?=$languageArray['vehicle_no_code'][$language]?></label>
                                                            <input type="text" class="form-control" placeholder="Vehicle No" id="vehicleNo">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invoiceNoSearch" class="form-label"><?=$languageArray['weighing_type_code'][$language]?></label>
                                                            <select id="invoiceNoSearch" class="form-select select2"  >
                                                                <option selected>-</option>
                                                                <?php if(weightTypePermitted('Normal')): ?>
                                                                <option value="Normal"><?=$languageArray['normal_weighing_code'][$language]?></option>
                                                                <?php endif; ?>
                                                                <?php if($containerWeighingEnabled && weightTypePermitted('Container')){ ?>
                                                                <option value="Container">Primer Mover</option>
                                                                <?php } ?>
                                                                <?php if(($containerWeighingEnabled || $differentBinWeighingEnabled) && weightTypePermitted('Empty Container')){ ?>
                                                                <option value="Empty Container">Primer Mover + Container</option>
                                                                <?php } ?>
                                                                <?php if($differentBinWeighingEnabled && weightTypePermitted('Different Container')){ ?>
                                                                <option value="Different Container">Primer Mover + Different Bins</option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label"><?=$languageArray['status_code'][$language]?></label>
                                                            <select id="batchNoSearch" class="form-select select2">
                                                                <option value="Pending" selected><?=$languageArray['pending_code'][$language]?></option>
                                                                <option value="Complete"><?=$languageArray['complete_code'][$language]?></option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->                                                
                                                    <div class="col-3" id="productSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="productSearch" class="form-label"><?=$languageArray['product_code'][$language]?></label>
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
                                                            <label for="rawMatSearch" class="form-label"><?=$languageArray['raw_material_code'][$language]?></label>
                                                            <select id="rawMatSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowRawMatF=mysqli_fetch_assoc($rawMaterial2)){ ?>
                                                                    <option value="<?=$rowRawMatF['raw_mat_code'] ?>"><?=$rowRawMatF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="plantSearchDisplay" style="display:none">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label"><?=$languageArray['plant_code'][$language]?></label>
                                                            <select id="plantSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="locationSearch" class="form-label"><?=$languageArray['location_code'][$language]?></label>
                                                            <select id="locationSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowLocationF=mysqli_fetch_assoc($location)){ ?>
                                                                    <option value="<?=$rowLocationF['id'] ?>"><?=$rowLocationF['location_name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="transactionIdSearch" class="form-label"><?=$languageArray['transaction_id_code'][$language]?></label>
                                                            <input type="text" class="form-control" id="transactionIdSearch" name="transactionIdSearch" placeholder="<?=$languageArray['transaction_id_code'][$language]?>">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="containerNoSearch" class="form-label"><?=$languageArray['container_no_code'][$language]?></label>
                                                            <input type="text" class="form-control" id="containerNoSearch" name="containerNoSearch" placeholder="<?=$languageArray['container_no_code'][$language]?>">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="sealNoSearch" class="form-label"><?=$languageArray['seal_no_code'][$language]?></label>
                                                            <input type="text" class="form-control" id="sealNoSearch" name="sealNoSearch" placeholder="<?=$languageArray['seal_no_code'][$language]?>">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invDelPoSearch" class="form-label"><?=$languageArray['inv_del_po_no_code'][$language]?></label>
                                                            <input type="text" class="form-control" id="invDelPoSearch" name="invDelPoSearch" placeholder="<?=$languageArray['inv_del_po_no_code'][$language]?>">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="button" class="btn btn-danger" id="clearAllSearch"><i class="bx bx-x"></i> <?=$languageArray['clear_all_code'][$language]?></button>
                                                            <button type="submit" class="btn btn-success" id="filterSearch"><i class="bx bx-search-alt"></i> <?=$languageArray['search_code'][$language]?></button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" style="background-color: #4CAF50;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-white text-truncate mb-0">
                                                        Dispatch
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span class="counter-value text-white" id="salesInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0" style="background-color:white;">
                                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                                        <i class="bx bx-dollar-circle text-success"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" style="background-color: #FFC107;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-white text-truncate mb-0">
                                                        Receiving
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span class="counter-value text-white" id="purchaseInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0" style="background-color:white;">
                                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                                        <i class="bx bx-shopping-bag text-info"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" style="background-color: #81D4FA;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-white text-truncate mb-0">
                                                        Internal Transfer
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span class="counter-value text-white" id="localInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0" style="background-color:white;">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-user-circle text-warning"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" style="background-color: #9C27B0;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-white text-uppercase fw-medium text-truncate mb-0">
                                                        Miscellaneous
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span class="counter-value text-white" id="miscInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0" style="background-color:white;">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-user-circle text-warning"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                
                                <div class="col-xl-3 col-md-6 add-new-weight">
                                    <!-- <button type="button" class="btn btn-lg btn-soft-success" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                            class="ri-add-circle-line align-middle me-1"></i>
                                        <?=$languageArray['add_new_code'][$language]?></button> -->

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle"><?=$languageArray['add_new_code'][$language]?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-primary">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h3 class="ff-secondary fw-semibold text-white"><?=$languageArray['indicator_weight_code'][$language]?></h3>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="indicatorWeight">0</span> Kg</h2>
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
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-primary">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h3 class="ff-secondary fw-semibold text-white"><?=$languageArray['final_weight_code'][$language]?></h3>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="currentWeight">0</span> Kg</h2>
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

                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">
                                                                                        <?=$languageArray['vehicle_plate_no_code'][$language]?>
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
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionId" class="col-sm-4 col-form-label"><?=$languageArray['transaction_id_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" placeholder="<?=$languageArray['transaction_id_code'][$language]?>" readonly>                                                                                  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionDate" class="col-sm-4 col-form-label"><?=$languageArray['transaction_date_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control input-readonly" data-provider="flatpickr" id="transactionDate" name="transactionDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label"><?=$languageArray['plant_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="plant" name="plant" required>
                                                                                            <?php while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                                                                                                <option value="<?=$rowPlant['name'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weightType" class="col-sm-4 col-form-label"><?=$languageArray['weight_type_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="weightType" name="weightType" class="form-select select2">
                                                                                            <?php if(weightTypePermitted('Normal')): ?>
                                                                                            <option value="Normal" selected><?=$languageArray['normal_weighing_code'][$language]?></option>
                                                                                            <?php endif; ?>
                                                                                            <?php if($containerWeighingEnabled && weightTypePermitted('Container')){ ?>
                                                                                            <option value="Container">Primer Mover</option>
                                                                                            <?php } ?>
                                                                                            <?php if(($containerWeighingEnabled || $differentBinWeighingEnabled) && weightTypePermitted('Empty Container')){ ?>
                                                                                            <option value="Empty Container">Primer Mover + Container</option>
                                                                                            <?php } ?>
                                                                                            <?php if($differentBinWeighingEnabled && weightTypePermitted('Different Container')){ ?>
                                                                                            <option value="Different Container">Primer Mover + Different Bins</option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label"><?=$languageArray['transaction_status_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                                            <?php if($transactionDispatchEnabled && statusPermitted('Weighing', 'Sales')){ ?>
                                                                                            <option value="Sales" <?=$defaultTransactionStatus == 'Sales' ? 'selected' : '' ?>><?=$languageArray['dispatch_code'][$language]?></option>
                                                                                            <?php } ?>
                                                                                            <?php if($transactionReceivingEnabled && statusPermitted('Weighing', 'Purchase')){ ?>
                                                                                            <option value="Purchase" <?=$defaultTransactionStatus == 'Purchase' ? 'selected' : '' ?>><?=$languageArray['receiving_code'][$language]?></option>
                                                                                            <?php } ?>
                                                                                            <?php if($transactionInternalTransferEnabled && statusPermitted('Weighing', 'Local')){ ?>
                                                                                            <option value="Local" <?=$defaultTransactionStatus == 'Local' ? 'selected' : '' ?>><?=$languageArray['internal_transfer_code'][$language]?></option>
                                                                                            <?php } ?>
                                                                                            <?php if($transactionMiscellaneousEnabled && statusPermitted('Weighing', 'Misc')){ ?>
                                                                                            <option value="Misc" <?=$defaultTransactionStatus == 'Misc' ? 'selected' : '' ?>><?=$languageArray['miscellaneous_code'][$language]?></option>
                                                                                            <?php } ?>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label"><?=$languageArray['customer_name_code'][$language]?></label>
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierName" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierName" class="col-sm-4 col-form-label"><?=$languageArray['supplier_name_code'][$language]?></label>
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="productNameDisplay">
                                                                                    <label for="productName" class="col-sm-4 col-form-label"><?=$languageArray['product_code_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="productName" name="productName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option 
                                                                                                    value="<?=$rowProduct['name'] ?>" 
                                                                                                    data-code="<?=$rowProduct['product_code'] ?>" 
                                                                                                    data-high="<?=$rowProduct['high'] ?>" 
                                                                                                    data-low="<?=$rowProduct['low'] ?>" 
                                                                                                    data-variance="<?=$rowProduct['variance'] ?>" 
                                                                                                    data-description="<?=$rowProduct['description'] ?>">
                                                                                                    <?=$rowProduct['product_code'] .' - '. $rowProduct['name']?>
                                                                                                </option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="rawMaterialDisplay" style="display:none;">
                                                                                    <label for="rawMaterialName" class="col-sm-4 col-form-label"><?=$languageArray['raw_material_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="rawMaterialName" name="rawMaterialName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                                                <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] .' - '. $rowRowMat['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>           
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="contractorId" class="col-sm-4 col-form-label"><?=$languageArray['contractor_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="contractorId" name="contractorId">
                                                                                            <option value="">-</option>
                                                                                            <?php while($rowContractor=mysqli_fetch_assoc($contractor)){ ?>
                                                                                                <option value="<?=$rowContractor['id'] ?>"><?=$rowContractor['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="containerDisplay">
                                                                                    <label for="containerNoInput" class="col-sm-4 col-form-label"><?=$languageArray['container_no1_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNoInput" name="containerNoInput" placeholder="<?=$languageArray['container_no_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="emptyContainerDisplay" style="display:none" >
                                                                                    <label for="emptyContainerNo" class="col-sm-4 col-form-label" id="containerNo1Label"><?=$languageArray['container_no1_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="emptyContainerNo" name="emptyContainerNo">
                                                                                            <option selected="-">-</option>
                                                                                            <?php /*while($rowContainer=mysqli_fetch_assoc($container)){ ?>
                                                                                                <option value="<?=$rowContainer['container_no'] ?>"><?=$rowContainer['container_no'] ?></option>
                                                                                            <?php }*/ ?>
                                                                                        </select>                   
                                                                                    </div>
                                                                                </div>
                                                                                <input type="text" class="form-control" id="containerNo" name="containerNo" hidden>
                                                                            </div>

                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2Display">
                                                                                <div class="row">
                                                                                    <label for="containerNo2" class="col-sm-4 col-form-label"><?=$languageArray['container_no2_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="containerNo2" name="containerNo2">
                                                                                            <option value="" selected>-</option>
                                                                                            <?php while($rowBinNo=mysqli_fetch_assoc($binNoList)){ ?>
                                                                                                <option value="<?=$rowBinNo['name'] ?>"><?=$rowBinNo['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2Display">
                                                                                <div class="row">
                                                                                    <label for="sealNo2" class="col-sm-4 col-form-label"><?=$languageArray['seal_no2_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="sealNo2" name="sealNo2">
                                                                                            <option value="" selected>-</option>
                                                                                            <?php while($rowBinType=mysqli_fetch_assoc($binTypeList)){ ?>
                                                                                                <option value="<?=$rowBinType['name'] ?>"><?=$rowBinType['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row" style="display:none;">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                                <div class="row">
                                                                                    <label for="orderWeight" class="col-sm-4 col-form-label"><?=$languageArray['order_weight_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="orderWeight" name="orderWeight"  placeholder="<?=$languageArray['order_weight_code'][$language]?>">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierWeight" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierWeight" class="col-sm-4 col-form-label"><?=$languageArray['supplier_weight_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="supplierWeight" name="supplierWeight"  placeholder="<?=$languageArray['supplier_weight_code'][$language]?>">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label"><?=$languageArray['weight_difference_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifferencePerc">
                                                                                <div class="row">
                                                                                    <label for="weightDifferencePerc" class="col-sm-4 col-form-label">% <?=$languageArray['variance_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifferencePerc" name="weightDifferencePerc" placeholder="Variance %" readonly>
                                                                                            <div class="input-group-text">%</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        
                                                                        <div class="row" style="display:none;">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label"><?=$languageArray['po_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="doDisplay">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label"><?=$languageArray['delivery_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="<?=$languageArray['delivery_no_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sealNoDisplay">
                                                                                <div class="row">
                                                                                    <label for="sealNo" class="col-sm-4 col-form-label"><?=$languageArray['seal_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="sealNo" name="sealNo" placeholder="<?=$languageArray['seal_no_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="display:none;">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label"><?=$languageArray['transporter_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="transporter" name="transporter" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label"><?=$languageArray['invoice_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="<?=$languageArray['invoice_no_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="display:none;">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="destination" class="col-sm-4 col-form-label"><?=$languageArray['destination_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="destination" name="destination" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                                <option value="<?=$rowDestination['name'] ?>" data-code="<?=$rowDestination['destination_code'] ?>"><?=$rowDestination['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>            
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label"><?=$languageArray['reduce_weight_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="replacementContainerDisplay" style="display:none">
                                                                                <div class="row">
                                                                                    <label for="replacementContainer" class="col-sm-4 col-form-label"><?=$languageArray['new_empty_bin_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="replacementContainer" name="replacementContainer" placeholder="Replacement Container" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12">
                                                                                <div class="row">
                                                                                    <label for="otherRemarks" class="col-sm-1 col-form-label" style="width: 11%;"><?=$languageArray['other_remarks_code'][$language]?></label>
                                                                                    <div class="col-sm-11" style="width: 89%;">
                                                                                        <textarea class="form-control" id="otherRemarks" name="otherRemarks" placeholder="<?=$languageArray['other_remarks_code'][$language]?>"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-12">
                                                            <div class="col-xxl-4 col-lg-4" id="normalCard">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3" id="manualWeightRow">
                                                                            <label for="manualWeight" class="col-sm-4 col-form-label"><?=$languageArray['manual_weight_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="form-check align-radio mr-2">
                                                                                    <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightYes" value="true">
                                                                                    <label class="form-check-label" for="manualWeightYes">
                                                                                        <?=$languageArray['yes_code'][$language]?>
                                                                                    </label>
                                                                                </div>

                                                                                <div class="form-check align-radio">
                                                                                    <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightNo" value="false" checked>
                                                                                    <label class="form-check-label" for="manualWeightNo">
                                                                                        <?=$languageArray['no_code'][$language]?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming" class="col-sm-4 col-form-label"><?=$languageArray['incoming_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manual" name="manual" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>-->
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label"><?=$languageArray['incoming_date_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing" class="col-sm-4 col-form-label"><?=$languageArray['outgoing_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualOutgoing" name="manualOutgoing" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>-->
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>                                                                                       
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate" class="col-sm-4 col-form-label"><?=$languageArray['outgoing_date_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate">
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight" class="col-sm-4 col-form-label"><?=$languageArray['nett_weight_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>                        
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4" id="containerCard" style="display:none;">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="vehiclePlateNo2" class="col-sm-4 col-form-label"><?=$languageArray['vehicle_plate_no_code'][$language]?>2</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualVehicle2" name="manualVehicle2" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>
                                                                                    <input type="text" class="form-control" id="vehicleNoTxt2" name="vehicleNoTxt2" placeholder="Vehicle Plate No" style="display:none">
                                                                                    <div class="col-10 index-vehicle2">
                                                                                        <select class="form-select select2" id="vehiclePlateNo2" name="vehiclePlateNo2">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowv2=mysqli_fetch_assoc($vehicles2)){ ?>
                                                                                                <option value="<?=$rowv2['veh_number'] ?>" data-weight="<?=$rowv2['vehicle_weight'] ?>"><?=$rowv2['veh_number'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="invalid-feedback">
                                                                                        Please fill in the field.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="vehicleWeight2Display" style="display:none">
                                                                            <label for="vehicleWeight2" class="col-sm-4 col-form-label"><?=$languageArray['vehicle_weight_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="vehicleWeight2" name="vehicleWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming2" class="col-sm-4 col-form-label"><?=$languageArray['incoming_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming2" name="grossIncoming2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate2" class="col-sm-4 col-form-label"><?=$languageArray['incoming_date_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="container2WeightDisplay" style="display:none">
                                                                            <label for="emptyContainerWeight2" class="col-sm-4 col-form-label"><?=$languageArray['empty_container_weight_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="emptyContainerWeight2" name="emptyContainerWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <div class="input-group-text">-</div>
                                                                                    <div class="input-group-text" id="replaceContainerText"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing2" class="col-sm-4 col-form-label"><?=$languageArray['outgoing_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing2" name="tareOutgoing2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="tareCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate2" class="col-sm-4 col-form-label"><?=$languageArray['outgoing_date_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2">
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight2" class="col-sm-4 col-form-label"><?=$languageArray['nett_weight_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight2" name="nettWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                                                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4" id="priceCard" style="display:none;">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="unitPrice" class="col-sm-4 col-form-label"><?=$languageArray['unit_price_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="unitPrice" name="unitPrice" step="0.01" min="0" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="sstRateDisplay" style="display:none;">
                                                                            <label for="sstRate" class="col-sm-4 col-form-label"><?=$languageArray['sst_rate_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="number" class="form-control" id="sstRate" name="sstRate" step="0.01" min="0" max="100" placeholder="0">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="sstDisplay" style="display:none;">
                                                                            <label for="sstPrice" class="col-sm-4 col-form-label"><?=$languageArray['sst_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="sstPrice" name="sstPrice" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="subTotalPriceDisplay" style="display:none;">
                                                                            <label for="subTotalPrice" class="col-sm-4 col-form-label"><?=$languageArray['sub_total_price_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="subTotalPrice" name="subTotalPrice" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="totalPriceDisplay">
                                                                            <label for="totalPrice" class="col-sm-4 col-form-label"><?=$languageArray['total_price_code'][$language]?></label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="totalPrice" name="totalPrice" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                                                 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                                <button type="button" class="btn btn-success" id="submitWeightPrint"><?=$languageArray['submit_print_code'][$language]?></button>
                                                                <button type="button" class="btn btn-primary" id="submitWeight"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->   

                                                        <!-- All Hidden Fields -->
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="customerType" class="col-sm-4 col-form-label">Customer Type</label>
                                                                <div class="col-sm-8">
                                                                    <select id="customerType" name="customerType" class="form-select select2">
                                                                        <option>Cash</option>
                                                                        <option selected>Normal</option>
                                                                    </select>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="poSupplyWeight" class="col-sm-4 col-form-label">P/O Supply Weight</label>
                                                                <div class="col-sm-8">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control input-readonly" id="poSupplyWeight" name="poSupplyWeight" placeholder="P/O Supply Weight" readonly>
                                                                        <div class="input-group-text">Kg</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="balance" class="col-sm-4 col-form-label">Balance</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control input-readonly text-danger" id="balance" name="balance" placeholder="0" readonly>   
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2" id="insufficientBalDisplay" style="display:none;">
                                                                <span class="col-sm-4"></span>
                                                                <label class="col-sm-8 text-danger">Insufficient Balance</label>
                                                            </div>
                                                        </div>   
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                <div class="col-sm-8">
                                                                    <select id="indicatorId" name="indicatorId" class="form-select select2" >
                                                                        <option selected>ind12345</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <input type="hidden" id="finalWeight" name="finalWeight">
                                                        <input type="hidden" id="customerCode" name="customerCode">
                                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                                        <input type="hidden" id="plantCode" name="plantCode">
                                                        <input type="hidden" id="status" name="status">
                                                        <input type="hidden" id="productCode" name="productCode">
                                                        <input type="hidden" id="productDescription" name="productDescription">
                                                        <input type="hidden" id="productHigh" name="productHigh">
                                                        <input type="hidden" id="productLow" name="productLow">
                                                        <input type="hidden" id="productVariance" name="productVariance">
                                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                                        <input type="hidden" id="rawMaterialCode" name="rawMaterialCode">
                                                        <input type="hidden" id="id" name="id">
                                                        <input type="hidden" id="overridePassword" name="overridePassword">
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                                        <input type="hidden" id="grossWeightBy1" name="grossWeightBy1">
                                                        <input type="hidden" id="tareWeightBy1" name="tareWeightBy1">
                                                        <input type="hidden" id="grossLocationId" name="grossLocationId">
                                                        <input type="hidden" id="tareLocationId" name="tareLocationId">
                                                        <input type="hidden" id="grossWeightBy2" name="grossWeightBy2">
                                                        <input type="hidden" id="tareWeightBy2" name="tareWeightBy2">
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="entryOverrideModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border border-2 border-danger shadow-lg">
                                                <form role="form" id="entryOverrideForm" autocomplete="off" onsubmit="return false;">
                                                    <div class="modal-header bg-danger p-3">
                                                        <h5 class="modal-title text-white"><i class="ri-lock-password-line align-middle me-2"></i><?=$languageArray['insufficient_entry_title_code'][$language]?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body pt-4">
                                                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                                            <i class="ri-error-warning-line fs-20 me-2"></i>
                                                            <div id="entryOverrideMessage" class="fw-semibold"></div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="entryOverridePassword" class="form-label"><?=$languageArray['password_code'][$language]?> *</label>
                                                            <div class="input-group has-validation">
                                                                <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                                                <input type="password" class="form-control" id="entryOverridePassword" autocomplete="new-password">
                                                                <div class="invalid-feedback" id="entryOverridePasswordError"><?=$languageArray['please_enter_your_password_code'][$language]?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                        <button type="button" class="btn btn-danger" id="submitEntryOverride"><?=$languageArray['confirm_code'][$language]?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="confirmCaptureModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body pt-4">
                                                    <div class="alert alert-info mb-0" role="alert">
                                                        <div id="confirmCaptureMessage" class="fw-semibold"></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                    <button type="button" class="btn btn-primary" id="submitCaptureConfirm"><?=$languageArray['confirm_code'][$language]?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="vehicleNotRegisteredModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border border-2 border-danger shadow-lg">
                                                <div class="modal-header bg-danger p-3">
                                                    <h5 class="modal-title text-white"><i class="ri-truck-line align-middle me-2"></i><?=$languageArray['vehicle_not_registered_title_code'][$language]?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pt-4">
                                                    <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                                                        <i class="ri-error-warning-line fs-20 me-2"></i>
                                                        <div id="vehicleNotRegisteredMessage" class="fw-semibold"></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                    <button type="button" class="btn btn-danger" id="goToVehicle"><?=$languageArray['go_to_vehicle_code'][$language]?></button>
                                                </div>
                                            </div>
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
                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header" style="background-color: #405189;">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0 text-white"><?=$languageArray['previous_records_code'][$language]?> (Lorry)</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <?php if(hasPermission('Weighing', ['export'])): ?>
                                                                <button type="button" id="exportPdf" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    <?=$languageArray['export_pdf_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    <?=$languageArray['export_excel_code'][$language]?>
                                                                </button>
                                                                <?php endif; ?>
                                                                <?php if (hasPermission('Weighing', ['cancelled'])) { ?>
                                                                <button type="button" id="multiDeleteLorry" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <?php } ?>
                                                                <?php if($transactionReceivingEnabled && statusPermitted('Weighing', 'Purchase', 'create')){ ?>
                                                                <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" data-transaction-status="Purchase" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['receiving_code'][$language]?>
                                                                </button>
                                                                <?php } ?>
                                                                <?php if($transactionDispatchEnabled && statusPermitted('Weighing', 'Sales', 'create')){ ?>
                                                                <button type="button" id="addDispatch" class="btn btn-info waves-effect waves-light" data-transaction-status="Sales" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['dispatch_code'][$language]?>
                                                                </button>
                                                                <?php } ?>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['location_code'][$language]?></th>
                                                                    <th><?=$languageArray['transaction_id_code'][$language]?></th>
                                                                    <!-- <th><?=$languageArray['weight_type_code'][$language]?></th> -->
                                                                    <th><?=$languageArray['weight_status_code'][$language]?></th>
                                                                    <th><?=$languageArray['customer_supplier_code'][$language]?></th>
                                                                    <th><?=$languageArray['container_no2_code'][$language]?></th>
                                                                    <th><?=$languageArray['seal_no2_code'][$language]?></th>
                                                                    <!-- <th><?=$languageArray['container_no_code'][$language]?></th> -->
                                                                    <!-- <th><?=$languageArray['seal_no_code'][$language]?></th> -->
                                                                    <th><?=$languageArray['vehicle_code'][$language]?></th>
                                                                    <th><?=$languageArray['gross_incoming_code'][$language]?></th>
                                                                    <th><?=$languageArray['incoming_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['duration_code'][$language]?></th>
                                                                    <th><?=$languageArray['tare_outgoing_code'][$language]?></th>
                                                                    <th><?=$languageArray['outgoing_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['nett_weight_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['gross_incoming_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['incoming_date_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['tare_outgoing_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['outgoing_date_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['nett_weight_code'][$language]?> 2</th>
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

                            <!-- Second Card for Empty Container -->
                            <div class="row" <?= ($containerWeighingEnabled || $differentBinWeighingEnabled) ? '' : 'style="display:none;"' ?>>
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header" style="background-color: #405189;">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0 text-white"><?=$languageArray['pending_empty_container_records_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <?php if (hasPermission('Weighing', ['cancelled'])) { ?>
                                                                <button type="button" id="multiDeleteContainer" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <?php } ?>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="emptyContainerTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllContainerCheckbox" class="selectAllContainerCheckbox"></th>
                                                                    <th><?=$languageArray['container_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['seal_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['weight_status_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_code'][$language]?></th>
                                                                    <th><?=$languageArray['gross_incoming_code'][$language]?></th>
                                                                    <th><?=$languageArray['incoming_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['tare_outgoing_code'][$language]?></th>
                                                                    <th><?=$languageArray['outgoing_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['nett_weight_code'][$language]?></th>
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
                            <button type="submit" class="btn btn-primary"><?=$languageArray['submit_code'][$language]?></button>
                        </div>
                    </form>
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
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
    var table = null;
    var emptyContainerTable = null;
    var containerColumnsEnabled = <?= json_encode($containerWeighingEnabled || $differentBinWeighingEnabled) ?>;
    let clickTimer = null;

    var fromDateSearchPicker;
    var toDateSearchPicker;
    var grossIncomingDatePicker;
    var canEditWeight = <?= json_encode(hasPermission('Weighing', ['edit'])) ?>;
    var canWeightOut = <?= json_encode(hasPermission('Weighing', ['weight_out'])) ?>;
    var canPrintWeight = <?= json_encode(hasPermission('Weighing', ['print'])) ?>;
    var manualWeighingStatuses = <?= json_encode(array_values(array_filter(array_keys(transactionStatusModules()), function ($s) { return statusPermitted('Weighing', $s, 'manual_weighing'); }))) ?>;
    var manualDateStatuses = <?= json_encode(array_values(array_filter(array_keys(transactionStatusModules()), function ($s) { return statusPermitted('Weighing', $s, 'manual_date_change'); }))) ?>;
    var manualPriceStatuses = <?= json_encode(array_values(array_filter(array_keys(transactionStatusModules()), function ($s) { return statusPermitted('Weighing', $s, 'manual_price'); }))) ?>;
    var keepUnitPrice = false; // true while editing, so the async deposit price lookup does not overwrite the saved price
    var tareOutgoingDatePicker; 
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2; 
    var userRole = '<?=$role ?>';
    var isSecondWeighingCapture = false;

    $(function () {
        var ind = '<?=$indicator ?>';
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        // Initialize all Select2 elements in the search bar
        $('#collapseSearch .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
        });

        // Apply custom styling to Select2 elements in search bar
        $('.select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('.select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

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

        //Date picker
        fromDateSearchPicker = $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        toDateSearchPicker = $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: '',
            allowInput: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
            clickOpens: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
        });

        grossIncomingDatePicker = $('#grossIncomingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
            clickOpens: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
        });

        tareOutgoingDatePicker = $('#tareOutgoingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
            clickOpens: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
        });

        grossIncomingDatePicker2 = $('#grossIncomingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
            clickOpens: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
        });

        tareOutgoingDatePicker2 = $('#tareOutgoingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
            clickOpens: (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') ? true : false,
        });

        // Clear All Filter Function
        $('#clearAllSearch').on('click', function(){
            fromDateSearchPicker.clear();
            toDateSearchPicker.clear();
            $('#statusSearch').val('-').trigger('change');
            $('#customerNoSearch').val('-').trigger('change');
            $('#supplierSearch').val('-').trigger('change');
            $('#vehicleNo').val('');
            $('#invoiceNoSearch').val('-').trigger('change');
            $('#batchNoSearch').val('Pending').trigger('change');
            $('#productSearch').val('-').trigger('change');
            $('#rawMatSearch').val('-').trigger('change');
            $('#plantSearch').val('-').trigger('change');
            $('#locationSearch').val('-').trigger('change');
            $('#transactionIdSearch').val('');
            $('#containerNoSearch').val('');
            $('#sealNoSearch').val('');
            $('#invDelPoSearch').val('');
        });

        if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
            $('#plantSearchDisplay').show();
        }else{
            $('#plantSearchDisplay').hide();
        }

        $('#statusSearch').on('change', function(){
            var status = $(this).val();

            if (status == 'Purchase' || status == 'Local'){
                // Hide & reset customer then show supplier
                $('#customerSearchDisplay').hide();
                $('#customerSearchDisplay').find('#customerNoSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').show();
                // Hide & reset product then show raw material
                $('#productSearchDisplay').find('#productSearch').val('-').trigger('change');
                $('#productSearchDisplay').hide();
                $('#rawMatSearchDisplay').show();
            }else{
                // Hide & reset supplier then show customer
                $('#supplierSearchDisplay').find('#supplierSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').hide();
                $('#customerSearchDisplay').show();
                // Hide & reset raw material then show product
                $('#rawMatSearchDisplay').find('#rawMatSearch').val('-').trigger('change');
                $('#rawMatSearchDisplay').hide();
                $('#productSearchDisplay').show();
            }
        });

        // $('#statusSearch').val('Sales').trigger('change');

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        $('#selectAllContainerCheckbox').on('change', function() {
            var checkboxes = $('#emptyContainerTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        renderTable();

        $('#filterSearch').on('click', function(){
            renderTable();
        });

        // Add event listener for opening and closing details on row click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this); // The row that was clicked
            var row = table.row(tr);
            if (!row.data()) return; // <-- Exit early if row data is not available

            // Exclude specific td elements by checking the event target
            if ($(e.target).closest('td').hasClass('select-checkbox') || $(e.target).closest('td').hasClass('action-button') || row.data().weight_type =='Primer Mover + Container') {
                return;
            }

            // Clear any previous timer if have
            if (clickTimer) {
                clearTimeout(clickTimer);
                clickTimer = null;
            }

            // Delay to detect double-click
            clickTimer = setTimeout(function () {
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    $.post('php/modules/weight/index.php', { action: 'get', userID: row.data().id, format: 'EXPANDABLE' }, function (data) {
                        var obj = JSON.parse(data);
                        if (obj.status === 'success') {
                            row.child(format(obj.message)).show();
                            tr.addClass("shown");
                        }
                    });
                }

                clickTimer = null; // Reset after execution
            }, 250); // Delay to distinguish from double-click
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
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
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

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                // Check if the value is empty
                if (select2Field.val() === "" || select2Field.val() === null) {
                    select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    isValid = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                }
            });

            if(pass && $('#weightForm').valid()){
                checkVehicleEntry(function(){
                $('#spinnerLoading').show();
                $.post('php/modules/weight/index.php?action=save', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['weight'])){
                                echo "window.location = 'index.php';";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = 'index.php';
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
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
                });
            }
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
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
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

            if(pass && $('#weightForm').valid()){
                checkVehicleEntry(function(){
                    $('#spinnerLoading').show();
                    $.post('php/modules/weight/index.php?action=save', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                            print(obj.id, null, isEmptyContainer);
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
                });
            }
        });

        $('#submitEntryOverride').on('click', function(){
            var password = $('#entryOverridePassword').val();

            if (!password) {
                showEntryOverrideError(<?=json_encode($languageArray['please_enter_your_password_code'][$language])?>);
                return;
            }

            // Check the password here so a wrong one keeps the modal open (weight.php checks it again on save)
            var confirmBtn = $(this).prop('disabled', true);
            $.post('php/modules/vehicle/index.php', {action: 'checkEntry', verifyPassword: password}, function(data){
                var obj = JSON.parse(data);
                confirmBtn.prop('disabled', false);

                if (obj.status === 'success' && obj.valid) {
                    $('#entryOverridePassword').removeClass('is-invalid');
                    $('#weightForm').find('#overridePassword').val(password);
                    $('#entryOverrideModal').modal('hide');

                    if (entryOverrideProceed) {
                        var proceed = entryOverrideProceed;
                        entryOverrideProceed = null;
                        proceed();
                    }
                }
                else {
                    showEntryOverrideError(<?=json_encode($languageArray['invalid_password_code'][$language])?>);
                }
            }).fail(function(){
                confirmBtn.prop('disabled', false);
            });
        });

        $('#entryOverridePassword').on('input', function(){
            $(this).removeClass('is-invalid');
        });

        // Stack the password modal (and a darker backdrop) above the open Add New modal
        $('#entryOverrideModal').on('show.bs.modal', function(){
            $(this).css('z-index', 1065);

            // The backdrop is added right after this event
            setTimeout(function(){
                $('.modal-backdrop').last().css({'z-index': 1060, 'opacity': 0.7, 'backdrop-filter': 'blur(4px)', '-webkit-backdrop-filter': 'blur(4px)'});
            }, 0);
        });

        $('#entryOverrideModal').on('shown.bs.modal', function(){
            $('#entryOverridePassword').trigger('focus');
        });

        $('#entryOverrideModal').on('hidden.bs.modal', function(){
            // Bootstrap removes the stacked backdrop; keep scrolling locked for the Add New modal still open
            if ($('#addModal').hasClass('show')) {
                $('body').addClass('modal-open');
            }
        });

        $('#entryOverridePassword').on('keypress', function(e){
            if (e.which == 13) {
                $('#submitEntryOverride').click();
            }
        });

        $('#submitCancel').on('click', function(){
            if($('#cancelForm').valid()){
                $('#spinnerLoading').show();
                var id = $('#cancelModal').find('#id').val();
                $.post('php/modules/weight/index.php', $('#cancelForm').serialize() + '&action=delete', function(data){
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

        setInterval(function () {
            $.post('http://127.0.0.1:5002/handshaking', function(data){
                if(data != "Error"){
                    console.log("Data Received:" + data);
                    
                    if(ind == 'X2S' || ind == 'X722'){
                        if(data.includes("GS")){
                            var text = data.split(" ");
                            var text2 = text[text.length - 1];
                            text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(text2);
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'BDI'){
                        if(data.includes("GS") || data.includes("NT") || data.includes("ST") || data.includes("US")){
                            var text = data.split(" ");
                            var text2 = text[text.length - 1];
                            text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(text2);
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'EX2001'){
                        data = data.replace("kg", "").replace("KG", "").replace("Kg", "").replace("g", "");
                        if(data != null && data != ''){
                            var text = data.split(",");
                            var text2 = text[text.length - 1];
                            //text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(parseInt(text2.replaceAll(",", "").trim()).toString());
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'D2008'){
                        if(data.includes("GS")){
                            var text = data.split(",");
                            var text2 = text[text.length - 1];
                            text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(parseInt(text2).toString());
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'BX23'){
                        // Remove control characters
                        data = data.replace(/[^\x20-\x7E]/g, '').trim();
                        var text = data.split(/\s+/);
                        var weight = "";
                        
                        if (text.length == 2 && /^[iqy]/i.test(text[0])) { // Format 1: i00103250 0
                            var match = text[0].match(/\d+/);
                        
                            if (match) {
                                weight = parseInt(match[0]).toString();
                            }
                        }
                        else if (text.length >= 3 && /^[iqy]/i.test(text[0])) { // Format 2: i0 093250 0
                            var part1 = text[0].replace(/[^\d]/g, "");
                            var part2 = text[1];
                        
                            if (part2.length > 0) {
                                weight = parseInt(part2).toString();
                            }
                        }
                        
                        // Only update if we have a valid weight
                        if (weight !== "" && !isNaN(weight)) {
                            $('#indicatorWeight').html(weight);
                        }
                        
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
                        $('#indicatorActive').removeClass('d-none').addClass('d-flex');
                        $('#indicatorInactive').addClass('d-none');
                    }
                    else if(ind == '205'){
                        var text = data.split(" ");
                        var newArray = text.slice(1, -1);
                        var newtext = newArray.join();
                        $('#indicatorWeight').html(newtext.replaceAll(",", "").trim());
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
                        $('#indicatorActive').removeClass('d-none').addClass('d-flex');
                        $('#indicatorInactive').addClass('d-none');
                    }
                    else if(ind == 'IND246'){
                        var text = data.replace(/[\x00-\x1F\x7F]/g, '');
                        var newtext = text.replace(/\s+/g, ' ').trim();
                    
                        console.log('CLEAN:', newtext);
                    
                        const match = newtext.match(/^\S+\s+(\d+)\s+\d+$/);
                        console.log("MATCH", match);
                    
                        if (match) {
                            const weight = match[1];
                    
                            $('#indicatorWeight').html(weight);
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                            $('#indicatorActive').removeClass('d-none').addClass('d-flex');
                            $('#indicatorInactive').addClass('d-none');
                        }
                    }
                }
                else{
                    $('#indicatorWeight').html('0');
                    $('#indicatorConnected').removeClass('bg-primary');
                    $('#checkingConnection').addClass('bg-danger');
                }
            });
        }, 250);

        $('#addWeight, #addDispatch').on('click', function(){
            // Show Capture Buttons When Add New
            $('#addModal').find('#grossCapture').show();
            $('#addModal').find('#tareCapture').show();
            $('#addModal').find('#id').val("");
            $('#addModal').find('#currentWeight').text("0");
            $('#addModal').find('#transactionId').val("");
            $('#addModal').find('#transactionStatus').val($(this).data('transactionStatus')).trigger('change');
            $('#addModal').find('#emptyContainerNo').val("").trigger('change');
            $('#addModal').find('#weightType option.feature-hidden').remove();
            $('#addModal').find('#weightType').val("Normal").trigger('change');
            $('#addModal').find('#customerType').val("Normal").trigger('change');
            $('#addModal').find('#transactionDate').val(formatDate2(today));
            $('#addModal').find('#vehiclePlateNo1').val("").trigger('change');
            $('#addModal').find('#vehiclePlateNo2').val("").trigger('change');
            $('#addModal').find('#supplierWeight').val("");
            $('#addModal').find('#customerCode').val("");
            $('#addModal').find('#customerName').val("-").trigger('change');
            $('#addModal').find('#supplierCode').val("");
            $('#addModal').find('#supplierName').val("-").trigger('change');
            $('#addModal').find('#productCode').val("");
            $('#addModal').find('#productName').val("-").trigger('change');
            $('#addModal').find('#rawMaterialCode').val("");
            $('#addModal').find('#contractorId').val("").trigger('change');
            $('#addModal').find('#rawMaterialName').val("-").trigger('change');
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#sealNo').val("");
            $('#addModal').find('#invoiceNo').val("");
            $('#addModal').find('#purchaseOrder').val("").trigger('change');
            $('#addModal').find('#salesOrder').val("").trigger('change');
            $('#addModal').find('#deliveryNo').val("");
            $('#addModal').find('#transporterCode').val("");
            $('#addModal').find('#transporter').val("-").trigger('change');
            $('#addModal').find('#destinationCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#destination').val("-").trigger('change');
            $('#addModal').find('#replacementContainer').val('').trigger('keyup');
            $('#addModal').find('#otherRemarks').val("");
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val("");
            $('#addModal').find('#grossLocationId, #tareLocationId').val('');
            grossIncomingDatePicker.clear();
            $('#addModal').find('#tareOutgoing').val("");
            tareOutgoingDatePicker.clear();
            $('#addModal').find('#nettWeight').val("");
            $('#addModal').find('#vehicleWeight2').val("");
            $('#addModal').find('#emptyContainerWeight2').val("");
            $('#addModal').find('#grossIncoming2').val("");
            $('#addModal').find('#status').val("");
            grossIncomingDatePicker2.clear();
            $('#addModal').find('#tareOutgoing2').val("");
            tareOutgoingDatePicker2.clear();
            $('#addModal').find('#nettWeight2').val("");
            $('#addModal').find('#reduceWeight').val("");
            $('#addModal').find('#weightDifference').val("");
            $('#addModal').find('#weightDifferencePerc').val("");
            $('#addModal').find('#manualWeightNo').trigger('click');
            $('#addModal').find('#weighbridge').val("");
            $('#addModal').find('#productDescription').val("");
            $('#addModal').find('#productHigh').val("");
            $('#addModal').find('#productLow').val("");
            $('#addModal').find('#productVariance').val("");
            $('#addModal').find('#orderWeight').val("0");
            $('#addModal').find('#unitPrice').val("0.00");
            keepUnitPrice = false;
            $('#addModal').find('#sstRate').val("");
            $('#addModal').find('#subTotalPrice').val("0.00");
            $('#addModal').find('#sstPrice').val("0.00");
            $('#addModal').find('#totalPrice').val("0.00");
            $('#addModal').find('#finalWeight').val("");
            $('#addModal').find('#balance').val("");
            $('#addModal').find('#insufficientBalDisplay').hide();
            $('#addModal').find('#containerNoInput').val("");
            $('#addModal').find('#containerNo').val("");
            $('#addModal').find('#containerNo2').val("").trigger('change');
            $('#addModal').find('#sealNo2').val("").trigger('change');

            // Show select and hide input readonly
            $('#addModal').find('#salesOrderEdit').val("").hide();
            $('#addModal').find('#purchaseOrderEdit').val("").hide();
            $('#addModal').find('#salesOrder').next('.select2-container').show();

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
            var locationI = $('#locationSearch').val() ? $('#locationSearch').val() : '';
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
                    source : 'weighing',
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
                    location : locationI,
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
                    source : 'weighing',
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
                    location : locationI,
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
            var locationI = $('#locationSearch').val() ? $('#locationSearch').val() : '';
            var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
            var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
            var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';
            var invDelPoI = $('#invDelPoSearch').val() ? $('#invDelPoSearch').val() : '';
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
                window.open("php/export.php?source=weighing&file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+invoiceNoI+"&product="+productSearchI+"&rawMat="+rawMaterialI+"&plant="+plantNoI+"&location="+locationI+"&status="+batchNoI+
                "&transactionId="+encodeURIComponent(transactionIdI)+"&containerNo="+encodeURIComponent(containerNoI)+
                "&sealNo="+encodeURIComponent(sealNoI)+"&invDelPo="+encodeURIComponent(invDelPoI)+"&isMulti=Y&ids="+selectedIds);
            }else{
                window.open("php/export.php?source=weighing&file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+invoiceNoI+"&product="+productSearchI+"&rawMat="+rawMaterialI+"&plant="+plantNoI+"&location="+locationI+"&status="+batchNoI+
                "&transactionId="+encodeURIComponent(transactionIdI)+"&containerNo="+encodeURIComponent(containerNoI)+
                "&sealNo="+encodeURIComponent(sealNoI)+"&invDelPo="+encodeURIComponent(invDelPoI)+"&isMulti=N");
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
                if (confirm('Are you sure you want to cancel these weighing records?')) {
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
                if (confirm('Are you sure you want to cancel these weighing records?')) {
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
                $.post('php/modules/weight/index.php', {action: 'containers', userID: transaType}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#addModal').find('#emptyContainerNo').empty();
                            $('#addModal').find('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#addModal').find('#emptyContainerNo').append(
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
                $('#addModal').find('#containerNo1Label').text("Container No 1");
                $('#addModal').find('#emptyContainerDisplay').show();
                $('#addModal').find('#replacementContainerDisplay').hide();
                $('#addModal').find('#vehicleWeight2Display').hide();
                $('#addModal').find('#container2WeightDisplay').hide();
                $('#addModal').find('#containerNo2Display').show();
                $('#addModal').find('#containerNo2ReplaceDisplay').hide();
                $('#addModal').find('#sealNoDisplay').show();
                $('#addModal').find('#sealNoReplaceDisplay').hide();
                $('#addModal').find('#sealNo2Display').show();
                $('#addModal').find('#sealNo2ReplaceDisplay').hide();
                $('#addModal').find('#containerDisplay').hide();
                $('#addModal').find('#containerNoInput').attr('required', false);
                $('#addModal').find('#emptyContainerNo').attr('required', true);
            }else if (weightType == 'Empty Container'){
                handleWeightType(weightType);
                $('#addModal').find('#containerNo1Label').text("Container No 1");
                $('#addModal').find('#emptyContainerDisplay').hide();
                $('#addModal').find('#replacementContainerDisplay').hide();
                $('#addModal').find('#vehicleWeight2Display').hide();
                $('#addModal').find('#container2WeightDisplay').hide();
                $('#addModal').find('#containerNo2Display').show();
                $('#addModal').find('#containerNo2ReplaceDisplay').hide();
                $('#addModal').find('#sealNoDisplay').show();
                $('#addModal').find('#sealNoReplaceDisplay').hide();
                $('#addModal').find('#sealNo2Display').show();
                $('#addModal').find('#sealNo2ReplaceDisplay').hide();
                $('#addModal').find('#containerDisplay').show();
                $('#addModal').find('#containerNoInput').attr('required', true);
                $('#addModal').find('#emptyContainerNo').attr('required', false);
            }else if (weightType == 'Different Container') {
                $.post('php/modules/weight/index.php', {action: 'containers', userID: transaType}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#addModal').find('#emptyContainerNo').empty();
                            $('#addModal').find('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#addModal').find('#emptyContainerNo').append(
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
                $('#addModal').find('#containerNo1Label').text("<?= $languageArray['pending_bin_code'][$language] ?>");
                $('#addModal').find('#emptyContainerDisplay').show();
                $('#addModal').find('#replacementContainerDisplay').show();
                $('#addModal').find('#vehicleWeight2Display').show();
                $('#addModal').find('#container2WeightDisplay').show();
                $('#addModal').find('#containerNo2Display').hide();
                $('#addModal').find('#containerNo2ReplaceDisplay').show();
                $('#addModal').find('#sealNoDisplay').hide();
                $('#addModal').find('#sealNoReplaceDisplay').show();
                $('#addModal').find('#sealNo2Display').hide();
                $('#addModal').find('#sealNo2ReplaceDisplay').show();
                $('#addModal').find('#containerDisplay').hide();
                $('#addModal').find('#containerNoInput').attr('required', false);
                $('#addModal').find('#emptyContainerNo').attr('required', true);
            }else{
                handleWeightType(weightType);
                $('#addModal').find('#containerNo1Label').text("Container No 1");
                $('#addModal').find('#emptyContainerDisplay').hide();
                $('#addModal').find('#replacementContainerDisplay').hide();
                $('#addModal').find('#vehicleWeight2Display').hide();
                $('#addModal').find('#container2WeightDisplay').hide();
                $('#addModal').find('#containerNo2Display').show();
                $('#addModal').find('#containerNo2ReplaceDisplay').hide();
                $('#addModal').find('#sealNoDisplay').show();
                $('#addModal').find('#sealNoReplaceDisplay').hide();
                $('#addModal').find('#sealNo2Display').show();
                $('#addModal').find('#sealNo2ReplaceDisplay').hide();
                $('#addModal').find('#containerDisplay').show();
                $('#addModal').find('#containerNoInput').attr('required', false);
                $('#addModal').find('#emptyContainerNo').attr('required', false);
            }
        });

        $('#replacementContainer').on('keyup', function(){
            var replacementContainer = $(this).val();
            $('#replaceContainerText').text(replacementContainer);
        });

        /*$('#customerType').on('change', function(){
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            if (transactionStatus == 'Purchase'){
                $('#unitPriceDisplay').hide();
                $('#subTotalPriceDisplay').hide();
                $('#sstDisplay').hide();
                $('#totalPriceDisplay').hide();
            }else{
                if($(this).val() == "Cash")
                {
                    $('#unitPriceDisplay').show();
                    $('#subTotalPriceDisplay').show();
                    $('#sstDisplay').show();
                    $('#totalPriceDisplay').show();
                }
                else
                {
                    $('#unitPriceDisplay').hide();
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                }
            }
        });*/

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

        $('#vehiclePlateNo1').on('change', function(){
            vehicleChanged($(this).val());
        });

        $('#vehicleNoTxt').on('keyup', function(){
            var x = $('#vehicleNoTxt').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt').val(x);
        });

        $('#vehicleNoTxt').on('change', function(){
            var x = $(this).val().toUpperCase();
            $(this).val(x);
            vehicleChanged(x);
        });

        // Stack the not-registered popup above the open Add New modal, with a darker blurred backdrop
        $('#vehicleNotRegisteredModal').on('show.bs.modal', function(){
            $(this).css('z-index', 1065);

            // The backdrop is added right after this event
            setTimeout(function(){
                $('.modal-backdrop').last().css({'z-index': 1060, 'opacity': 0.7, 'backdrop-filter': 'blur(4px)', '-webkit-backdrop-filter': 'blur(4px)'});
            }, 0);
        });

        // Closing the popup clears the vehicle that was not found
        $('#vehicleNotRegisteredModal').on('hidden.bs.modal', function(){
            if ($('#manualVehicle').is(':checked')){
                $('#vehicleNoTxt').val('');
            }else{
                $('#vehiclePlateNo1').val('-').trigger('change');
            }
        });

        $('#goToVehicle').on('click', function(){
            window.location.href = 'vehicle.php';
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
                $.post('php/modules/vehicle/index.php', {action: 'get', userID: x, type: 'pullCustomer'}, function (data){
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
                $.post('php/modules/vehicle/index.php', {action: 'get', userID: vehiclePlateNo2, type: 'pullCustomer'}, function (data){
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
            toggleSubmitButtons();
        });

        $('#grossIncoming').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');
            $('#grossWeightBy1').val('<?php echo $username; ?>');
            $('#grossLocationId').val('<?= (int) $locationId ?>'); // gate at gross capture

            // Update the Flatpickr instance
            grossIncomingDatePicker.setDate(new Date()); // sets it to current date/time
            $('#grossIncomingDate').trigger('change');
        });

        $('#grossCapture').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
            $('#confirmCaptureMessage').text('<?=$languageArray['confirm_first_weighing_code'][$language]?>: ' + $('#grossIncoming').val() + ' kg');
            isSecondWeighingCapture = false;
            $('#confirmCaptureModal').modal('show');
        });

        $('#confirmCaptureModal').on('show.bs.modal', function(){
            $(this).css('z-index', 1065);

            // The backdrop is added right after this event
            setTimeout(function(){
                $('.modal-backdrop').last().css({'z-index': 1060, 'opacity': 0.7, 'backdrop-filter': 'blur(4px)', '-webkit-backdrop-filter': 'blur(4px)'});
            }, 0);
        });

        $('#submitCaptureConfirm').on('click', function(){
            $('#confirmCaptureModal').modal('hide');
            $(isSecondWeighingCapture ? '#submitWeightPrint' : '#submitWeight').trigger('click');
        });

        $('#tareOutgoing').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');
            $('#tareWeightBy1').val('<?php echo $username; ?>');
            $('#tareLocationId').val('<?= (int) $locationId ?>'); // gate at tare capture

            // Update the Flatpickr instance
            tareOutgoingDatePicker.setDate(new Date()); // sets it to current date/time
            $('#tareOutgoingDate').trigger('change');

        });

        $('#tareCapture').on('click', function(event){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');

            // Tare capture completes the weighing and charges the deposit, so check the balance against the total price
            var transactionStatus = $('#transactionStatus').val();
            var partyId = transactionStatus == 'Sales' ? $('#customerName :selected').data('id') : $('#supplierName :selected').data('id');
            if ((transactionStatus != 'Sales' && transactionStatus != 'Purchase') || !partyId) {
                showTareConfirm();
                return;
            }

            var total = parseFloat($('#totalPrice').val()) || 0;
            $.post('php/modules/deposit/index.php', {action: 'check', partyType: transactionStatus == 'Sales' ? 'CUSTOMER' : 'SUPPLIER', partyId: partyId, itemCode: ''}, function(data){
                var obj = JSON.parse(data);
                if (obj.status === 'success' && obj.enabled && total > parseFloat(obj.balance)) {
                    alert(<?=json_encode($languageArray['insufficient_deposit_code'][$language])?>);
                    return;
                }
                showTareConfirm();
            });
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
                nett2 = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                nett2 = parseFloat($('#addModal').find('#orderWeight').val());
            }
            
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));

            // Processing for variance %
            var variancePercent = (current / parseFloat($(this).val())) * 100;
            $('#weightDifferencePerc').val(variancePercent.toFixed(2));

        });

        $('#orderWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));

            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#addModal').find('#insufficientBalDisplay').hide();
                } else {
                    $('#addModal').find('#insufficientBalDisplay').show();
                }
            }

            // Processing for variance %
            var variancePercent = (current / parseFloat($(this).val())) * 100;
            $('#weightDifferencePerc').val(variancePercent.toFixed(2));
        });

        $('#supplierWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
            
            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#addModal').find('#insufficientBalDisplay').hide();
                } else {
                    $('#addModal').find('#insufficientBalDisplay').show();
                }
            }

            // Processing for variance %
            var variancePercent = (current / parseFloat($(this).val())) * 100;
            $('#weightDifferencePerc').val(variancePercent.toFixed(2));
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
                var nett = Math.abs(tare2 - vehicleWeight2 - emptyContainer1);

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
            calculatePrice();
        });

        $('#transactionStatus').on('change', function(){
            $('#priceCard').toggle($(this).val() == 'Sales' || $(this).val() == 'Purchase');
            applyManualPermissions();
            var customerType = $('#addModal').find('#customerType').val();
            var weightType = $('#addModal').find('#weightType').val();

            if(weightType == 'Container'){
                $.post('php/modules/weight/index.php', {action: 'containers', userID: $(this).val()}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            $('#addModal').find('#emptyContainerNo').empty();
                            $('#addModal').find('#emptyContainerNo').append(`<option selected="-">-</option>`);

                            var deliveredTransporter;

                            for (var i = 0; i < obj.message.length; i++) {
                                var id = obj.message[i].id;
                                var container_no = obj.message[i].container_no;

                                $('#addModal').find('#emptyContainerNo').append(
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
                $('#addModal').find('#orderWeight').val("");
                $('#addModal').find('#supplierWeight').val("0");
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
                $('#rawMaterialDisplay').show();
                $('#productNameDisplay').hide();
                $('#addModal').find('#divPoSupplyWeight').show();
                
                if ($(this).val() == "Purchase"){
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                }else{
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                }
            }
            else{
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#addModal').find('#divPoSupplyWeight').hide();
            }
        });

        //productName
        $('#productName').on('change', function(){
            $('#productCode').val($('#productName :selected').data('code'));
            $('#productDescription').val($('#productName :selected').data('description'));
            $('#productHigh').val($('#productName :selected').data('high'));
            $('#productLow').val($('#productName :selected').data('low'));
            $('#productVariance').val($('#productName :selected').data('variance'));

            loadDepositPrice();
        });

        $('#sstRate, #unitPrice').on('keyup change', calculatePrice);
        $('#customerName, #supplierName, #productName, #rawMaterialName').on('change', function(){ keepUnitPrice = false; });

        //supplierName
        $('#supplierName').on('change', function(){
            $('#supplierCode').val($('#supplierName :selected').data('code'));
            loadDepositPrice();
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

        //customerName
        $('#customerName').on('change', function(){
            $('#customerCode').val($('#customerName :selected').data('code'));
            loadDepositPrice();
        });

        //rawMaterialName
        $('#rawMaterialName').on('change', function(){
            $('#rawMaterialCode').val($('#rawMaterialName :selected').data('code'));
            loadDepositPrice();
        });

        //Empty Container No
        $('#emptyContainerNo').on('change', function (){
            var emptyContainerNo = $(this).val();
            var weightType = $('#weightType').val();
            $('#containerNo').val(emptyContainerNo);

            if (emptyContainerNo == '-'){
                $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
                $('#addModal').find('#grossIncoming').val(0);
                $('#addModal').find('#grossIncomingDate').val("");
                $('#addModal').find('#tareOutgoing').val(0);
                $('#addModal').find('#tareOutgoingDate').val("");
                $('#addModal').find('#nettWeight').val(0);
                $('#tareOutgoing2').trigger('keyup');
                $('#normalCard').hide();
                $('#grossCapture').show();
                $('#tareCapture').show();
                toggleSubmitButtons();
            } else if (emptyContainerNo) { 
                $.post('php/modules/weight/index.php', {action: 'emptyContainer', userID: emptyContainerNo}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){ 
                        $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                        $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                        $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                        $('#addModal').find('#sealNo').val(obj.message.seal_no);

                        if (weightType != 'Different Container'){
                            if (obj.message.container_no2 && !$('#containerNo2 option[value="' + obj.message.container_no2 + '"]').length) { $('#containerNo2').append(new Option(obj.message.container_no2, obj.message.container_no2)); }
                            if (obj.message.seal_no2 && !$('#sealNo2 option[value="' + obj.message.seal_no2 + '"]').length) { $('#sealNo2').append(new Option(obj.message.seal_no2, obj.message.seal_no2)); }
                            $('#addModal').find('#containerNo2').val(obj.message.container_no2).trigger('change');
                            $('#addModal').find('#sealNo2').val(obj.message.seal_no2).trigger('change');
                        }

                        if (obj.message.transaction_status == 'Sales' || obj.message.transaction_status == 'Misc'){
                            $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                            $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                        }else{
                            $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change');
                            $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                        }
                        $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                        $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                        $('#addModal').find('#destination').val(obj.message.destination).trigger('change');

                        
                        $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).select2('destroy').select2();
                        $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                        grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date)); 
                        // $('#addModal').find('#grossIncomingDate').val(obj.message.gross_weight1_date);
                        $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                        tareOutgoingDatePicker.setDate(new Date(obj.message.tare_weight1_date));
                        // $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date);
                        $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
                        $('#addModal').find('#grossLocationId').val(obj.message.location_id || '');
                        $('#addModal').find('#tareLocationId').val(obj.message.location_id2 || '');
                        $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);

                        if(obj.message.vehicleNoTxt != null){
                            $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                            $('#manualVehicle').val(1);
                            $('#manualVehicle').prop("checked", true);
                            $('.index-vehicle').hide();
                            $('#vehicleNoTxt').show();
                        }
                        else{
                            $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).select2('destroy').select2();
                            $('#manualVehicle').val(0);
                            $('#manualVehicle').prop("checked", false);
                            $('.index-vehicle').show();
                            $('#vehicleNoTxt').hide();
                        }
                        
                        $('#tareOutgoing2').trigger('keyup');
                        
                        $('#normalCard').show();
                        $('#grossCapture').hide();
                        $('#tareCapture').hide();
                        toggleSubmitButtons();
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
                $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
                $('#addModal').find('#grossIncoming').val(0);
                $('#addModal').find('#grossIncomingDate').val("");
                $('#addModal').find('#tareOutgoing').val(0);
                $('#addModal').find('#tareOutgoingDate').val("");
                $('#addModal').find('#nettWeight').val(0);
                $('#tareOutgoing2').trigger('keyup');
                $('#normalCard').hide();
                $('#grossCapture').show();
                $('#tareCapture').show();
                toggleSubmitButtons();
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

        <?php
            if(isset($_GET['weight'])){
                echo 'edit('.$_GET['weight'].');';
            }
        ?>
    });

    function showTareConfirm(){
        $('#confirmCaptureMessage').text('<?=$languageArray['confirm_second_weighing_code'][$language]?>: ' + $('#tareOutgoing').val() + ' kg');
        isSecondWeighingCapture = true;
        $('#confirmCaptureModal').modal('show');
    }

    // Price card: subtotal = final weight (kg) / 1000 x unit price (per tonne), SST = subtotal x SST %
    function calculatePrice(){
        var weight = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
        var price = $('#unitPrice').val() ? parseFloat($('#unitPrice').val()) : 0;
        var sstRate = $('#sstRate').val() ? parseFloat($('#sstRate').val()) : 0;
        var subTotalPrice = weight / 1000 * price;
        var sstPrice = subTotalPrice * sstRate / 100;

        $('#subTotalPrice').val(subTotalPrice.toFixed(2));
        $('#sstPrice').val(sstPrice.toFixed(2));
        $('#totalPrice').val((subTotalPrice + sstPrice).toFixed(2));
    }

    // Unit price of the selected item for the selected customer/supplier, then the deposit balance check
    function loadDepositPrice(){
        var transactionStatus = $('#transactionStatus').val();
        if (transactionStatus != 'Sales' && transactionStatus != 'Purchase') {
            calculatePrice();
            return;
        }

        var isSales = transactionStatus == 'Sales';
        var partyId = isSales ? $('#customerName :selected').data('id') : $('#supplierName :selected').data('id');
        var itemCode = isSales ? $('#productCode').val() : $('#rawMaterialCode').val();
        if (!partyId) {
            if (!keepUnitPrice) $('#unitPrice').val(0);
            calculatePrice();
            return;
        }

        $.post('php/modules/deposit/index.php', {action: 'check', partyType: isSales ? 'CUSTOMER' : 'SUPPLIER', partyId: partyId, itemCode: itemCode || ''}, function(data){
            var obj = JSON.parse(data);
            if (obj.status !== 'success') return;

            if (!keepUnitPrice) $('#unitPrice').val(obj.price);
            calculatePrice();

            if (obj.enabled && parseFloat(obj.balance) < 1) {
                alert(<?=json_encode($languageArray['insufficient_deposit_code'][$language])?>);
                $('#addModal').modal('hide');
            }
        });
    }

    // Manual weighing is shown, and dates can be changed, only for statuses with their Weighing permission
    function applyManualPermissions() {
        var status = $('#addModal').find('#transactionStatus').val();
        var allowed = manualWeighingStatuses.includes(status);
        $('#manualWeightRow').toggle(allowed);
        if (!allowed && $('#manualWeightYes').is(':checked')) {
            $('#manualWeightNo').prop('checked', true).trigger('click');
        }
        toggleSubmitButtons();
        // Date pickers stay visible but read-only when the status lacks manual_date_change
        var priceAllowed = manualPriceStatuses.includes(status);
        $('#unitPrice').prop('readonly', !priceAllowed).toggleClass('input-readonly', !priceAllowed);
        var dateAllowed = manualDateStatuses.includes(status);
        [grossIncomingDatePicker, tareOutgoingDatePicker, grossIncomingDatePicker2, tareOutgoingDatePicker2].forEach(function (picker) {
            if (!picker) {
                return;
            }
            picker.set('allowInput', dateAllowed);
            picker.set('clickOpens', dateAllowed);
            $(picker.input).prop('readonly', !dateAllowed);
            $(picker.altInput).prop('readonly', !dateAllowed);
        });
    }
    
    function renderTable() {
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
        var locationI = $('#locationSearch').val() ? $('#locationSearch').val() : '';
        var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
        var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
        var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';
        var invDelPoI = $('#invDelPoSearch').val() ? $('#invDelPoSearch').val() : '';

        //Destroy the old Datatable
        if ($.fn.DataTable.isDataTable("#weightTable")) $("#weightTable").DataTable().clear().destroy();
        if ($.fn.DataTable.isDataTable("#emptyContainerTable")) $("#emptyContainerTable").DataTable().clear().destroy();

        //Create new Datatable
        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/modules/weight/index.php?action=list',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    customer: customerNoI,
                    supplier: supplierI,
                    vehicle: vehicleNoI,
                    invoice: invoiceNoI,
                    batch: batchNoI,
                    product: productSearchI,
                    rawMaterial: rawMaterialI,
                    plant: plantNoI,
                    location: locationI,
                    transactionId: transactionIdI,
                    containerNo: containerNoI,
                    sealNo: sealNoI,
                    invDelPo: invDelPoI
                } 
            },
            'columns': [
                {
                    // Add a checkbox with a unique ID for each row
                    data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                    className: 'select-checkbox',
                    orderable: false,
                    render: function (data, type, row) {
                        if (row.weight_type == 'Primer Mover + Container'){
                            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'" data-type="Empty Container"/>';
                        }else{
                            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'" data-type="Lorry"/>';
                        }
                    }
                },
                { data: 'location' },                
                { data: 'transaction_id' },                
                // { data: 'weight_type' },
                { data: 'transaction_status' },
                { data: 'customer' },
                // { data: 'seal_no' },
                { data: 'container_no2' },
                { data: 'seal_no2' },
                // { data: 'container_no' },
                { data: 'lorry_plate_no1' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { data: 'duration', orderable: false },
                { data: 'tare_weight1' },
                { data: 'tare_weight1_date' },
                { data: 'nett_weight1' },
                { data: 'lorry_plate_no2', visible: containerColumnsEnabled },
                { data: 'gross_weight2', visible: containerColumnsEnabled },
                { data: 'gross_weight2_date', visible: containerColumnsEnabled },
                { data: 'tare_weight2', visible: containerColumnsEnabled },
                { data: 'tare_weight2_date', visible: containerColumnsEnabled },
                { data: 'nett_weight2', visible: containerColumnsEnabled },
                { 
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        let buttons = `<div class="row g-1 d-flex">`;

                        if (canEditWeight) {
                            // if (row.is_complete != 'Y' ){
                            if (row.weight_type == 'Primer Mover + Container'){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>`;
                            }else{
                                buttons += `
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'N')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>`;
                            }
                            // }
                        }else if (canWeightOut) {
                            if (row.is_complete != 'Y' ){
                                if (row.weight_type == 'Primer Mover + Container'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-weight-hanging"></i>
                                        </button>
                                    </div>`;    
                                }else{
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'N')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-weight-hanging"></i>
                                        </button>
                                    </div>`;  
                                }

                            }
                        }

                        if (row.weight_type != 'Primer Mover + Container'){
                            buttons += `
                            ${canPrintWeight ? `<div class="col-auto">
                                <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>` : ''}`;
                        }

                        if(userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
                            if (row.weight_type == 'Primer Mover + Container'){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data}, 'Y')" class="btn btn-danger btn-sm">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>`;
                            }else{
                                buttons += `
                                <div class="col-auto">
                                    <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data}, 'N')" class="btn btn-danger btn-sm">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>`;
                            }
                        }
                            
                        buttons += `</div>`;

                        return buttons;
                    }
                }
            ],
            "drawCallback": function(settings) {
                $('#salesInfo').text(settings.json.salesTotal);
                $('#purchaseInfo').text(settings.json.purchaseTotal);
                $('#localInfo').text(settings.json.localTotal);
                $('#miscInfo').text(settings.json.miscTotal);
            }   
        });

        //Create new Datatable for empty container
        emptyContainerTable = $("#emptyContainerTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/modules/weight/index.php?action=listEmptyContainer',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    location: locationI
                } 
            },
            'columns': [
                {
                    // Add a checkbox with a unique ID for each row
                    data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                    className: 'select-checkbox',
                    orderable: false,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                    }
                },
                { data: 'container_no' },                
                { data: 'seal_no' },                
                { data: 'transaction_status' },
                { data: 'lorry_plate_no1' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { data: 'tare_weight1' },
                { data: 'tare_weight1_date' },
                { data: 'nett_weight1' },
                { 
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        let buttons = `<div class="row g-1 d-flex">`;

                        if (canEditWeight) {
                            if (row.is_complete != 'Y' ){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>`;
                            }
                        }else if (canWeightOut) {
                            if (row.is_complete != 'Y' ){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data},'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-weight-hanging"></i>
                                    </button>
                                </div>`;
                            }
                        }

                        buttons += `
                        ${canPrintWeight ? `<div class="col-auto">
                            <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}', 'Y')" class="btn btn-info btn-sm">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>` : ''}`;

                        if(userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
                            buttons += `
                            <div class="col-auto">
                                <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data}, 'Y')" class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>`;
                        }
                            
                        buttons += `</div>`;

                        return buttons;
                    }
                }
            ]
        });
    }

    function showVehicleNotRegistered(vehicleNo){
        $('#vehicleNotRegisteredMessage').text(<?=json_encode($languageArray['vehicle_not_registered_code'][$language])?>.replace('{vehicle}', vehicleNo));
        $('#vehicleNotRegisteredModal').modal('show');
    }
    
    function showEntryOverrideError(message){
        $('#entryOverridePassword').val('').addClass('is-invalid').trigger('focus');
        $('#entryOverridePasswordError').text(message);
    }

    function toggleSubmitButtons(){
        // Capture is usable only while its capture button is shown
        var canCapture = $('#grossCapture').css('display') != 'none';
        var show = $('#manualWeightYes').is(':checked') || !canCapture;
        $('#submitWeight, #submitWeightPrint').toggle(show);
    }

    function handleWeightType(weightType){
        if (weightType == 'Container'){
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val(0);
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val(0);
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#grossCapture').hide();
            $('#tareCapture').hide();
            $('#containerCard').show();
        }else if(weightType == 'Empty Container'){
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming2').val(0);
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val(0);
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val(0);
            $('#containerCard').hide();
            $('#grossCapture').show();
            $('#tareCapture').show();
            $('#normalCard').show();
        }else if(weightType == 'Different Container'){
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val(0);
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val(0);
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#grossCapture').hide();
            $('#tareCapture').hide();
            $('#containerCard').show();
        }else{
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming2').val(0);
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val(0);
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val(0);
            $('#normalCard').show();
            $('#grossCapture').show();
            $('#tareCapture').show();
            $('#containerCard').hide();
        }
        toggleSubmitButtons();
    }

    function format (row) {
        var transactionStatus = '';
        var weightType = '';

        // Translation variables
        var lang = {
            customerSupplier: <?=json_encode($languageArray['customer_supplier_code'][$language])?>,
            deliveryOrderInfo: <?=json_encode($languageArray['delivery_order_info_code'][$language])?>,
            purchaseInfo: <?=json_encode($languageArray['purchase_info_code'][$language])?>,
            transactionId: <?=json_encode($languageArray['transaction_id_code'][$language])?>,
            transactionDate: <?=json_encode($languageArray['transaction_date_code'][$language])?>,
            vehiclePlate: <?=json_encode($languageArray['vehicle_code'][$language])?>,
            plant: <?=json_encode($languageArray['plant_code'][$language])?>,
            binNo: <?=json_encode($languageArray['container_no2_code'][$language])?>,
            binType: <?=json_encode($languageArray['seal_no2_code'][$language])?>,
            weightType: <?=json_encode($languageArray['weight_type_code'][$language])?>,
            weightStatus: <?=json_encode($languageArray['weight_status_code'][$language])?>,
            waste: <?=json_encode($languageArray['raw_material_code'][$language])?>,
            product: <?=json_encode($languageArray['product_code'][$language])?>,
            containerNo: <?=json_encode($languageArray['container_no_code'][$language])?>,
            contractor: <?=json_encode($languageArray['contractor_code'][$language])?>,
            remarks: <?=json_encode($languageArray['remarks_code'][$language])?>,
            weighingInfo: <?=json_encode($languageArray['weighing_info_code'][$language])?>,
            inWeight: <?=json_encode($languageArray['gross_incoming_code'][$language])?>,
            inDateTime: <?=json_encode($languageArray['incoming_date_code'][$language])?>,
            inWeighBy: <?=json_encode($languageArray['in_weigh_by_code'][$language])?>,
            outWeight: <?=json_encode($languageArray['tare_outgoing_code'][$language])?>,
            outDateTime: <?=json_encode($languageArray['outgoing_date_code'][$language])?>,
            outWeighBy: <?=json_encode($languageArray['out_weigh_by_code'][$language])?>,
            nettWeight: <?=json_encode($languageArray['nett_weight_code'][$language])?>,
            subTotalWeight: <?=json_encode($languageArray['sub_total_weight_code'][$language])?>,
            unitPrice: <?=json_encode($languageArray['unit_price_code'][$language])?>,
            totalPrice: <?=json_encode($languageArray['total_price_code'][$language])?>,
            containerWeighing: <?=json_encode($languageArray['container_weighing_code'][$language])?>,
            dispatch: <?=json_encode($languageArray['dispatch_code'][$language])?>,
            receiving: <?=json_encode($languageArray['receiving_code'][$language])?>,
            internalTransfer: <?=json_encode($languageArray['internal_transfer_code'][$language])?>,
            miscellaneous: <?=json_encode($languageArray['miscellaneous_code'][$language])?>,
            primerMover: <?=json_encode($languageArray['primer_mover_code'][$language])?>,
            primerMoverContainer: <?=json_encode($languageArray['primer_mover_container_code'][$language])?>,
            normalWeighing: <?=json_encode($languageArray['normal_weighing_code'][$language])?>,
            primerMoverDiffBins: <?=json_encode($languageArray['primer_mover_diff_bins_code'][$language])?>
        };

        if (row.transaction_status == 'Sales') {
            transactionStatus = lang.dispatch;
        } else if (row.transaction_status == 'Purchase') {
            transactionStatus = lang.receiving;
        } else if (row.transaction_status == 'Local') {
            transactionStatus = lang.internalTransfer;
        } else {
            transactionStatus = lang.miscellaneous;
        }

        if(row.weight_type == 'Container'){
            weightType = lang.primerMover;
        }else if(row.weight_type == 'Empty Container'){
            weightType = lang.primerMoverContainer;
        }else if(row.weight_type == 'Normal'){
            weightType = lang.normalWeighing;
        }else if(row.weight_type == 'Different Container'){
            weightType = lang.primerMoverDiffBins;
        }else{
            weightType = row.weight_type;
        }

        var returnString = `
        <!-- Customer Section -->
        <div class="row">
            <div class="col-6">
                <p><span><strong style="font-size:120%; text-decoration: underline;">${lang.customerSupplier}</strong></span><br>
                <p>
                    ${row.name ? `<span><strong>${row.name}</strong></span><br>` : ''}
                    ${row.address_line_1 ? `<span>${row.address_line_1}</span><br>` : ''}
                    ${row.address_line_2 ? `<span>${row.address_line_2}</span><br>` : ''}
                    ${row.address_line_3 ? `<span>${row.address_line_3}</span><br>` : ''}
                    ${row.phone_no || row.fax_no ? `<span>${row.phone_no ? 'TEL: ' + row.phone_no : ''}${row.phone_no && row.fax_no ? ' ' : ''}${row.fax_no ? 'FAX: ' + row.fax_no : ''}</span>` : ''}
                </p>
            </div>
        </div>
        <hr>
        <!-- Delivery Order Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">${row.transaction_status === 'Purchase' ? lang.purchaseInfo : lang.deliveryOrderInfo}</strong></span><br>
            <div class="col-12 row">
                <p class="col-4"><strong>${lang.transactionId}:</strong> ${row.transaction_id}</p>
                <p class="col-4"><strong>${lang.vehiclePlate}:</strong> ${row.lorry_plate_no1}</p>
                <p class="col-4"><strong>${lang.binNo}:</strong> ${row.container_no2}</p>


                <p class="col-4"><strong>${lang.transactionDate}:</strong> ${row.transaction_date}</p>
                <p class="col-4"><strong>${lang.plant}:</strong> ${row.plant_name}</p>
                <p class="col-4"><strong>${lang.binType}:</strong> ${row.seal_no2}</p>

                <p class="col-4"><strong>${lang.weightType}:</strong> ${weightType}</p>
                `;

                if (row.transaction_status == 'Purchase' || row.transaction_status == 'Local'){
                    returnString += `<p class="col-4"><strong>${lang.waste}:</strong> ${row.product_rawmat_name}</p>`;
                }else{
                    returnString += `<p class="col-4"><strong>${lang.product}:</strong> ${row.product_rawmat_name}</p>`;
                }
        
            returnString += `
                <p class="col-4"><strong>${lang.containerNo}:</strong> ${row.container_no}</p>
                <p class="col-4"><strong>${lang.weightStatus}:</strong> ${transactionStatus}</p>
                <p class="col-4"><strong>${lang.contractor}:</strong> ${row.contractorName}</p>
                <p class="col-12"><strong>${lang.remarks}:</strong> ${$('<div>').text(row.remarks || '').html()}</p>
            </div>
        </div>
        <hr>

        <!-- Weighing Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">${lang.weighingInfo}</strong></span><br>
            <!-- Normal -->
            <div class="col-12 row">
                <p class="col-4"><strong>${lang.inWeight}:</strong> ${row.gross_weight1}</p>
                <p class="col-4"><strong>${lang.inDateTime}:</strong> ${row.gross_weight1_date}</p>
                <p class="col-4"><strong>${lang.inWeighBy}:</strong> ${row.gross_weight_by1}</p>
                <p class="col-4"><strong>${lang.outWeight}:</strong> ${row.tare_weight1}</p>
                <p class="col-4"><strong>${lang.outDateTime}:</strong> ${row.tare_weight1_date}</p>
                <p class="col-4"><strong>${lang.outWeighBy}:</strong> ${row.tare_weight_by1}</p>
                <p class="col-4"><strong>${lang.nettWeight}:</strong> ${row.nett_weight1}</p>
                <p class="col-4"><strong>${lang.unitPrice}:</strong> ${row.unit_price || ''}</p>
                <p class="col-4"><strong>${lang.totalPrice}:</strong> ${row.total_price || ''}</p>
            </div>
            <!-- Container -->
            ${containerColumnsEnabled ? `
            <p><strong style="font-size:110%; text-decoration: underline;">${lang.containerWeighing}</strong></p>
            <div class="col-12 row">
                <p class="col-4"><strong>${lang.vehiclePlate} 2:</strong> ${row.lorry_plate_no2}</p>
                <p class="col-4"><strong>${lang.inWeight} 2:</strong> ${row.gross_weight2}</p>
                <p class="col-4"><strong>${lang.inDateTime} 2:</strong> ${row.gross_weight2_date}</p>
                <p class="col-4"><strong>${lang.inWeighBy} 2:</strong> ${row.gross_weight_by2}</p>
                <p class="col-4"><strong>${lang.outWeight} 2:</strong> ${row.tare_weight2}</p>
                <p class="col-4"><strong>${lang.outDateTime} 2:</strong> ${row.tare_weight2_date}</p>
                <p class="col-4"><strong>${lang.outWeighBy} 2:</strong> ${row.tare_weight_by2}</p>
                <p class="col-4"><strong>${lang.nettWeight} 2:</strong> ${row.nett_weight2}</p>            
            </div>
            <div class="col-12 row">
                <p class="col-4"><strong>${lang.subTotalWeight}:</strong> ${row.final_weight}</p>
            </div>
            ` : ''}
        </div>
        `;
        
        returnString += formatVehicleEntryLog(row.vehicle_entry_logs || []);

        return returnString;
    }

    // Vehicle Entry Log section: IN (counted towards the daily limit) and REVERSE (record cancelled)
    function formatVehicleEntryLog(logs){
        var html = '<hr><div class="row"><p><span><strong style="font-size:120%; text-decoration: underline;">' + <?=json_encode($languageArray['vehicle_entry_log_code'][$language])?> + '</strong></span></p>';

        if (logs.length == 0) {
            return html + '<p class="text-muted">' + <?=json_encode($languageArray['no_log_found_code'][$language])?> + '</p></div>';
        }

        var rows = '';

        for (var i = 0; i < logs.length; i++) {
            var log = logs[i];
            var action = log.action == 'REVERSE'
                ? '<span class="badge bg-danger">' + <?=json_encode($languageArray['entry_reversed_code'][$language])?> + '</span>'
                : '<span class="badge bg-success">' + <?=json_encode($languageArray['entry_in_code'][$language])?> + '</span>';
            var override = log.is_override == 'Y'
                ? '<span class="badge bg-warning">' + <?=json_encode($languageArray['yes_code'][$language])?> + '</span>'
                : <?=json_encode($languageArray['no_code'][$language])?>;

            rows += '<tr>' +
                '<td>' + action + '</td>' +
                '<td>' + log.entry_date + '</td>' +
                '<td>' + log.entry_count + ' / ' + (log.max_entry !== null ? log.max_entry : <?=json_encode($languageArray['unlimited_code'][$language])?>) + '</td>' +
                '<td>' + override + '</td>' +
                '<td>' + log.approved_by + (log.approved_date ? '<br><small class="text-muted">' + log.approved_date + '</small>' : '') + '</td>' +
                '<td>' + $('<div>').text(log.remarks).html() + '</td>' +
                '<td>' + log.action_by + '</td>' +
                '<td>' + log.event_date + '</td>' +
            '</tr>';
        }

        return html + '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">' +
            '<thead><tr>' +
                '<th>' + <?=json_encode($languageArray['action_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['entry_date_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['entry_no_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['override_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['approved_by_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['remarks_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['action_by_code'][$language])?> + '</th>' +
                '<th>' + <?=json_encode($languageArray['action_date_code'][$language])?> + '</th>' +
            '</tr></thead><tbody>' + rows + '</tbody></table></div></div>';
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

    var entryOverrideProceed = null;

    // Vehicle daily entry limit: only for new weighing. Over the limit, the logged-in user re-enters their password.
    function checkVehicleEntry(proceed){
        $('#weightForm').find('#overridePassword').val('');

        var vehicleNo = $('#manualVehicle').is(':checked') ? $('#vehicleNoTxt').val() : $('#vehiclePlateNo1').val();

        if ($('#weightForm').find('#id').val() || !vehicleNo) {
            proceed();
            return;
        }

        $('#spinnerLoading').show();
        $.post('php/modules/vehicle/index.php', {action: 'checkEntry', vehicleNo: vehicleNo}, function(data){
            var obj = JSON.parse(data);
            $('#spinnerLoading').hide();

            if (obj.status === 'success' && obj.exceeded) {
                var blocked = obj.max === 0;
                var message = blocked
                    ? <?=json_encode($languageArray['vehicle_blocked_code'][$language])?>.replace('{vehicle}', vehicleNo)
                    : <?=json_encode($languageArray['insufficient_entry_code'][$language])?>
                        .replace('{vehicle}', vehicleNo).replace('{count}', obj.count).replace('{max}', obj.max);

                // Limit 0: straight to the password. Otherwise confirm first, then the password.
                if (blocked || confirm(message + ' ' + <?=json_encode($languageArray['proceed_code'][$language])?>)) {
                    entryOverrideProceed = proceed;
                    $('#entryOverrideMessage').text(message);
                    $('#entryOverridePassword').val('').removeClass('is-invalid');
                    $('#entryOverrideModal').modal('show');
                }
            }
            else {
                // weight.php checks the limit again before saving
                proceed();
            }
        });
    }

    // Vehicle picked (dropdown or typed): open its pending record for a second weighing, else fill a new record
    function vehicleChanged(vehicleNo){
        if (!vehicleNo){
            return;
        }
        if (vehicleNo == '-' || $('#addModal').find('#id').val()){
            pullVehicleDetails(vehicleNo);
            return;
        }

        $.post('php/modules/weight/index.php', {action: 'getPendingByVehicle', vehicleNo: vehicleNo, transactionStatus: $('#transactionStatus').val()}, function(data){
            var obj = JSON.parse(data);

            if (obj.status === 'success' && obj.id){
                edit(obj.id, 'N');
                $("#successBtn").attr('data-toast-text', <?=json_encode($languageArray['pending_record_loaded_code'][$language])?>);
                $("#successBtn").click();
            }
            else{
                if (obj.status !== 'success'){
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
                pullVehicleDetails(vehicleNo);
            }
        });
    }

    // Vehicle master data for a new record: customer / supplier, bin and vehicle weight
    function pullVehicleDetails(vehicleNo){
        var transactionStatus = $('#transactionStatus').val();

        $.post('php/modules/vehicle/index.php', {action: 'get', userID: vehicleNo, type: 'pullCustomer'}, function (data){
            var obj = JSON.parse(data);

            if (obj.status == 'success' && !obj.message.id && vehicleNo != '-'){
                $('#addModal').find('#contractorId').val('').trigger('change');
                $('#addModal').find('#containerNo2').val('').trigger('change');
                $('#addModal').find('#sealNo2').val('').trigger('change');
                showVehicleNotRegistered(vehicleNo);
            }
            else if (obj.status == 'success'){
                if (transactionStatus == 'Sales'){
                    $('#addModal').find('#grossIncoming').val(obj.message.vehicle_weight || 0).trigger('keyup');
                }
                var customerName = obj.message.customer_name;
                var customerCode = obj.message.customer_code;
                var supplierName = obj.message.supplier_name;
                var supplierCode = obj.message.supplier_code;
                $('#addModal').find('#contractorId').val(obj.message.contractor_id || '').trigger('change');
                $('#addModal').find('#containerNo2').val(obj.message.bin_no || '').trigger('change');
                $('#addModal').find('#sealNo2').val(obj.message.bin_type || '').trigger('change');

                if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                    $('#addModal').find('#customerName').val(customerName).trigger('change');
                    $('#addModal').find('#customerCode').val(customerCode);
                }else{
                    $('#addModal').find('#supplierName').val(supplierName).trigger('change');
                    $('#addModal').find('#supplierCode').val(supplierCode);
                    $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name || '-').trigger('change');
                }
            }
            else if(obj.status === 'error'){
                alert(obj.message);
                $('#vehicleNoTxt').val('');
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }

    function edit(id, isContainer){
        $('#spinnerLoading').show();

        var type = '';
        if (isContainer == 'Y'){
            type = 'Container';
        }else{
            type = 'Weight'
        }

        $.post('php/modules/weight/index.php', {action: 'get', userID: id, type: type}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                if(obj.message.is_complete == 'Y'){
                    // Hide Capture Button When Edit
                    $('#addModal').find('#grossCapture').hide();
                    $('#addModal').find('#tareCapture').hide();
                }
                else{
                    // Show Capture Button When Edit
                    $('#addModal').find('#grossCapture').show();
                    $('#addModal').find('#tareCapture').show();
                }

                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).trigger('change');
                // Feature-flagged weight types may be hidden; keep old records showing their saved type
                if (obj.message.weight_type && $('#addModal').find('#weightType option[value="' + obj.message.weight_type + '"]').length == 0) {
                    var hiddenWeightTypes = {'Container': 'Primer Mover', 'Empty Container': 'Primer Mover + Container', 'Different Container': 'Primer Mover + Different Bins'};
                    $('#addModal').find('#weightType').append($('<option class="feature-hidden">').val(obj.message.weight_type).text(hiddenWeightTypes[obj.message.weight_type] || obj.message.weight_type));
                }
                $('#addModal').find('#weightType').val(obj.message.weight_type).trigger('change');
                $('#addModal').find('#customerType').val(obj.message.customer_type).trigger('change');
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));

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
                    $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                    $('#manualVehicle').val(1);
                    $('#manualVehicle').prop("checked", true);
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo1Edit').val('EDIT');
                    $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).select2('destroy').select2();
                    $('#manualVehicle').val(0);
                    $('#manualVehicle').prop("checked", false);
                    $('.index-vehicle').show();
                    $('#vehicleNoTxt').hide();
                }

                if(obj.message.vehicleNoTxt2 != null){
                    $('#addModal').find('#vehicleNoTxt2').val(obj.message.vehicleNoTxt2);
                    $('#manualVehicle2').val(1);
                    $('#manualVehicle2').prop("checked", true);
                    $('.index-vehicle2').hide();
                    $('#vehicleNoTxt2').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2).select2('destroy').select2();
                    $('#manualVehicle2').val(0);
                    $('#manualVehicle2').prop("checked", false);
                    $('.index-vehicle2').show();
                    $('#vehicleNoTxt2').hide();
                }
                
                $('#addModal').find('#productCode').val(obj.message.product_code);                
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                $('#addModal').find('#customerName').val(obj.message.customer_name).select2('destroy').select2();
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name).select2('destroy').select2();
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                $('#addModal').find('#contractorId').val(obj.message.contractor_id || '').trigger('change');
                $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                $('#addModal').find('#productCode').val(obj.message.product_code);
                $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                $('#addModal').find('#plantCode').val(obj.message.plant_code);
                
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date));
                $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
                $('#addModal').find('#grossLocationId').val(obj.message.location_id || '');
                $('#addModal').find('#tareLocationId').val(obj.message.location_id2 || '');
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#vehicleWeight2').val(obj.message.lorry_no2_weight);
                $('#addModal').find('#emptyContainerWeight2').val(obj.message.empty_container2_weight);
                $('#addModal').find('#replacementContainer').val(obj.message.replacement_container).trigger('keyup');
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                grossIncomingDatePicker2.setDate(obj.message.gross_weight2_date != null ? new Date(obj.message.gross_weight2_date) : null);
                $('#addModal').find('#grossWeightBy2').val(obj.message.gross_weight_by2);
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                tareOutgoingDatePicker2.setDate(obj.message.tare_weight2_date != null ? new Date(obj.message.tare_weight2_date) : null);
                $('#addModal').find('#tareWeightBy2').val(obj.message.tare_weight_by2);
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);
                $('#addModal').find('#weightDifferencePerc').val(obj.message.weight_different_perc);
                $('#addModal').find('#currentWeight').text(obj.message.final_weight);

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
                applyManualPermissions();

                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                keepUnitPrice = true;
                $('#addModal').find('#unitPrice').val(obj.message.unit_price);
                $('#addModal').find('#sstRate').val(obj.message.sst_rate);
                $('#addModal').find('#subTotalPrice').val(obj.message.sub_total);
                $('#addModal').find('#sstPrice').val(obj.message.sst);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);
                $('#addModal').find('#containerNoInput').val(obj.message.container_no);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#containerNo2').val(obj.message.container_no2);
                $('#addModal').find('#sealNo').val(obj.message.seal_no);
                $('#addModal').find('#sealNo2').val(obj.message.seal_no2);

                // Load container data and update the emptyContainerNo field if it's a container
                if((obj.message.weight_type == 'Container' || obj.message.weight_type == 'Different Container') && obj.message.container_no){
                    loadContainerData(function() {
                        $('#normalCard').show();

                        // Check if container value exist in the select tag
                        var emptyContainerExists = $('#addModal').find('#emptyContainerNo option').filter(function() {
                            return $(this).val() === obj.message.container_no;
                        }).length > 0;

                        if (!emptyContainerExists){
                            // Append missing empty container no
                            $('#addModal').find('#emptyContainerNo').append(
                                '<option value="'+obj.message.container_no+'">'+obj.message.container_no+'</option>'
                            );
                        }

                        // Callback to ensure the dropdown is updated before setting the value
                        $('#addModal').find('#emptyContainerNo').val(obj.message.container_no).select2('destroy').select2();

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

                // Load these field after PO/SO is loaded
                /*$('#addModal').on('orderLoaded', function() {
                    $('#addModal').find('#customerCode').val(obj.message.customer_code);
                    $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                    $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                    $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change')
                    $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                    $('#addModal').find('#contractorId').val(obj.message.contractor_id || '').trigger('change');
                    $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                    $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                    $('#addModal').find('#productCode').val(obj.message.product_code);
                    $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                    $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                    $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                    $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                    $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                    $('#addModal').find('#plantCode').val(obj.message.plant_code);

                    // Hide select and show input readonly
                    // if (obj.message.transaction_status == 'Purchase'){
                    //     $('#addModal').find('#purchaseOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#purchaseOrderEdit').val(obj.message.purchase_order).show();
                    // }else{
                    //     $('#addModal').find('#salesOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#salesOrderEdit').val(obj.message.purchase_order).show();
                    // }
                });*/

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
        $.post('php/modules/weight/index.php', {action: 'containers', userID: transactionStatus}, function (data){
            var obj = JSON.parse(data);

            if (obj.status == 'success'){
                if (obj.message.length > 0){
                    $('#addModal').find('#emptyContainerNo').empty();
                    $('#addModal').find('#emptyContainerNo').append('<option selected="-">-</option>');

                    // Populate container numbers
                    for (var i = 0; i < obj.message.length; i++) {
                        var id = obj.message[i].id;
                        var container_no = obj.message[i].container_no;

                        $('#addModal').find('#emptyContainerNo').append(
                            '<option value="'+container_no+'">'+container_no+'</option>'
                        );
                    }

                    // Execute the callback to finalize the process
                                    }

                // Execute the callback to finalize the process
                if (callback) { callback(); }
            } else {
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }

    function deactivate(id, isEmptyContainer) {
        if (confirm('Are you sure you want to cancel this weighing record?')) {
            $('#cancelModal').find('#id').val(id);
            $('#cancelModal').find('#isEmptyContainer').val(isEmptyContainer);
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
    }

    function print(id, transactionStatus, isEmptyContainer = 'N') {
        $('#spinnerLoading').show();

        $.post('php/modules/weight/index.php', {action: 'print', userID: id, file: 'weight', prePrint: 'en', isEmptyContainer: isEmptyContainer, source: 'weighing'}, function(data){
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
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', "Something wrong when print");
                $("#failBtn").click();
            }
        });
    }
    </script>
</body>
</html>