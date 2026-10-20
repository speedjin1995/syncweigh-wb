<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$user = $_SESSION['id'];
$username = $_SESSION["username"];
$plantId = $_SESSION['plant'];
$stmt = $db->prepare("SELECT * from Port WHERE weighind_id = ?");
$stmt->bind_param('s', $user);
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
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0' ORDER BY name ASC");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$purchaseOrder = $db->query("SELECT * FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$salesOrder = $db->query("SELECT * FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$container = $db->query("SELECT * FROM Weight_Container WHERE status = '0' AND is_complete = 'Y' AND is_cancel = 'N'");

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
                                                                <option value="Sales"><?=$languageArray['dispatch_code'][$language]?></option>
                                                                <option value="Purchase"><?=$languageArray['receiving_code'][$language]?></option>
                                                                <option value="Local"><?=$languageArray['internal_transfer_code'][$language]?></option>
                                                                <option value="Misc"><?=$languageArray['miscellaneous_code'][$language]?></option>
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
                                                                <option value="Normal"><?=$languageArray['normal_weighing_code'][$language]?></option>
                                                                <option value="Container">Primer Mover</option>
                                                                <option value="Empty Container">Primer Mover + Container</option>
                                                                <option value="Different Container">Primer Mover + Different Bins</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label"><?=$languageArray['status_code'][$language]?></label>
                                                            <select id="batchNoSearch" class="form-select select2">
                                                                <option value="N" selected><?=$languageArray['pending_code'][$language]?></option>
                                                                <option value="Y"><?=$languageArray['complete_code'][$language]?></option>
                                                                <option value="C"><?=$languageArray['cancelled_code'][$language]?></option>
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
                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="dispatchCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0"><?=$languageArray['dispatch_code'][$language]?></p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                                        <i class="bx bx-export text-success"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4 list-group list-group-flush">
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="N" data-transaction="Sales">
                                        <span class="text-muted"><?=$languageArray['pending_code'][$language]?></span>
                                        <span class="fw-semibold" id="salesPending">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="Y" data-transaction="Sales">
                                        <span class="text-muted"><?=$languageArray['complete_code'][$language]?></span>
                                        <span class="fw-semibold text-success" id="salesComplete">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="C" data-transaction="Sales">
                                        <span class="text-muted"><?=$languageArray['cancelled_code'][$language]?></span>
                                        <span class="fw-semibold text-danger" id="salesCancel">0</span>
                                        </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="receivingCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0"><?=$languageArray['receiving_code'][$language]?></p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                                        <i class="bx bx-import text-info"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4 list-group list-group-flush">
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="N" data-transaction="Purchase">
                                        <span class="text-muted"><?=$languageArray['pending_code'][$language]?></span>
                                        <span class="fw-semibold" id="purchasePending">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="Y" data-transaction="Purchase">
                                        <span class="text-muted"><?=$languageArray['complete_code'][$language]?></span>
                                        <span class="fw-semibold text-success" id="purchaseComplete">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="C" data-transaction="Purchase">
                                        <span class="text-muted"><?=$languageArray['cancelled_code'][$language]?></span>
                                        <span class="fw-semibold text-danger" id="purchaseCancel">0</span>
                                        </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="transferCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0"><?=$languageArray['internal_transfer_code'][$language]?></p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-transfer text-warning"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4 list-group list-group-flush">
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="N" data-transaction="Local">
                                        <span class="text-muted"><?=$languageArray['pending_code'][$language]?></span>
                                        <span class="fw-semibold" id="localPending">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="Y" data-transaction="Local">
                                        <span class="text-muted"><?=$languageArray['complete_code'][$language]?></span>
                                        <span class="fw-semibold text-success" id="localComplete">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="C" data-transaction="Local">
                                        <span class="text-muted"><?=$languageArray['cancelled_code'][$language]?></span>
                                        <span class="fw-semibold text-danger" id="localCancel">0</span>
                                        </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="miscCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0"><?=$languageArray['miscellaneous_code'][$language]?></p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-secondary rounded fs-3">
                                                        <i class="bx bx-dots-horizontal-rounded text-secondary"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mt-4 list-group list-group-flush">
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="N" data-transaction="Misc">
                                        <span class="text-muted"><?=$languageArray['pending_code'][$language]?></span>
                                        <span class="fw-semibold" id="miscPending">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="Y" data-transaction="Misc">
                                        <span class="text-muted"><?=$languageArray['complete_code'][$language]?></span>
                                        <span class="fw-semibold text-success" id="miscComplete">0</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 status-item" data-status="C" data-transaction="Misc">
                                        <span class="text-muted"><?=$languageArray['cancelled_code'][$language]?></span>
                                        <span class="fw-semibold text-danger" id="miscCancel">0</span>
                                        </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6 add-new-weight">
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3"  <?php
                                                                                if($_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'ADMIN'){
                                                                                    echo 'style="display:none;"';
                                                                                }?>>
                                                                                <div class="row">
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
                                                                                            <option value="Normal" selected><?=$languageArray['normal_weighing_code'][$language]?></option>
                                                                                            <option value="Container">Primer Mover</option>
                                                                                            <option value="Empty Container">Primer Mover + Container</option>
                                                                                            <option value="Different Container">Primer Mover + Different Bins</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label"><?=$languageArray['transaction_status_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                                            <option value="Sales" selected><?=$languageArray['dispatch_code'][$language]?></option>
                                                                                            <option value="Purchase"><?=$languageArray['receiving_code'][$language]?></option>
                                                                                            <option value="Local"><?=$languageArray['internal_transfer_code'][$language]?></option>
                                                                                            <option value="Misc"><?=$languageArray['miscellaneous_code'][$language]?></option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
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
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label"><?=$languageArray['customer_name_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select js-choice select2" id="customerName" name="customerName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
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
                                                                                                <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label"><?=$languageArray['po_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
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
                                                                        </div>
                                                                        <div class="row">
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
                                                                                    <label for="rawMaterialName" class="col-sm-4 col-form-label"><?=$languageArray['raw_material_code_code'][$language]?></label>
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
                                                                        <div class="row">
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2Display">
                                                                                <div class="row">
                                                                                    <label for="containerNo2" class="col-sm-4 col-form-label"><?=$languageArray['container_no2_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo2" name="containerNo2" placeholder="<?=$languageArray['container_no2_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2Display">
                                                                                <div class="row">
                                                                                    <label for="sealNo2" class="col-sm-4 col-form-label"><?=$languageArray['seal_no2_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="sealNo2" name="sealNo2" placeholder="<?=$languageArray['seal_no2_code'][$language]?>">
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
                                                                        <div class="row mb-3">
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
                                                                                    <!--div class="invalid-feedback">
                                                                                        Please fill in the field.
                                                                                    </div-->
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
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture2"><i class="mdi mdi-sync" type="button"></i></button>
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
                                                                                    <input type="number" class="form-control input-readonly" id="unitPrice" name="unitPrice" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="sstDisplay">
                                                                            <label for="sstPrice" class="col-sm-4 col-form-label"><?=$languageArray['sst_code'][$language]?> (6%)</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="sstPrice" name="sstPrice" placeholder="0" readonly>
                                                                                    <div class="input-group-text">RM</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="subTotalPriceDisplay">
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
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                                        <input type="hidden" id="grossWeightBy1" name="grossWeightBy1">
                                                        <input type="hidden" id="tareWeightBy1" name="tareWeightBy1">
                                                        <input type="hidden" id="grossWeightBy2" name="grossWeightBy2">
                                                        <input type="hidden" id="tareWeightBy2" name="tareWeightBy2">
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="prePrintModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="prePrintForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title"><?=$languageArray['language_code'][$language]?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <label for="prePrint" class="col-sm-4 col-form-label"><?=$languageArray['language_code'][$language]?></label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <div class="col-12">
                                                                        <select class="form-select select2" id="prePrint" name="prePrint" >
                                                                            <option value="en">English</option>
                                                                            <option value="zh">Chinese</option>
                                                                            <option value="my">Bahasa Malaysia</option>
                                                                            <option value="ne">नेपाली</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" class="form-control" id="isEmptyContainer" name="isEmptyContainer">
                                                        <input type="hidden" class="form-control" id="id" name="id">
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                        <button type="button" class="btn btn-success" id="submitPrePrint"><?=$languageArray['submit_code'][$language]?></button>
                                                    </div>
                                                </form>
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
                                                                <button type="button" id="exportPdf" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    <?=$languageArray['export_pdf_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-info waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    <?=$languageArray['export_excel_code'][$language]?>
                                                                </button>
                                                                <?php if ($role == 'SADMIN' || $role == 'ADMIN' || $role == 'MANAGER') { ?>
                                                                <button type="button" id="multiDeleteLorry" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <?php } ?>
                                                                <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['transaction_id_code'][$language]?></th>
                                                                    <th><?=$languageArray['weight_type_code'][$language]?></th>
                                                                    <th><?=$languageArray['weight_status_code'][$language]?></th>
                                                                    <th><?=$languageArray['customer_supplier_code'][$language]?></th>
                                                                    <th><?=$languageArray['container_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['seal_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_code'][$language]?></th>
                                                                    <th><?=$languageArray['gross_incoming_code'][$language]?></th>
                                                                    <th><?=$languageArray['incoming_date_code'][$language]?></th>
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
                                                                <h5 class="card-title mb-0 text-white"><?=$languageArray['pending_empty_container_records_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <?php if ($role == 'SADMIN' || $role == 'ADMIN' || $role == 'MANAGER') { ?>
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
    <script src="assets/js/weighing-calc.js"></script>

    <script type="text/javascript">
    var table = null;
    var emptyContainerTable = null;
    let clickTimer = null;

    var fromDateSearchPicker;
    var toDateSearchPicker;
    var grossIncomingDatePicker;
    var tareOutgoingDatePicker;
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2;
    var userRole = '<?=$role ?>';
    var ind = '<?=$indicator ?>';
    const isManager = userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER';
    // List rows carry the display label of the weight type, not the stored value
    const emptyContainerLabel = 'Primer Mover + Container';
    const num = WeighCalc.num;

    $(function () {
        const today = new Date();

        $('#collapseSearch .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
        });
        initModalSelect2();
        styleSelect2('body');

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
            allowInput: isManager,
            clickOpens: isManager,
        });

        grossIncomingDatePicker = dateTimePicker('#grossIncomingDate');
        tareOutgoingDatePicker = dateTimePicker('#tareOutgoingDate');
        grossIncomingDatePicker2 = dateTimePicker('#grossIncomingDate2');
        tareOutgoingDatePicker2 = dateTimePicker('#tareOutgoingDate2');

        validateForm('#weightForm');
        validateForm('#prePrintForm');
        validateForm('#cancelForm');

        // ---------- Search, summary cards and tables ----------

        $('#clearAllSearch').on('click', function () {
            fromDateSearchPicker.clear();
            toDateSearchPicker.clear();
            $('#statusSearch').val('-').trigger('change');
            $('#customerNoSearch').val('-').trigger('change');
            $('#supplierSearch').val('-').trigger('change');
            $('#vehicleNo').val('');
            $('#invoiceNoSearch').val('-').trigger('change');
            $('#batchNoSearch').val('N').trigger('change');
            $('#productSearch').val('-').trigger('change');
            $('#rawMatSearch').val('-').trigger('change');
            $('#plantSearch').val('-').trigger('change');
            $('#transactionIdSearch').val('');
            $('#containerNoSearch').val('');
            $('#sealNoSearch').val('');
            $('#invDelPoSearch').val('');
        });

        $('#plantSearchDisplay').toggle(isManager);

        // Purchase and Local filter by supplier / raw material, the others by customer / product
        $('#statusSearch').on('change', function () {
            var status = $(this).val();

            if (status == 'Purchase' || status == 'Local') {
                $('#customerSearchDisplay').hide();
                $('#customerSearchDisplay').find('#customerNoSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').show();
                $('#productSearchDisplay').find('#productSearch').val('-').trigger('change');
                $('#productSearchDisplay').hide();
                $('#rawMatSearchDisplay').show();
            } else {
                $('#supplierSearchDisplay').find('#supplierSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').hide();
                $('#customerSearchDisplay').show();
                $('#rawMatSearchDisplay').find('#rawMatSearch').val('-').trigger('change');
                $('#rawMatSearchDisplay').hide();
                $('#productSearchDisplay').show();
            }
        });

        $('#selectAllCheckbox').on('change', function () {
            $('#weightTable tbody input[type="checkbox"]').prop('checked', $(this).prop('checked')).trigger('change');
        });

        $('#selectAllContainerCheckbox').on('change', function () {
            $('#emptyContainerTable tbody input[type="checkbox"]').prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Summary card row: filter by its transaction type and status
        $('.status-item').on('click', function () {
            $('#statusSearch').val($(this).data('transaction')).trigger('change');
            $('#batchNoSearch').val($(this).data('status')).trigger('change');
            $('#filterSearch').click();
        });

        renderTable();

        $('#filterSearch').on('click', function () {
            renderTable();
        });

        // Single click expands the row details; the delay leaves room for a double click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this);
            var row = table.row(tr);
            if (!row.data()) {
                return;
            }

            var cell = $(e.target).closest('td');
            if (cell.hasClass('select-checkbox') || cell.hasClass('action-button') || row.data().weight_type == emptyContainerLabel) {
                return;
            }

            if (clickTimer) {
                clearTimeout(clickTimer);
                clickTimer = null;
            }

            clickTimer = setTimeout(function () {
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    $.post('php/modules/weight/api.php', {action: 'get', userID: row.data().id, format: 'EXPANDABLE'}, function (data) {
                        var obj = JSON.parse(data);
                        if (obj.status === 'success') {
                            row.child(format(obj.message)).show();
                            tr.addClass("shown");
                        }
                    });
                }

                clickTimer = null;
            }, 250);
        });

        $('#weightTable tbody').on('dblclick', 'tr', function () {
            if (clickTimer) {
                clearTimeout(clickTimer);
                clickTimer = null;
            }

            var row = table.row(this);
            edit(row.data().id, row.data().weight_type == 'Empty Container' ? 'Y' : 'N');
        });

        // ---------- Save, print and cancel ----------

        $('#submitWeight').on('click', function () {
            markEmptySelect2();

            if (!$('#weightForm').valid()) {
                return;
            }

            $('#spinnerLoading').show();
            $.post('php/modules/weight/api.php', $('#weightForm').serialize() + '&action=save', function (data) {
                var obj = JSON.parse(data);
                if (obj.status === 'success') {
                    table.ajax.reload();
                    window.location = 'index.php';
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    toastSuccess(obj.message);
                    return;
                }

                $('#spinnerLoading').hide();
                alert(obj.message);
                toastFail(obj.status === 'failed' ? obj.message : 'Failed to save');
            });
        });

        $('#submitWeightPrint').on('click', function () {
            var isEmptyContainer = $('#weightType').val() == 'Empty Container' ? 'Y' : 'N';

            if (!$('#weightForm').valid()) {
                return;
            }

            $('#spinnerLoading').show();
            $.post('php/modules/weight/api.php', $('#weightForm').serialize() + '&action=save', function (data) {
                var obj = JSON.parse(data);
                $('#spinnerLoading').hide();

                if (obj.status !== 'success') {
                    toastFail(obj.status === 'failed' ? obj.message : 'Failed to save');
                    return;
                }

                $('#addModal').modal('hide');
                toastSuccess(obj.message);
                $('#prePrintModal').find('#id').val(obj.id);
                $('#prePrintModal').find('#isEmptyContainer').val(isEmptyContainer);
                $('#prePrintModal').find('#prePrint').val("<?=$language ?>");
                $("#prePrintModal").modal("show");
            });
        });

        $('#submitPrePrint').on('click', function () {
            if (!$('#prePrintForm').valid()) {
                return;
            }

            $('#spinnerLoading').show();
            var request = {
                action: 'print',
                userID: $('#prePrintModal').find('#id').val(),
                file: 'weight',
                prePrint: $('#prePrintModal').find('#prePrint').val(),
                isEmptyContainer: $('#prePrintModal').find('#isEmptyContainer').val()
            };

            $.post('php/modules/weight/api.php', request, function (data) {
                var obj = JSON.parse(data);

                if (obj.status === 'success') {
                    openPrintWindow(obj.message);
                    $("#prePrintModal").modal("hide");
                    $('#spinnerLoading').hide();
                } else {
                    toastFail(obj.status === 'failed' ? obj.message : "Something wrong when print");
                }
            });
        });

        $('#submitCancel').on('click', function () {
            if (!$('#cancelForm').valid()) {
                return;
            }

            $('#spinnerLoading').show();
            $.post('php/modules/weight/api.php', $('#cancelForm').serialize() + '&action=delete', function (data) {
                var obj = JSON.parse(data);

                if (obj.status !== 'success') {
                    failResponse(obj);
                    return;
                }

                table.ajax.reload();
                emptyContainerTable.ajax.reload();
                $('#spinnerLoading').hide();
                $('#cancelModal').modal('hide');
                toastSuccess(obj.message);
            });
        });

        // ---------- Weighing indicator ----------

        $.post('http://127.0.0.1:5002/', $('#setupForm').serialize(), function (data) {
            if (data == "true") {
                $('#indicatorConnected').addClass('bg-primary');
                $('#checkingConnection').removeClass('bg-danger');
            } else {
                $('#indicatorConnected').removeClass('bg-primary');
                $('#checkingConnection').addClass('bg-danger');
            }
        });

        setInterval(function () {
            $.post('http://127.0.0.1:5002/handshaking', function (data) {
                if (data == "Error") {
                    $('#indicatorWeight').html('0');
                    $('#indicatorConnected').removeClass('bg-primary');
                    $('#checkingConnection').addClass('bg-danger');
                    return;
                }

                console.log("Data Received:" + data);

                var reading = WeighCalc.parseIndicatorReading(ind, data);
                if (reading !== null) {
                    $('#indicatorWeight').html(reading);
                    $('#indicatorConnected').addClass('bg-primary');
                    $('#checkingConnection').removeClass('bg-danger');
                }
            });
        }, 500);

        // ---------- Add new weighing ----------

        // Statement order matters here: several fields trigger change handlers that read or reset other fields
        $('#addWeight').on('click', function () {
            var $modal = $('#addModal');

            $modal.find('#grossCapture').show();
            $modal.find('#tareCapture').show();
            $modal.find('#id').val("");
            $modal.find('#currentWeight').text("0");
            $modal.find('#transactionId').val("");
            $modal.find('#transactionStatus').val("Sales").trigger('change');
            $modal.find('#emptyContainerNo').val("").trigger('change');
            $modal.find('#weightType').val("Normal").trigger('change');
            $modal.find('#customerType').val("Normal").trigger('change');
            $modal.find('#transactionDate').val(formatDate2(today));
            $modal.find('#vehiclePlateNo1').val("").trigger('change');
            $modal.find('#vehiclePlateNo2').val("").trigger('change');
            $modal.find('#supplierWeight').val("");
            $modal.find('#customerCode').val("");
            $modal.find('#customerName').val("-").trigger('change');
            $modal.find('#supplierCode').val("");
            $modal.find('#supplierName').val("-").trigger('change');
            $modal.find('#productCode').val("");
            $modal.find('#productName').val("-").trigger('change');
            $modal.find('#rawMaterialCode').val("");
            $modal.find('#rawMaterialName').val("-").trigger('change');
            $modal.find('#plantCode').val("");
            $modal.find('#sealNo').val("");
            $modal.find('#invoiceNo').val("");
            $modal.find('#purchaseOrder').val("").trigger('change');
            $modal.find('#salesOrder').val("").trigger('change');
            $modal.find('#deliveryNo').val("");
            $modal.find('#transporterCode').val("");
            $modal.find('#transporter').val("-").trigger('change');
            $modal.find('#destinationCode').val("");
            $modal.find('#plantCode').val("");
            $modal.find('#plant').val("<?=$plantName ?>").trigger('change');
            $modal.find('#destination').val("-").trigger('change');
            $modal.find('#replacementContainer').val('').trigger('keyup');
            $modal.find('#otherRemarks').val("");
            $modal.find('#manualVehicle').prop('checked', false).trigger('change');
            $modal.find('#manualVehicle2').prop('checked', false).trigger('change');
            $modal.find('#grossIncoming').val("");
            grossIncomingDatePicker.clear();
            $modal.find('#tareOutgoing').val("");
            tareOutgoingDatePicker.clear();
            $modal.find('#nettWeight').val("");
            $modal.find('#vehicleWeight2').val("");
            $modal.find('#emptyContainerWeight2').val("");
            $modal.find('#grossIncoming2').val("");
            $modal.find('#status').val("");
            grossIncomingDatePicker2.clear();
            $modal.find('#tareOutgoing2').val("");
            tareOutgoingDatePicker2.clear();
            $modal.find('#nettWeight2').val("");
            $modal.find('#reduceWeight').val("");
            $modal.find('#weightDifference').val("");
            $modal.find('#weightDifferencePerc').val("");
            $modal.find('#manualWeightNo').trigger('click');
            $modal.find('#weighbridge').val("");
            $modal.find('#productDescription').val("");
            $modal.find('#productHigh').val("");
            $modal.find('#productLow').val("");
            $modal.find('#productVariance').val("");
            $modal.find('#orderWeight').val("0");
            $modal.find('#unitPrice').val("0.00");
            $modal.find('#subTotalPrice').val("0.00");
            $modal.find('#sstPrice').val("0.00");
            $modal.find('#totalPrice').val("0.00");
            $modal.find('#finalWeight').val("");
            $modal.find('#balance').val("");
            $modal.find('#insufficientBalDisplay').hide();
            $modal.find('#containerNoInput').val("");
            $modal.find('#containerNo').val("");
            $modal.find('#containerNo2').val("");
            $modal.find('#sealNo2').val("");

            // Show the order selects, hide the read-only order inputs
            $modal.find('#salesOrderEdit').val("").hide();
            $modal.find('#purchaseOrderEdit').val("").hide();
            $modal.find('#salesOrder').next('.select2-container').show();

            $('#addModal .is-invalid').removeClass('is-invalid');
            clearSelect2Errors();

            $modal.modal('show');
        });

        // ---------- Export and bulk cancel ----------

        $('#exportPdf').on('click', function () {
            var filters = searchFilters();
            var ids = checkedIds('#weightTable', (type) => type == 'Lorry');
            var request = {
                fromDate: filters.fromDate,
                toDate: filters.fromDate,
                transactionStatus: filters.status,
                customer: filters.customer,
                supplier: filters.supplier,
                vehicle: filters.vehicle,
                weighingType: filters.invoice,
                status: WeighCalc.batchLabel(filters.batch),
                product: filters.product,
                rawMat: filters.rawMaterial,
                plant: filters.plant,
                isMulti: ids.length > 0 ? 'Y' : 'N',
                file: 'weight'
            };
            if (ids.length > 0) {
                request.ids = ids;
            }

            $.post('php/modules/weight/api.php?action=exportPdf', request, function (response) {
                var obj = JSON.parse(response);

                if (obj.status === 'success') {
                    openPrintWindow(obj.message);
                } else if (obj.status === 'failed') {
                    toastr["error"](obj.message, "Failed:");
                } else {
                    toastr["error"]("Something wrong when activate", "Failed:");
                }
            }).fail(function (error) {
                console.error("Error exporting PDF:", error);
                alert("An error occurred while generating the PDF.");
            });
        });

        $('#exportExcel').on('click', function () {
            var filters = searchFilters();
            var ids = checkedIds('#weightTable', (type) => type == 'Lorry');
            var url = "php/modules/weight/api.php?action=exportExcel&fromDate=" + filters.fromDate + "&toDate=" + filters.toDate +
                "&transactionStatus=" + filters.status + "&customer=" + filters.customer + "&supplier=" + filters.supplier + "&vehicle=" + filters.vehicle +
                "&weighingType=" + filters.invoice + "&product=" + filters.product + "&rawMat=" + filters.rawMaterial + "&plant=" + filters.plant +
                "&status=" + WeighCalc.batchLabel(filters.batch);

            window.open(url + (ids.length > 0 ? "&isMulti=Y&ids=" + ids : "&isMulti=N"));
        });

        $('#multiDeleteLorry').on('click', function () {
            var lorryIds = checkedIds('#weightTable', (type) => type == 'Lorry');
            var emptyContainerIds = checkedIds('#weightTable', (type) => type != 'Lorry');

            if (lorryIds.length == 0 && emptyContainerIds.length == 0) {
                alert("Please select at least one weighing record to delete.");
                return;
            }

            if (confirm('Are you sure you want to cancel these weighing records?')) {
                $('#cancelModal').find('#id').val(lorryIds);
                $('#cancelModal').find('#containerId').val(emptyContainerIds);
                $('#cancelModal').find('#isEmptyContainer').val('N');
                $('#cancelModal').find('#isMulti').val('Y');
                $('#cancelModal').modal('show');
            }
        });

        $('#multiDeleteContainer').on('click', function () {
            var ids = checkedIds('#emptyContainerTable');

            if (ids.length == 0) {
                alert("Please select at least one weighing record to delete.");
                return;
            }

            if (confirm('Are you sure you want to cancel these weighing records?')) {
                $('#cancelModal').find('#id').val(ids);
                $('#cancelModal').find('#isEmptyContainer').val('Y');
                $('#cancelModal').find('#isMulti').val('Y');
                $('#cancelModal').modal('show');
            }
        });

        // ---------- Weighing form ----------

        $('#weightType').on('change', function () {
            var weightType = $(this).val();
            var isDifferentContainer = weightType == 'Different Container';
            // Container and Different Container weigh against a pending empty container picked from the list
            var picksContainer = weightType == 'Container' || isDifferentContainer;
            var $modal = $('#addModal');

            if (picksContainer) {
                loadContainerData();
            }
            handleWeightType(weightType);

            $modal.find('#containerNo1Label').text(isDifferentContainer ? "<?= $languageArray['pending_bin_code'][$language] ?>" : "Container No 1");
            $modal.find('#emptyContainerDisplay').toggle(picksContainer);
            $modal.find('#containerDisplay').toggle(!picksContainer);
            $modal.find('#replacementContainerDisplay, #vehicleWeight2Display, #container2WeightDisplay, #containerNo2ReplaceDisplay, #sealNoReplaceDisplay, #sealNo2ReplaceDisplay').toggle(isDifferentContainer);
            $modal.find('#containerNo2Display, #sealNoDisplay, #sealNo2Display').toggle(!isDifferentContainer);
            $modal.find('#containerNoInput').attr('required', weightType == 'Empty Container');
            $modal.find('#emptyContainerNo').attr('required', picksContainer);
        });

        $('#replacementContainer').on('keyup', function () {
            $('#replaceContainerText').text($(this).val());
        });

        $('#manualVehicle').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $('#vehiclePlateNo1').val('-').trigger('change');
                $('.index-vehicle').hide();
                $('#vehicleNoTxt').show();
            } else {
                $(this).val(0);
                $('#vehicleNoTxt').hide();
                $('#vehicleNoTxt').val('');
                $('.index-vehicle').show();
            }
        });

        $('#vehicleNoTxt').on('keyup', function () {
            var plate = upperCaseValue(this);
            if (plate) {
                fillPartyFromVehicle(plate);
            }
        });

        $('#vehiclePlateNo1').on('change', function () {
            if ($(this).val()) {
                fillPartyFromVehicle($(this).val());
            }
        });

        $('#manualVehicle2').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $('#vehiclePlateNo2').val('-');
                $('.index-vehicle2').hide();
                $('#vehicleNoTxt2').show();
            } else {
                $(this).val(0);
                $('#vehicleNoTxt2').hide();
                $('#vehicleNoTxt2').val('');
                $('.index-vehicle2').show();
            }
        });

        $('#vehicleNoTxt2').on('keyup', function () {
            var plate = upperCaseValue(this);
            if ($('#weightType').val() == 'Different Container' && plate) {
                fillVehicle2Weight(plate);
            }
        });

        $('#vehiclePlateNo2').on('change', function () {
            var plate = $(this).val();
            if ($('#weightType').val() == 'Different Container' && plate) {
                fillVehicle2Weight(plate);
            }
        });

        $('.radio-manual-weight').on('click', function () {
            var weightFields = $('#grossIncoming, #tareOutgoing, #grossIncoming2, #tareOutgoing2');

            if ($('input[name="manualWeight"]:checked').val() == "true") {
                weightFields.removeAttr('readonly');
            } else {
                weightFields.attr('readonly', 'readonly');
            }
        });

        $('#grossIncoming').on('keyup', function () {
            stampWeighing(firstNett(), '#nettWeight', '#grossWeightBy1', grossIncomingDatePicker, '#grossIncomingDate');
        });

        $('#tareOutgoing').on('keyup', function () {
            stampWeighing(firstNett(), '#nettWeight', '#tareWeightBy1', tareOutgoingDatePicker, '#tareOutgoingDate');
        });

        $('#grossIncoming2').on('keyup', function () {
            stampWeighing(secondNett(), '#nettWeight2', '#grossWeightBy2', grossIncomingDatePicker2, '#grossIncomingDate2');
        });

        $('#tareOutgoing2').on('keyup', function () {
            stampWeighing(secondNett(), '#nettWeight2', '#tareWeightBy2', tareOutgoingDatePicker2, '#tareOutgoingDate2');
        });

        $('#grossCapture').on('click', captureInto('#grossIncoming'));
        $('#tareCapture').on('click', captureInto('#tareOutgoing'));
        $('#grossCapture2').on('click', captureInto('#grossIncoming2'));
        $('#tareCapture2').on('click', captureInto('#tareOutgoing2'));

        $('#nettWeight, #nettWeight2').on('change', function () {
            showCurrentWeight(currentFromNetts());
            $('#reduceWeight').trigger('change');
        });

        $('#reduceWeight').on('change', function () {
            showCurrentWeight(WeighCalc.finalWeight(currentFromNetts(), num($(this).val())));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#finalWeight').on('change', function () {
            var final = num($(this).val());
            var target = isSupplierTransaction($('#transactionStatus').val())
                ? parseFloat($('#addModal').find('#supplierWeight').val())
                : parseFloat($('#addModal').find('#orderWeight').val());
            var difference = final - target;

            $('#weightDifference').val(difference.toFixed(0));
            $('#weightDifferencePerc').val(((difference / parseFloat($(this).val())) * 100).toFixed(2));
        });

        // Order (Sales) and supplier (Purchase / Local) weights: difference, balance and variance %
        $('#orderWeight, #supplierWeight').on('change', function () {
            var difference = num($('#finalWeight').val()) - num($(this).val());
            $('#weightDifference').val(difference.toFixed(0));

            if ($('#addModal').find('#previousRecordsTag').val() == 'false') {
                $('#addModal').find('#balance').val($(this).val());
                $('#addModal').find('#insufficientBalDisplay').toggle(!($(this).val() <= 0));
            }

            $('#weightDifferencePerc').val(((difference / parseFloat($(this).val())) * 100).toFixed(2));
        });

        $('#currentWeight').on('change', function () {
            updatePrices();
        });

        $('#transactionStatus').on('change', function () {
            var status = $(this).val();

            if ($('#addModal').find('#weightType').val() == 'Container') {
                loadContainerData();
            }

            if (isSupplierTransaction(status)) {
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
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text(status == "Purchase" ? 'Purchase Order' : 'Sale Order');
            } else {
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
                $('#addModal').find('#divPoSupplyWeight').hide();
            }
        });

        $('#productName').on('change', function () {
            var selected = $('#productName :selected');
            $('#productCode').val(selected.data('code'));
            $('#productDescription').val(selected.data('description'));
            $('#productHigh').val(selected.data('high'));
            $('#productLow').val(selected.data('low'));
            $('#productVariance').val(selected.data('variance'));
            $('#unitPrice').val(updatePrices().price);
        });

        // Keep each hidden code field in step with its selected name
        $.each({
            supplierName: 'supplierCode',
            transporter: 'transporterCode',
            destination: 'destinationCode',
            plant: 'plantCode',
            customerName: 'customerCode',
            rawMaterialName: 'rawMaterialCode'
        }, function (select, codeField) {
            $('#' + select).on('change', function () {
                $('#' + codeField).val($('#' + select + ' :selected').data('code'));
            });
        });

        // Picking a pending empty container fills in its first weighing
        $('#emptyContainerNo').on('change', function () {
            var emptyContainerNo = $(this).val();
            var weightType = $('#weightType').val();
            var $modal = $('#addModal');
            $('#containerNo').val(emptyContainerNo);

            if (!emptyContainerNo || emptyContainerNo == '-') {
                $modal.find('#manualVehicle').prop('checked', false).trigger('change');
                $modal.find('#grossIncoming').val(0);
                $modal.find('#grossIncomingDate').val("");
                $modal.find('#tareOutgoing').val(0);
                $modal.find('#tareOutgoingDate').val("");
                $modal.find('#nettWeight').val(0);
                $('#tareOutgoing2').trigger('keyup');
                $('#normalCard').hide();
                $('#grossCapture').show();
                $('#tareCapture').show();
                return;
            }

            $.post('php/modules/weight/api.php', {action: 'emptyContainer', userID: emptyContainerNo}, function (data) {
                var obj = JSON.parse(data);
                if (obj.status != 'success') {
                    failResponse(obj);
                    return;
                }

                var container = obj.message;
                $modal.find('#invoiceNo').val(container.invoice_no);
                $modal.find('#deliveryNo').val(container.delivery_no);
                $modal.find('#purchaseOrder').val(container.purchase_order);
                $modal.find('#sealNo').val(container.seal_no);

                if (weightType != 'Different Container') {
                    $modal.find('#containerNo2').val(container.container_no2);
                    $modal.find('#sealNo2').val(container.seal_no2);
                }

                if (container.transaction_status == 'Sales' || container.transaction_status == 'Misc') {
                    $modal.find('#customerName').val(container.customer_name).trigger('change');
                    $modal.find('#productName').val(container.product_name).trigger('change');
                } else {
                    $modal.find('#supplierName').val(container.supplier_name).trigger('change');
                    $modal.find('#rawMaterialName').val(container.raw_mat_name).trigger('change');
                }
                $modal.find('#plant').val(container.plant_name).trigger('change');
                $modal.find('#transporter').val(container.transporter).trigger('change');
                $modal.find('#destination').val(container.destination).trigger('change');

                $modal.find('#vehiclePlateNo1').val(container.lorry_plate_no1).trigger('change');
                $modal.find('#grossIncoming').val(container.gross_weight1);
                grossIncomingDatePicker.setDate(new Date(container.gross_weight1_date));
                $modal.find('#grossWeightBy1').val(container.gross_weight_by1);
                $modal.find('#tareOutgoing').val(container.tare_weight1);
                tareOutgoingDatePicker.setDate(new Date(container.tare_weight1_date));
                $modal.find('#tareWeightBy1').val(container.tare_weight_by1);
                $modal.find('#nettWeight').val(container.nett_weight1);

                if (container.vehicleNoTxt != null) {
                    $modal.find('#vehicleNoTxt').val(container.vehicleNoTxt);
                    $('#manualVehicle').val(1);
                    $('#manualVehicle').prop("checked", true);
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                } else {
                    $modal.find('#vehiclePlateNo1').val(container.lorry_plate_no1).trigger('change');
                    $('#manualVehicle').val(0);
                    $('#manualVehicle').prop("checked", false);
                    $('.index-vehicle').show();
                    $('#vehicleNoTxt').hide();
                }

                $('#tareOutgoing2').trigger('keyup');

                $('#normalCard').show();
                $('#grossCapture').hide();
                $('#tareCapture').hide();
            });
        });

        $('#containerNoInput').on('keyup', function () {
            $('#containerNo').val(upperCaseValue(this));
        });

        $('#containerNoInput').on('change', function () {
            $('#containerNo').val($(this).val());
        });

        $('#sealNo, #containerNo2, #sealNo2, #containerNoSearch, #sealNoSearch').on('keyup', function () {
            upperCaseValue(this);
        });

        <?php
            if(isset($_GET['weight'])){
                echo 'edit('.$_GET['weight'].');';
            }
        ?>
    });

    // ---------- Page helpers ----------

    function toastSuccess(message) {
        $("#successBtn").attr('data-toast-text', message);
        $("#successBtn").click();
    }

    function toastFail(message) {
        $("#failBtn").attr('data-toast-text', message);
        $("#failBtn").click();
    }

    // Failure arm shared by most requests: hide the spinner and show the server message
    function failResponse(obj) {
        $('#spinnerLoading').hide();
        toastFail(obj.message);
    }

    function validateForm(selector) {
        $(selector).validate({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    }

    function initModalSelect2() {
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });
    }

    function styleSelect2(scope) {
        $(scope + ' .select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $(scope + ' .select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });
    }

    // Marks empty required Select2 fields in the weighing form. It does not block the save.
    function markEmptySelect2() {
        $('#addModal .select2[required]').each(function () {
            var field = $(this);
            var container = field.next('.select2-container');

            if (field.val() === "" || field.val() === null) {
                container.find('.select2-selection').css('border', '1px solid red');
                if (container.next('.select2-error').length === 0) {
                    container.after("<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>");
                }
            } else {
                container.find('.select2-selection').css('border', '');
                container.next('.select2-error').remove();
            }
        });
    }

    function clearSelect2Errors() {
        $('#addModal .select2[required]').each(function () {
            var container = $(this).next('.select2-container');
            container.find('.select2-selection').css('border', '');
            container.next('.select2-error').remove();
        });
    }

    function dateTimePicker(selector) {
        return $(selector).flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: isManager,
            clickOpens: isManager,
        });
    }

    function openPrintWindow(html) {
        var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
        printWindow.document.write(html);
        printWindow.document.close();
        setTimeout(function () {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    // Values of the ticked row checkboxes, optionally only those whose data-type passes the filter
    function checkedIds(tableSelector, typeFilter) {
        var ids = [];
        $(tableSelector + " tbody input[type='checkbox']").each(function () {
            if (this.checked && (!typeFilter || typeFilter($(this).data('type')))) {
                ids.push($(this).val());
            }
        });
        return ids;
    }

    // Search bar values; each request maps them to its own parameter names
    function searchFilters() {
        var value = function (selector) {
            return $(selector).val() ? $(selector).val() : '';
        };

        return {
            fromDate: $('#fromDateSearch').val(),
            toDate: $('#toDateSearch').val(),
            status: value('#statusSearch'),
            customer: value('#customerNoSearch'),
            supplier: value('#supplierSearch'),
            vehicle: value('#vehicleNo'),
            invoice: value('#invoiceNoSearch'),
            batch: value('#batchNoSearch'),
            product: value('#productSearch'),
            rawMaterial: value('#rawMatSearch'),
            plant: value('#plantSearch'),
            transactionId: value('#transactionIdSearch'),
            containerNo: value('#containerNoSearch'),
            sealNo: value('#sealNoSearch'),
            invDelPo: value('#invDelPoSearch')
        };
    }

    function upperCaseValue(input) {
        var value = $(input).val().toUpperCase();
        $(input).val(value);
        return value;
    }

    function isSupplierTransaction(status) {
        return status == "Purchase" || status == "Local";
    }

    // ---------- Weight calculations on the form ----------

    function firstNett() {
        return WeighCalc.nett(num($('#grossIncoming').val()), num($('#tareOutgoing').val()));
    }

    // Different Container also fills in the second empty container weight
    function secondNett() {
        var gross2 = num($('#grossIncoming2').val());
        var tare2 = num($('#tareOutgoing2').val());

        if ($('#weightType').val() != 'Different Container') {
            return WeighCalc.nett(gross2, tare2);
        }

        var vehicle2 = num($('#vehicleWeight2').val());
        $('#emptyContainerWeight2').val(WeighCalc.emptyContainer2(gross2, vehicle2));
        return WeighCalc.differentContainerNett(tare2, vehicle2, num($('#nettWeight').val()));
    }

    // After a gross or tare entry: show the nett, then record who weighed and when
    function stampWeighing(nett, nettField, weighedByField, datePicker, dateField) {
        $(nettField).val(nett.toFixed(0));
        $(nettField).trigger('change');
        $(weighedByField).val('<?php echo $username; ?>');
        datePicker.setDate(new Date());
        $(dateField).trigger('change');
    }

    // Click handler that copies the live indicator reading into a weight field
    function captureInto(weightField) {
        return function (event) {
            event.preventDefault();
            $(weightField).val(parseFloat($('#indicatorWeight').text()).toFixed(0));
            $(weightField).trigger('keyup');
        };
    }

    function currentFromNetts() {
        return WeighCalc.currentWeight($('#weightType').val(), num($('#nettWeight').val()), num($('#nettWeight2').val()));
    }

    function showCurrentWeight(weight) {
        $('#currentWeight').text(weight.toFixed(0));
        $('#finalWeight').val(weight.toFixed(0));
    }

    function updatePrices() {
        var prices = WeighCalc.prices($('#unitPrice').val(), $('#currentWeight').text());
        $('#subTotalPrice').val(prices.subTotal.toFixed(2));
        $('#sstPrice').val(prices.sst.toFixed(2));
        $('#totalPrice').val(prices.total.toFixed(2));
        return prices;
    }

    // ---------- Lookups ----------

    function pullVehicle(plate, onSuccess) {
        $.post('php/modules/vehicle/api.php', {action: 'pullCustomer', userID: plate}, function (data) {
            var obj = JSON.parse(data);

            if (obj.status == 'success') {
                onSuccess(obj.message);
            } else if (obj.status === 'error') {
                alert(obj.message);
                $('#vehicleNoTxt').val('');
            } else {
                failResponse(obj);
            }
        });
    }

    // Vehicle 1 brings its customer (Sales / Misc) or supplier (Purchase / Local)
    function fillPartyFromVehicle(plate) {
        var transactionStatus = $('#transactionStatus').val();

        pullVehicle(plate, function (vehicle) {
            if (transactionStatus == 'Sales' || transactionStatus == 'Misc') {
                $('#addModal').find('#customerName').val(vehicle.customer_name).trigger('change');
                $('#addModal').find('#customerCode').val(vehicle.customer_code);
            } else {
                $('#addModal').find('#supplierName').val(vehicle.supplier_name).trigger('change');
                $('#addModal').find('#supplierCode').val(vehicle.supplier_code);
            }
        });
    }

    function fillVehicle2Weight(plate) {
        pullVehicle(plate, function (vehicle) {
            $('#vehicleWeight2').val(vehicle.vehicle_weight);
        });
    }

    // Pending empty containers for the selected transaction status
    function loadContainerData(callback) {
        $.post('php/modules/weight/api.php', {action: 'containers', userID: $('#transactionStatus').val()}, function (data) {
            var obj = JSON.parse(data);
            if (obj.status != 'success') {
                failResponse(obj);
                return;
            }

            if (obj.message.length > 0) {
                var select = $('#addModal').find('#emptyContainerNo');
                select.empty();
                select.append('<option selected="-">-</option>');
                obj.message.forEach(function (container) {
                    select.append('<option value="' + container.container_no + '">' + container.container_no + '</option>');
                });
            }

            if (callback) {
                callback();
            }
        });
    }

    // ---------- Tables ----------

    function renderSummary(summary) {
        var prefixes = { Sales: 'sales', Purchase: 'purchase', Local: 'local', Misc: 'misc' };
        var suffixes = { Pending: 'Pending', Complete: 'Complete', Cancelled: 'Cancel' };
        $.each(summary, function (type, counts) {
            $.each(counts, function (progress, total) {
                $('#' + prefixes[type] + suffixes[progress]).text(total);
            });
        });
    }

    function actionButton(title, icon, colour, idPrefix, id, onclick) {
        return `
            <div class="col-auto">
                <button title="${title}" type="button" id="${idPrefix}${id}" onclick="${onclick}" class="btn ${colour} btn-sm">
                    <i class="${icon}"></i>
                </button>
            </div>`;
    }

    // Managers get Edit; other roles get Weight Out while the record is not complete
    function editButton(id, isContainer, isComplete) {
        if (isManager) {
            return actionButton('Edit', 'fas fa-pen', 'btn-warning', 'edit', id, `edit(${id}, '${isContainer}')`);
        }
        if (isComplete != 'Y') {
            return actionButton('Weight Out', 'fa-solid fa-weight-hanging', 'btn-warning', 'edit', id, `edit(${id}, '${isContainer}')`);
        }
        return '';
    }

    function deleteButton(id, isContainer) {
        return isManager ? actionButton('Delete', 'fa fa-times', 'btn-danger', 'delete', id, `deactivate(${id}, '${isContainer}')`) : '';
    }

    function serverTable(selector, data, columns, drawCallback) {
        return $(selector).DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'php/modules/weight/api.php',
                'data': data
            },
            'columns': columns,
            "drawCallback": drawCallback
        });
    }

    function renderTable() {
        var filters = searchFilters();

        if ($.fn.DataTable.isDataTable('#weightTable')) {
            $("#weightTable").DataTable().clear().destroy();
        }
        if ($.fn.DataTable.isDataTable('#emptyContainerTable')) {
            $("#emptyContainerTable").DataTable().clear().destroy();
        }

        table = serverTable('#weightTable', $.extend({ action: 'list' }, filters), [
            {
                data: 'id',
                className: 'select-checkbox',
                orderable: false,
                render: function (data, type, row) {
                    var rowType = row.weight_type == emptyContainerLabel ? 'Empty Container' : 'Lorry';
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="' + data + '" data-type="' + rowType + '"/>';
                }
            },
            { data: 'transaction_id' },
            { data: 'weight_type' },
            { data: 'transaction_status' },
            { data: 'customer' },
            { data: 'container_no' },
            { data: 'seal_no' },
            { data: 'lorry_plate_no1' },
            { data: 'gross_weight1' },
            { data: 'gross_weight1_date' },
            { data: 'tare_weight1' },
            { data: 'tare_weight1_date' },
            { data: 'nett_weight1' },
            { data: 'lorry_plate_no2' },
            { data: 'gross_weight2' },
            { data: 'gross_weight2_date' },
            { data: 'tare_weight2' },
            { data: 'tare_weight2_date' },
            { data: 'nett_weight2' },
            {
                data: 'id',
                class: 'action-button',
                render: function (data, type, row) {
                    var isContainer = row.weight_type == emptyContainerLabel ? 'Y' : 'N';
                    var buttons = editButton(data, isContainer, row.is_complete);

                    if (isContainer == 'N') {
                        buttons += actionButton('Print', 'fas fa-print', 'btn-info', 'print', data, `print('${data}', '${row.transaction_status}')`);
                    }

                    return `<div class="row g-1 d-flex">` + buttons + deleteButton(data, isContainer) + `</div>`;
                }
            }
        ], function (settings) {
            renderSummary(settings.json.summary);
        });

        emptyContainerTable = serverTable('#emptyContainerTable', {
            action: 'listEmptyContainer',
            fromDate: filters.fromDate,
            toDate: filters.toDate
        }, [
            {
                data: 'id',
                className: 'select-checkbox',
                orderable: false,
                render: function (data) {
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="' + data + '"/>';
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
                    var buttons = row.is_complete != 'Y' ? editButton(data, 'Y', row.is_complete) : '';
                    buttons += actionButton('Print', 'fas fa-print', 'btn-info', 'print', data, `print('${data}', '${row.transaction_status}', 'Y')`);

                    return `<div class="row g-1 d-flex">` + buttons + deleteButton(data, 'Y') + `</div>`;
                }
            }
        ]);
    }

    // Expanded row details under a weighing record
    function format(row) {
        var transactionStatus = { Sales: 'Dispatch', Purchase: 'Receiving', Local: 'Internal Transfer' }[row.transaction_status] || 'Miscellaneous';
        var weightTypes = { Container: 'Primer Mover', 'Empty Container': 'Primer Mover + Container', Normal: 'Normal Weighing', 'Different Container': 'Primer Mover + Different Bins' };
        var weightType = weightTypes[row.weight_type] || row.weight_type;
        var productLabel = isSupplierTransaction(row.transaction_status) ? 'PURCHASE PRODUCT' : 'SALES PRODUCT';

        return `
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
                <p><strong>PLANT NAME:</strong> ${row.plant_name}</p>
                <p><strong>${productLabel}:</strong> ${row.product_rawmat_name}</p>
                <p><strong>PURCHASE ORDER:</strong> ${row.purchase_order}</p>
                <p><strong>CONTAINER NO:</strong> ${row.container_no}</p>
                <p><strong>CONTAINER NO 2:</strong> ${row.container_no2}</p>
            </div>
            <div class="col-6">
                <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>WEIGHT STATUS:</strong> ${transactionStatus}</p>
                <p><strong>WEIGHT TYPE:</strong> ${weightType}</p>
                <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
                <p><strong>SEAL NO:</strong> ${row.seal_no}</p>
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
    }

    // ---------- Weighing modal ----------

    // Container and Different Container start from a pending empty container (second weighing card);
    // Normal and Empty Container weigh on the first card
    function handleWeightType(weightType) {
        var $modal = $('#addModal');

        if (weightType == 'Container' || weightType == 'Different Container') {
            $modal.find('#manualVehicle').prop('checked', false).trigger('change');
            $modal.find('#grossIncoming').val(0);
            $modal.find('#grossIncomingDate').val("");
            $modal.find('#tareOutgoing').val(0);
            $modal.find('#tareOutgoingDate').val("");
            $modal.find('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#grossCapture').hide();
            $('#tareCapture').hide();
            $('#containerCard').show();
            return;
        }

        $modal.find('#manualVehicle2').prop('checked', false).trigger('change');
        $modal.find('#grossIncoming2').val(0);
        $modal.find('#grossIncomingDate2').val("");
        $modal.find('#tareOutgoing2').val(0);
        $modal.find('#tareOutgoingDate2').val("");
        $modal.find('#nettWeight2').val(0);
        $('#containerCard').hide();
        $('#grossCapture').show();
        $('#tareCapture').show();
        $('#normalCard').show();
    }

    // Statement order matters here: several fields trigger change handlers that read or reset other fields
    function edit(id, isContainer) {
        $('#spinnerLoading').show();

        $.post('php/modules/weight/api.php', {action: 'get', userID: id, type: isContainer == 'Y' ? 'Container' : 'Weight'}, function (data) {
            var obj = JSON.parse(data);
            if (obj.status !== 'success') {
                failResponse(obj);
                $('#spinnerLoading').hide();
                return;
            }

            var record = obj.message;
            var $modal = $('#addModal');

            // Completed records can no longer capture from the indicator
            $modal.find('#grossCapture').toggle(record.is_complete != 'Y');
            $modal.find('#tareCapture').toggle(record.is_complete != 'Y');

            $modal.find('#id').val(record.id);
            $modal.find('#transactionId').val(record.transaction_id);
            $modal.find('#transactionStatus').val(record.transaction_status).trigger('change');
            $modal.find('#weightType').val(record.weight_type).trigger('change');
            $modal.find('#customerType').val(record.customer_type).trigger('change');
            $modal.find('#transactionDate').val(formatDate2(new Date(record.transaction_date)));

            if (isSupplierTransaction(record.transaction_status)) {
                $('#divWeightDifference').show();
                $('#divSupplierWeight').show();
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
            } else {
                $('#divOrderWeight').show();
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
            }

            if (record.vehicleNoTxt != null) {
                $modal.find('#vehicleNoTxt').val(record.vehicleNoTxt);
                $('#manualVehicle').val(1);
                $('#manualVehicle').prop("checked", true);
                $('.index-vehicle').hide();
                $('#vehicleNoTxt').show();
            } else {
                $modal.find('#vehiclePlateNo1Edit').val('EDIT');
                $modal.find('#vehiclePlateNo1').val(record.lorry_plate_no1).select2('destroy').select2();
                $('#manualVehicle').val(0);
                $('#manualVehicle').prop("checked", false);
                $('.index-vehicle').show();
                $('#vehicleNoTxt').hide();
            }

            if (record.vehicleNoTxt2 != null) {
                $modal.find('#vehicleNoTxt2').val(record.vehicleNoTxt2);
                $('#manualVehicle2').val(1);
                $('#manualVehicle2').prop("checked", true);
                $('.index-vehicle2').hide();
                $('#vehicleNoTxt2').show();
            } else {
                $modal.find('#vehiclePlateNo2').val(record.lorry_plate_no2).select2('destroy').select2();
                $('#manualVehicle2').val(0);
                $('#manualVehicle2').prop("checked", false);
                $('.index-vehicle2').show();
                $('#vehicleNoTxt2').hide();
            }

            $modal.find('#productCode').val(record.product_code);
            $modal.find('#purchaseOrder').val(record.purchase_order);
            $modal.find('#invoiceNo').val(record.invoice_no);
            $modal.find('#deliveryNo').val(record.delivery_no);
            $modal.find('#transporterCode').val(record.transporter_code);
            $modal.find('#transporter').val(record.transporter).trigger('change');
            $modal.find('#customerName').val(record.customer_name).select2('destroy').select2();
            $modal.find('#customerCode').val(record.customer_code);
            $modal.find('#supplierName').val(record.supplier_name).select2('destroy').select2();
            $modal.find('#supplierCode').val(record.supplier_code);
            $modal.find('#rawMaterialCode').val(record.raw_mat_code);
            $modal.find('#rawMaterialName').val(record.raw_mat_name).trigger('change');
            $modal.find('#productName').val(record.product_name).trigger('change');
            $modal.find('#productCode').val(record.product_code);
            $modal.find('#supplierWeight').val(record.supplier_weight);
            $modal.find('#orderWeight').val(record.order_weight);
            $modal.find('#destinationCode').val(record.destination_code);
            $modal.find('#destination').val(record.destination).trigger('change');
            $modal.find('#plant').val(record.plant_name).trigger('change');
            $modal.find('#plantCode').val(record.plant_code);

            $modal.find('#otherRemarks').val(record.remarks);
            $modal.find('#grossIncoming').val(record.gross_weight1);
            grossIncomingDatePicker.setDate(new Date(record.gross_weight1_date));
            $modal.find('#grossWeightBy1').val(record.gross_weight_by1);
            $modal.find('#tareOutgoing').val(record.tare_weight1);
            tareOutgoingDatePicker.setDate(record.tare_weight1_date != null ? new Date(record.tare_weight1_date) : null);
            $modal.find('#tareWeightBy1').val(record.tare_weight_by1);
            $modal.find('#nettWeight').val(record.nett_weight1);
            $modal.find('#vehicleWeight2').val(record.lorry_no2_weight);
            $modal.find('#emptyContainerWeight2').val(record.empty_container2_weight);
            $modal.find('#replacementContainer').val(record.replacement_container).trigger('keyup');
            $modal.find('#grossIncoming2').val(record.gross_weight2);
            grossIncomingDatePicker2.setDate(record.gross_weight2_date != null ? new Date(record.gross_weight2_date) : null);
            $modal.find('#grossWeightBy2').val(record.gross_weight_by2);
            $modal.find('#tareOutgoing2').val(record.tare_weight2);
            tareOutgoingDatePicker2.setDate(record.tare_weight2_date != null ? new Date(record.tare_weight2_date) : null);
            $modal.find('#tareWeightBy2').val(record.tare_weight_by2);
            $modal.find('#nettWeight2').val(record.nett_weight2);
            $modal.find('#reduceWeight').val(record.reduce_weight);
            $modal.find('#weightDifference').val(record.weight_different);
            $modal.find('#weightDifferencePerc').val(record.weight_different_perc);
            $modal.find('#currentWeight').text(record.final_weight);

            var manualWeight = record.manual_weight == 'true';
            $("#manualWeightYes").prop("checked", manualWeight);
            $("#manualWeightNo").prop("checked", !manualWeight);
            $(manualWeight ? '#manualWeightYes' : '#manualWeightNo').trigger('click');

            $modal.find('#indicatorId').val(record.indicator_id);
            $modal.find('#weighbridge').val(record.weighbridge_id);
            $modal.find('#indicatorId2').val(record.indicator_id_2);
            $modal.find('#productDescription').val(record.product_description);
            $modal.find('#unitPrice').val(record.unit_price);
            $modal.find('#subTotalPrice').val(record.sub_total);
            $modal.find('#sstPrice').val(record.sst);
            $modal.find('#totalPrice').val(record.total_price);
            $modal.find('#finalWeight').val(record.final_weight);
            $modal.find('#containerNoInput').val(record.container_no);
            $modal.find('#containerNo').val(record.container_no);
            $modal.find('#containerNo2').val(record.container_no2);
            $modal.find('#sealNo').val(record.seal_no);
            $modal.find('#sealNo2').val(record.seal_no2);

            // A container weighing shows its empty container, even if it is no longer in the pending list
            if ((record.weight_type == 'Container' || record.weight_type == 'Different Container') && record.container_no) {
                loadContainerData(function () {
                    $('#normalCard').show();

                    var select = $modal.find('#emptyContainerNo');
                    var exists = select.find('option').filter(function () {
                        return $(this).val() === record.container_no;
                    }).length > 0;

                    if (!exists) {
                        select.append('<option value="' + record.container_no + '">' + record.container_no + '</option>');
                    }

                    select.val(record.container_no).select2('destroy').select2();
                    initModalSelect2();
                    styleSelect2('#addModal');
                });
            }

            initModalSelect2();
            styleSelect2('#addModal');

            $('#addModal .is-invalid').removeClass('is-invalid');
            clearSelect2Errors();

            $modal.modal('show');
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id, isEmptyContainer) {
        if (confirm('Are you sure you want to cancel this weighing record?')) {
            $('#cancelModal').find('#id').val(id);
            $('#cancelModal').find('#isEmptyContainer').val(isEmptyContainer);
            $('#cancelModal').modal('show');
        }
    }

    function print(id, transactionStatus, isEmptyContainer = 'N') {
        $('#prePrintModal').find('#id').val(id);
        $('#prePrintModal').find('#isEmptyContainer').val(isEmptyContainer);
        $('#prePrintModal').find('#prePrint').val("<?=$language ?>");
        $("#prePrintModal").modal("show");
    }
    </script>
</body>
</html>
