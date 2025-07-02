<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

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
$unit = $db->query("SELECT * FROM Unit WHERE status = '0' ORDER BY unit ASC");
$purchaseOrder = $db->query("SELECT * FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$salesOrder = $db->query("SELECT * FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$agent = $db->query("SELECT * FROM Agents WHERE status = '0' ORDER BY name ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$site = $db->query("SELECT * FROM Site WHERE status = '0' ORDER BY name ASC");
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

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
                                                            <label for="statusSearch" class="form-label">Transaction Status</label>
                                                            <select id="statusSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <option value="Sales">Dispatch</option>
                                                                <option value="Purchase">Receiving</option>
                                                                <option value="Local">Internal Transfer</option>
                                                                <option value="Misc">Miscellaneous</option>
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
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invoiceNoSearch" class="form-label">Weighing Type</label>
                                                            <select id="invoiceNoSearch" class="form-select select2"  >
                                                                <option selected>-</option>
                                                                <option value="Normal">Normal Weighing</option>
                                                                <option value="Container">Primer Mover</option>
                                                                <option value="Empty Container">Primer Mover + Container</option>
                                                            </select>
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
                                                    <div class="col-3" id="plantSearchDisplay" style="display:none">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
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
                                                            <label for="transactionIdSearch" class="form-label">Transaction ID</label>
                                                            <input type="text" class="form-control" id="transactionIdSearch" name="transactionIdSearch" placeholder="Transaction ID">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="containerNoSearch" class="form-label">Container No</label>
                                                            <input type="text" class="form-control" id="containerNoSearch" name="containerNoSearch" placeholder="Container No">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="sealNoSearch" class="form-label">Seal No</label>
                                                            <input type="text" class="form-control" id="sealNoSearch" name="sealNoSearch" placeholder="Seal No">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-success" id="filterSearch"><i class="bx bx-search-alt"></i> Search</button>
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
                                        Add New Weight</button> -->

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Entry</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-success">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h3 class="ff-secondary fw-semibold text-white">Indicator Weight</h3>
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
                                                                        <div class="card bg-success">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h3 class="ff-secondary fw-semibold text-white">Final Weight</h3>
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
                                                                                    <label for="transactionId" class="col-sm-4 col-form-label">Transaction ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" placeholder="Transaction ID" readonly>                                                                                  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">P/O No.</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                                <div class="row">
                                                                                    <label for="orderWeight" class="col-sm-4 col-form-label">Order Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="orderWeight" name="orderWeight"  placeholder="Order Weight">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierWeight" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierWeight" class="col-sm-4 col-form-label">Supplier Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="supplierWeight" name="supplierWeight"  placeholder="Supplier Weight">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weightType" class="col-sm-4 col-form-label">Weight Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="weightType" name="weightType" class="form-select select2">
                                                                                            <option value="Normal" selected>Normal Weighing</option>
                                                                                            <option value="Container">Primer Mover</option>
                                                                                            <option value="Empty Container">Primer Mover + Container</option>
                                                                                        </select>   
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="containerDisplay">
                                                                                    <label for="containerNoInput" class="col-sm-4 col-form-label">Container No 1</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNoInput" name="containerNoInput" placeholder="Container No">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="emptyContainerDisplay" style="display:none" >
                                                                                    <label for="emptyContainerNo" class="col-sm-4 col-form-label">Container No 1</label>
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                                            <option value="Sales" selected>Dispatch</option>
                                                                                            <option value="Purchase">Receiving</option>
                                                                                            <option value="Local">Internal Transfer</option>
                                                                                            <option value="Misc">Miscellaneous</option>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="sealNo" class="col-sm-4 col-form-label">Seal No 1</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="sealNo" name="sealNo" placeholder="Seal No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Reduce Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionDate" class="col-sm-4 col-form-label">Transaction Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="transactionDate" name="transactionDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="containerNo2" class="col-sm-4 col-form-label">Container No 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo2" name="containerNo2" placeholder="Container No 2">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="unitPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="unitPrice" class="col-sm-4 col-form-label">Unit Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="unitPrice" name="unitPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="sealNo2" class="col-sm-4 col-form-label">Seal No 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="sealNo2" name="sealNo2" placeholder="Seal No 2">
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sstDisplay">
                                                                                <div class="row">
                                                                                    <label for="sstPrice" class="col-sm-4 col-form-label">SST (6%)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="sstPrice" name="sstPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="doDisplay">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
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
                                                                                    <label for="supplierName" class="col-sm-4 col-form-label">Supplier Name</label>
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="subTotalPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="subTotalPrice" class="col-sm-4 col-form-label">Sub-Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="subTotalPrice" name="subTotalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3"  <?php 
                                                                                if($_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'ADMIN'){
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="productNameDisplay">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Code</label>
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
                                                                                    <label for="rawMaterialName" class="col-sm-4 col-form-label">Raw Material Code</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="rawMaterialName" name="rawMaterialName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                                                <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>           
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="totalPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="totalPrice" class="col-sm-4 col-form-label">Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="totalPrice" name="totalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant</label>
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
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
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
                                                                                    <label for="destination" class="col-sm-4 col-form-label">Destination</label>
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
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="agent" class="col-sm-4 col-form-label">Sales Representative</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="agent" name="agent" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowAgent=mysqli_fetch_assoc($agent)){ ?>
                                                                                                <option value="<?=$rowAgent['name'] ?>" data-code="<?=$rowAgent['agent_code'] ?>"><?=$rowAgent['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                         
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
                                                                        </div>
                                                                        <div class="row mb-3" id="noOfDrumDisplay" style="display:none;">
                                                                            <label for="noOfDrum" class="col-sm-4 col-form-label">No of Drum</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="number" class="form-control" id="noOfDrum" name="noOfDrum">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming" class="col-sm-4 col-form-label">Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manual" name="manual" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>                                                                                             -->
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate" readonly>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
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
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate" readonly>
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
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
                                                                            <label for="vehiclePlateNo2" class="col-sm-4 col-form-label">Vehicle Plate No 2</label>
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
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming2" class="col-sm-4 col-form-label">Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming2" name="grossIncoming2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture2"><i class="mdi mdi-sync" type="button"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing2" class="col-sm-4 col-form-label">Outgoing</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing2" name="tareOutgoing2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="tareCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2" readonly>
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="otherRemarks" class="col-sm-2 col-form-label">Other Remarks</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-success" id="submitWeightPrint">Submit & Print</button>
                                                                <button type="button" class="btn btn-primary" id="submitWeight">Submit</button>
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
                                                        <div class="col-xxl-4 col-lg-4 mb-3"  style="display:none;">
                                                            <div class="row">
                                                                <label for="exDel" class="col-sm-4 col-form-label">Ex-Quarry/Delivered</label>
                                                                <div class="col-sm-8">
                                                                    <div class="form-check align-radio mr-2">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualEx" value="true">
                                                                        <label class="form-check-label" for="manualEx">
                                                                        Ex-Quarry
                                                                        </label>
                                                                    </div>

                                                                    <div class="form-check align-radio">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualDel" value="false" checked>
                                                                        <label class="form-check-label" for="manualDel">
                                                                        Delivered
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="loadDrum" class="col-sm-4 col-form-label">By-Load/By-Drum</label>
                                                                <div class="col-sm-8">
                                                                    <div class="form-check align-radio mr-2">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualLoad" value="true" checked>
                                                                        <label class="form-check-label" for="manualLoad">
                                                                        By-Load
                                                                        </label>
                                                                    </div>

                                                                    <div class="form-check align-radio">
                                                                        <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualDrum" value="false">
                                                                        <label class="form-check-label" for="manualDrum">
                                                                        By-Drum
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="siteName" class="col-sm-4 col-form-label">Project</label>
                                                                <div class="col-sm-8">
                                                                    <select class="form-select select2" id="siteName" name="siteName">
                                                                        <option selected="-">-</option>
                                                                        <?php while($rowSite=mysqli_fetch_assoc($site)){ ?>
                                                                            <option value="<?=$rowSite['name'] ?>" data-code="<?=$rowSite['site_code'] ?>"><?=$rowSite['name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>        
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
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="bypassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="bypassForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Key in reasons</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-12">
                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Password</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="passcode" name="passcode" placeholder="0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-xxl-12 col-lg-12 mb-12">
                                                            <div class="row">
                                                                <label for="reason" class="col-sm-2 col-form-label">Reasons *</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reasons" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-success" id="submitBypass">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="approvalForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Key in reasons</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="id" name="id"/>
                                                        <div class="row  col-xxl-12 col-lg-12 mb-1">
                                                            <div class="row">
                                                                <label for="statusA" class="col-sm-2 col-form-label">Approve?</label>
                                                                <div class="col-sm-8">
                                                                    <select class="form-select" id="statusA" name="statusA" required>
                                                                        <option value="Y">Approve</option>
                                                                        <option value="N">Reject</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-xxl-12 col-lg-12 mb-12">
                                                            <div class="row">
                                                                <label for="reasons" class="col-sm-2 col-form-label">Reasons *</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control" id="reasons" name="reasons" rows="3" placeholder="Reasons" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-success" id="submitApproval">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="uploadModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title">Upload Excel File</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton">Preview Data</button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-success" id="submitWeights">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="prePrintModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="prePrintForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title">Pre-print Sales Slip</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <label for="prePrint" class="col-sm-4 col-form-label">Pre-print Sales Slip</label>
                                                            <div class="col-sm-8">
                                                                <select id="prePrint" name="prePrint" class="form-select" required>
                                                                    <option value="Y" selected>Yes</option>
                                                                    <option value="N">No</option>
                                                                </select>  
                                                            </div>

                                                            <input type="hidden" class="form-control" id="id" name="id">                                   
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-success" id="submitPrePrint">Save changes</button>
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
                                                            <input type="hidden" class="form-control" id="isEmptyContainer" name="isEmptyContainer">                                   
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-success" id="submitCancel">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!--div class="modal fade" id="uploadModal" role="dialog" aria-labelledby="importModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importModalScrollableTitle">Upload Excel File</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton">Preview Data</button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div> 

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-success" id="saveButton">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div-->
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
                                                                <h5 class="card-title mb-0 text-white">Previous Records (Lorry)</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="exportPdf" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export PDF
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <!--a href="/template/Weight_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        Download Template 
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExccl" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal">
                                                                    <i class="mdi mdi-file-excel align-middle me-1"></i>
                                                                    Import Orders
                                                                </button-->
                                                                <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New Weight
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Transaction <br>Id</th>
                                                                    <th>Weight <br>Type</th>
                                                                    <th>Weight <br> Status</th>
                                                                    <th>Customer/ <br> Supplier</th>
                                                                    <th>Container No</th>
                                                                    <th>Seal No</th>
                                                                    <th>Vehicle</th>
                                                                    <th>Gross <br>Incoming</th>
                                                                    <th>Incoming <br>Date</th>
                                                                    <th>Tare <br>Outgoing</th>
                                                                    <th>Outgoing <br>Date</th>
                                                                    <th>Nett <br>Weight</th>
                                                                    <th>Vehicle 2</th>
                                                                    <th>Gross <br>Incoming 2</th>
                                                                    <th>Incoming <br>Date 2</th>
                                                                    <th>Tare <br>Outgoing 2</th>
                                                                    <th>Outgoing <br>Date 2</th>
                                                                    <th>Nett <br>Weight 2</th>
                                                                    <th>Action</th>
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
                                                                <h5 class="card-title mb-0 text-white">Pending Empty Container Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!--a href="/template/Weight_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        Download Template 
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExccl" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal">
                                                                    <i class="mdi mdi-file-excel align-middle me-1"></i>
                                                                    Import Orders
                                                                </button-->
                                                                <!-- <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New Weight
                                                                </button> -->
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="emptyContainerTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Container <br>No</th>
                                                                    <th>Seal <br>No</th>
                                                                    <th>Weight <br> Status</th>
                                                                    <th>Vehicle</th>
                                                                    <th>Gross <br>Incoming</th>
                                                                    <th>Incoming <br>Date</th>
                                                                    <th>Tare <br>Outgoing</th>
                                                                    <th>Outgoing <br>Date</th>
                                                                    <th>Nett <br>Weight</th>
                                                                    <th>Action</th>
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
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="prePrintModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <form role="form" id="prePrintForm">
                        <div class="modal-header bg-gray-dark color-palette">
                            <h4 class="modal-title"></h4>
                            <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Pre-print Sale Slip</label>
                                        <select name="prePrint" id="prePrint">
                                            <option selected>Please Select</option>
                                            <option value="Y">Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
    var table = null;
    var emptyContainerTable = null;
    let clickTimer = null;

    $(function () {
        var userRole = '<?=$role ?>';
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
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
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

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
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
            'ajax': {
                'url':'php/filterWeight.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    customer: customerNoI,
                    vehicle: vehicleNoI,
                    invoice: invoiceNoI,
                    batch: batchNoI,
                    product: productSearchI,
                    rawMaterial: rawMaterialI,
                    plant: plantNoI,
                    transactionId: transactionIdI,
                    containerNo: containerNoI,
                    sealNo: sealNoI
                } 
            },
            'columns': [
                { 
                    data: 'transaction_id',
                    class: 'transaction-column'
                },                
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
                        let buttons = `<div class="row g-1 d-flex">`;

                        if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER' ) {
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
                        }else {
                            if (row.is_complete != 'Y' ){
                                if (row.weight_type == 'Primer Mover + Container'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-weight-hanging"></i>
                                        </button>
                                    </div>`;    
                                }else{
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'N')" class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-weight-hanging"></i>
                                        </button>
                                    </div>`;  
                                }

                            }
                        }

                        if (row.is_approved == 'Y') {
                            if (row.weight_type != 'Primer Mover + Container'){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>`;
                            }
                        }

                        if (row.is_approved == 'N') {
                            buttons += `
                            <div class="col-auto">
                                <button title="Approve" type="button" id="approve${data}" onclick="approve(${data})" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </div>`;
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

                        // let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">';

                        // if (row.is_complete != 'Y' || userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER' ) {
                        //     dropdownMenu += '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>'; 
                        // }

                        // if (row.is_approved == 'Y') {
                        //     dropdownMenu += '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>';
                        // }

                        // if (row.is_approved == 'N') {
                        //     dropdownMenu += '<li><a class="dropdown-item approval-item-btn" id="approve' + data + '" onclick="approve(' + data + ')"><i class="ri-check-fill align-bottom me-2 text-muted"></i> Approval</a></li>';
                        // }

                        // if(userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
                        //     dropdownMenu += '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';
                        // }

                        // dropdownMenu += '</ul></div>';
                        // return dropdownMenu;
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

        emptyContainerTable = $("#emptyContainerTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterEmptyContainer.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI
                } 
            },
            'columns': [
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

                        if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER' ) {
                            if (row.is_complete != 'Y' ){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>`;
                            }
                        }else {
                            if (row.is_complete != 'Y' ){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data},'Y')" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-weight-hanging"></i>
                                    </button>
                                </div>`;
                            }
                        }

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

        // Add event listener for opening and closing details on row click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this); // The row that was clicked
            var row = table.row(tr);
            if (!row.data()) return; // <-- Exit early if row data is not available

            // Exclude specific td elements by checking the event target
            if ($(e.target).closest('td').hasClass('transaction-column') || $(e.target).closest('td').hasClass('action-button') || row.data().weight_type =='Primer Mover + Container') {
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
                    $.post('php/getWeight.php', { userID: row.data().id, format: 'EXPANDABLE' }, function (data) {
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
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
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
            }
            /*else{
                let userChoice = confirm('The final value is out of the acceptable range. Do you want to send for approval (OK) or bypass (Cancel)?');
                if (userChoice) {
                    $('#addModal').find('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
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

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        $.post('php/print.php', {userID: obj.id, file: 'weight'}, function(data){
                            var obj2 = JSON.parse(data);

                            if(obj2.status === 'success'){
                                var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                printWindow.document.write(obj2.message);
                                printWindow.document.close();
                                setTimeout(function(){
                                    printWindow.print();
                                    printWindow.close();
                                    table.ajax.reload();
                                    window.location = 'index.php';
                                    
                                    /*setTimeout(function () {
                                        if (confirm("Do you need to reprint?")) {
                                            $.post('php/print.php', { userID: obj.id, file: 'weight' }, function (data) {
                                                var obj = JSON.parse(data);
                                                if (obj.status === 'success') {
                                                    var reprintWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                                    reprintWindow.document.write(obj.message);
                                                    reprintWindow.document.close();
                                                    setTimeout(function () {
                                                        reprintWindow.print();
                                                        reprintWindow.close();
                                                    }, 500);
                                                } 
                                                else {
                                                    window.location = 'index.php';
                                                }
                                            });
                                        }
                                    }, 500);*/
                                }, 500);
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
                    $('#addModal').find('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
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

        $('#submitBypass').on('click', function(){
            if($('#bypassForm').valid()){
                $('#addModal').find('#bypassReason').val($('#bypassModal').find('#reason').val());
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
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

        $('#submitApproval').on('click', function(){
            if($('#approvalForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/updateApproval.php', $('#approvalForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['approve'])){
                                echo "window.location = 'index.php';";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = 'index.php';
                        $('#spinnerLoading').hide();
                        $('#approvalModal').modal('hide');
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

        $('#submitPrePrint').on('click', function(){
            if($('#prePrintForm').valid()){
                $('#spinnerLoading').show();
                var id = $('#prePrintModal').find('#id').val();
                var prePrintStatus = $('#prePrintModal').find('#prePrint').val();

                $.post('php/print.php', {userID: id, file: 'weight', prePrint: prePrintStatus}, function(data){
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
                        if(data.includes("GS")){
                            var text = data.split(" ");
                            var text2 = text[text.length - 1];
                            text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(text2);
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'EX2001'){
                        var text = data.split(" ");
                        let newArray = text.slice(1, -1);
                        let newtext = newArray.join();
                        $('#indicatorWeight').html(newtext.replaceAll(",", "").trim());
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
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
                }
                else{
                    $('#indicatorWeight').html('0');
                    $('#indicatorConnected').removeClass('bg-primary');
                    $('#checkingConnection').addClass('bg-danger');
                }
            });
        }, 500);

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
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
            $("#emptyContainerTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterWeight.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: customerNoI,
                        vehicle: vehicleNoI,
                        invoice: invoiceNoI,
                        batch: batchNoI,
                        product: productSearchI,
                        rawMaterial: rawMaterialI,
                        plant: plantNoI,
                        transactionId: transactionIdI,
                        containerNo: containerNoI,
                        sealNo: sealNoI
                    } 
                },
                'columns': [
                    { 
                        data: 'transaction_id',
                        class: 'transaction-column'
                    },
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
                            let buttons = `<div class="row g-1 d-flex">`;

                            if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER' ) {
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
                            }else {
                                if (row.is_complete != 'Y' ){
                                    if (row.weight_type == 'Primer Mover + Container'){
                                        buttons += `
                                        <div class="col-auto">
                                            <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-weight-hanging"></i>
                                            </button>
                                        </div>`;    
                                    }else{
                                        buttons += `
                                        <div class="col-auto">
                                            <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data}, 'N')" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-weight-hanging"></i>
                                            </button>
                                        </div>`;  
                                    }

                                }
                            }

                            if (row.is_approved == 'Y') {
                                if (row.weight_type != 'Primer Mover + Container'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                    </div>`;
                                }
                            }

                            if (row.is_approved == 'N') {
                                buttons += `
                                <div class="col-auto">
                                    <button title="Approve" type="button" id="approve${data}" onclick="approve(${data})" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>`;
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
                            // let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">';

                            // if (row.is_complete != 'Y' || userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER') {
                            //     dropdownMenu += '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>'; 
                            // }

                            // if (row.is_approved == 'Y') {
                            //     dropdownMenu += '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>';
                            // }

                            // if (row.is_approved == 'N') {
                            //     dropdownMenu += '<li><a class="dropdown-item approval-item-btn" id="approve' + data + '" onclick="approve(' + data + ')"><i class="ri-check-fill align-bottom me-2 text-muted"></i> Approval</a></li>';
                            // }

                            // if(userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
                            //     dropdownMenu += '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';
                            // }

                            // dropdownMenu += '</ul></div>';
                            // return dropdownMenu;
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
                    'url':'php/filterEmptyContainer.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI
                    } 
                },
                'columns': [
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

                            if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER' ) {
                                if (row.is_complete != 'Y' ){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Edit" type="button" id="edit${data}" onclick="edit(${data}, 'Y')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>`;
                                }
                            }else {
                                if (row.is_complete != 'Y' ){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="edit${data}" onclick="edit(${data},'Y')" class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-weight-hanging"></i>
                                        </button>
                                    </div>`;
                                }
                            }

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
        });

        $('#addWeight').on('click', function(){
            // Show Capture Buttons When Add New
            $('#addModal').find('#grossCapture').show();
            $('#addModal').find('#tareCapture').show();
            $('#addModal').find('#id').val("");
            $('#addModal').find('#currentWeight').text("0");
            $('#addModal').find('#transactionId').val("");
            $('#addModal').find('#transactionStatus').val("Sales").trigger('change');
            $('#addModal').find('#emptyContainerNo').val("").trigger('change');
            $('#addModal').find('#weightType').val("Normal").trigger('change');
            $('#addModal').find('#customerType').val("Normal").trigger('change');
            $('#addModal').find('#transactionDate').val(formatDate2(today));
            $('#addModal').find('#vehiclePlateNo1').val("").trigger('change');
            $('#addModal').find('#vehiclePlateNo2').val("").trigger('change');
            $('#addModal').find('#supplierWeight').val("");
            $('#addModal').find('#bypassReason').val("");
            $('#addModal').find('#customerCode').val("");
            $('#addModal').find('#customerName').val("-").trigger('change');
            $('#addModal').find('#supplierCode').val("");
            $('#addModal').find('#supplierName').val("-").trigger('change');
            $('#addModal').find('#productCode').val("");
            $('#addModal').find('#productName').val("-").trigger('change');
            $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
            $('#addModal').find('#rawMaterialCode').val("");
            $('#addModal').find('#rawMaterialName').val("-").trigger('change');
            $('#addModal').find('#siteCode').val("");
            $('#addModal').find('#siteName').val("").trigger('change');
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#sealNo').val("");
            $('#addModal').find('#invoiceNo').val("");
            $('#addModal').find('#purchaseOrder').val("").trigger('change');
            $('#addModal').find('#salesOrder').val("").trigger('change');
            $('#addModal').find('#deliveryNo').val("");
            $('#addModal').find('#transporterCode').val("");
            $('#addModal').find('#transporter').val("-").trigger('change');
            $('#addModal').find('#destinationCode').val("");
            $('#addModal').find('#agent').val("").trigger('change');
            $('#addModal').find('#agentCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#destination').val("-").trigger('change');
            $('#addModal').find('#otherRemarks').val("");
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val("");
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val("");
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val("");
            $('#addModal').find('#grossIncoming2').val("");
            $('#addModal').find('#status').val("");
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val("");
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val("");
            $('#addModal').find('#reduceWeight').val("");
            // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
            $('#addModal').find('#weightDifference').val("");
            // $('#addModal').find('#id').val(obj.message.is_complete);
            // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
            // $('#addModal').find("#manualWeightNo").prop("checked", true);
            // $('#addModal').find("#manualWeightYes").prop("checked", false);
            $('#addModal').find('#manualWeightNo').trigger('click');
            //$('#addModal').find('input[name="manualWeight"]').val("false");
            //$('#addModal').find('#indicatorId').val("");
            $('#addModal').find('#weighbridge').val("");
            //$('#addModal').find('#indicatorId2').val("");
            $('#addModal').find('#productDescription').val("");
            $('#addModal').find('#productHigh').val("");
            $('#addModal').find('#productLow').val("");
            $('#addModal').find('#productVariance').val("");
            $('#addModal').find('#orderWeight').val("0");
            $('#addModal').find('#unitPrice').val("0.00");
            $('#addModal').find('#subTotalPrice').val("0.00");
            $('#addModal').find('#sstPrice').val("0.00");
            $('#addModal').find('#productPrice').val("0.00");
            $('#addModal').find('#totalPrice').val("0.00");
            $('#addModal').find('#finalWeight').val("");
            $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
            $('#addModal').find('#noOfDrum').val("");
            $('#addModal').find('#balance').val("");
            $('#addModal').find('#insufficientBalDisplay').hide();
            $('#addModal').find('#containerNoInput').val("");
            $('#addModal').find('#containerNo').val("");
            $('#addModal').find('#containerNo2').val("");
            $('#addModal').find('#sealNo2').val("");

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

            //if (batchNoI == 'Pending'){
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
            /*}else{
                alert("Please change status to Pending before generating PDF.");
            }*/
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

            //if (batchNoI == 'Pending'){
                window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+invoiceNoI+"&product="+productSearchI+"&rawMat="+rawMaterialI+"&plant="+plantNoI+"&status="+batchNoI);
            /*}else{
                alert("Please change status to Pending before generating Excel.");
            }*/
        });

        $('#weightType').on('change', function(){
            var weightType = $(this).val();
            var transaType = $('#transactionStatus').val();

            if (weightType == 'Container'){
                $.post('php/getContainers.php', {userID: transaType}, function (data){
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
                $('#addModal').find('#emptyContainerDisplay').show();
                $('#addModal').find('#containerDisplay').hide();
                $('#addModal').find('#containerNoInput').attr('required', false);
                $('#addModal').find('#emptyContainerNo').attr('required', true);
            }else if (weightType == 'Empty Container'){
                handleWeightType(weightType);
                $('#addModal').find('#emptyContainerDisplay').hide();
                $('#addModal').find('#containerDisplay').show();
                $('#addModal').find('#containerNoInput').attr('required', true);
                $('#addModal').find('#emptyContainerNo').attr('required', false);
            }else{
                handleWeightType(weightType);
                $('#addModal').find('#emptyContainerDisplay').hide();
                $('#addModal').find('#containerDisplay').show();
                $('#addModal').find('#containerNoInput').attr('required', false);
                $('#addModal').find('#emptyContainerNo').attr('required', false);
            }
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

        $('#vehicleNoTxt').on('keyup', function(){
            var x = $('#vehicleNoTxt').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt').val(x);

            var exDel = $('input[name="exDel"]:checked').val();
            if (exDel == 'true'){
                // $('#addModal').find('#transporter').val('Own Transportation').trigger('change');
                // $('#addModal').find('#transporterCode').val('T01');
                $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        // var customerName = obj.message.customer_name;
                        // var customerCode = obj.message.customer_code;

                        // $('#addModal').find('#customerName').val(customerName).trigger('change');
                        // $('#addModal').find('#customerCode').val(customerCode);
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
            }else{
                // $('#addModal').find('#customerName').val('').trigger('change');
                // $('#addModal').find('#customerCode').val('');

                $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        // var transporterName = obj.message.transporter_name;
                        // var transporterCode = obj.message.transporter_code;

                        // $('#addModal').find('#transporter').val(transporterName).trigger('change');
                        // $('#addModal').find('#transporterCode').val(transporterCode);
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
            $('#grossIncomingDate').val(formatDate3(new Date()));
            $('#nettWeight').trigger('change');
            $('#grossWeightBy1').val('<?php echo $username; ?>');
        });

        $('#grossCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
        });

        $('#tareOutgoing').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#tareOutgoingDate').val(formatDate3(new Date()));
            $('#nettWeight').trigger('change');
            $('#tareWeightBy1').val('<?php echo $username; ?>');
        });

        $('#tareCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#nettWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            var current = Math.abs(nett1 - nett2);
            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#reduceWeight').trigger('change');
            //$('#finalWeight').trigger('change');
        });
        
        $('#reduceWeight').on('change', function(){
            var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
            var current = Math.abs(nett1 - nett2);
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
        });

        $('#grossIncoming2').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#grossIncomingDate2').val(formatDate3(new Date()));
            $('#nettWeight2').trigger('change');
            $('#grossWeightBy2').val('<?php echo $username; ?>');
        });

        $('#grossCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming2').val(parseFloat(text).toFixed(0));
            $('#grossIncoming2').trigger('keyup');
        });

        $('#tareOutgoing2').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#tareOutgoingDate2').val(formatDate3(new Date()));
            $('#nettWeight2').trigger('change');
            $('#tareWeightBy2').val('<?php echo $username; ?>');
        });

        $('#tareCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing2').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing2').trigger('keyup');
        });

        $('#nettWeight2').on('change', function(){
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
            var current = Math.abs(nett1 - nett2);
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
            var customerType = $('#addModal').find('#customerType').val();
            var weightType = $('#addModal').find('#weightType').val();

            if(weightType == 'Container'){
                $.post('php/getContainers.php', {userID: $(this).val()}, function (data){
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
            $('#supplierCode').val($('#supplierName :selected').data('code'));
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
            $('#customerCode').val($('#customerName :selected').data('code'));
        });

        $('input[name="exDel"]').change(function() {
            var vehicleNo1 = $('#addModal').find('#vehiclePlateNo1').val();
            var exDel = $('input[name="exDel"]:checked').val();
            if (exDel == 'true'){
                // $('#addModal').find('#transporter').val('Own Transportation').trigger('change');
                // $('#addModal').find('#transporterCode').val('T01');
                $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function(data){
                    var obj = JSON.parse(data);
                    if(obj.status === 'success'){
                        // var customerName = obj.message.customer_name;
                        // var customerCode = obj.message.customer_code;

                        // $('#addModal').find('#customerName').val(customerName).trigger('change');
                        // $('#addModal').find('#customerCode').val(customerCode);
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
                // $('#addModal').find('#customerName').val('').trigger('change');
                // $('#addModal').find('#customerCode').val('');

                $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        // var transporterName = obj.message.transporter_name;
                        // var transporterCode = obj.message.transporter_code;

                        // $('#addModal').find('#transporter').val(transporterName).trigger('change');
                        // $('#addModal').find('#transporterCode').val(transporterCode);
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
            $('#containerNo').val(emptyContainerNo);

            if (emptyContainerNo == '-'){
                $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
                $('#addModal').find('#grossIncoming').val(0);
                $('#addModal').find('#grossIncomingDate').val("");
                $('#addModal').find('#tareOutgoing').val(0);
                $('#addModal').find('#tareOutgoingDate').val("");
                $('#addModal').find('#nettWeight').val(0);
                $('#normalCard').hide();
            } else if (emptyContainerNo) { 
                $.post('php/getEmptyContainer.php', {userID: emptyContainerNo}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){ 
                        $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                        $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                        $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                        $('#addModal').find('#sealNo').val(obj.message.seal_no);
                        $('#addModal').find('#containerNo2').val(obj.message.container_no2);
                        $('#addModal').find('#sealNo2').val(obj.message.seal_no2);

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

                        
                        $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                        $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                        $('#addModal').find('#grossIncomingDate').val(obj.message.gross_weight1_date);
                        $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                        $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date);
                        $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
                        $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);

                        if(obj.message.vehicleNoTxt != null){
                            $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                            $('#manualVehicle').val(1);
                            $('#manualVehicle').prop("checked", true);
                            $('.index-vehicle').hide();
                            $('#vehicleNoTxt').show();
                        }
                        else{
                            $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                            $('#manualVehicle').val(0);
                            $('#manualVehicle').prop("checked", false);
                            $('.index-vehicle').show();
                            $('#vehicleNoTxt').hide();
                        }
                        
                        
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
                $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
                $('#addModal').find('#grossIncoming').val(0);
                $('#addModal').find('#grossIncomingDate').val("");
                $('#addModal').find('#tareOutgoing').val(0);
                $('#addModal').find('#tareOutgoingDate').val("");
                $('#addModal').find('#nettWeight').val(0);
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
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val(0);
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val(0);
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val(0);
            $('#normalCard').hide();
            $('#containerCard').show();
        }else if(weightType == 'Empty Container'){
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming2').val(0);
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val(0);
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val(0);
            $('#containerCard').hide();
            $('#normalCard').show();
        }else{
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming2').val(0);
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val(0);
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val(0);
            $('#normalCard').show();
            $('#containerCard').hide();
        }
    }

    function format (row) {
        var transactionStatus = '';
        var weightType = '';

        if (row.transaction_status == 'Sales') {
            transactionStatus = 'Dispatch';
        } else if (row.transaction_status == 'Purchase') {
            transactionStatus = 'Receiving';
        } else if (row.transaction_status == 'Local') {
            transactionStatus = 'Internal Transfer';
        } else {
            transactionStatus = 'Miscellaneous';
        }

        if(row.weight_type == 'Container'){
            weightType = 'Primer Mover';
        }else if(row.weight_type == 'Empty Container'){
            weightType = 'Primer Mover + Container';
        }else if(row.weight_type == 'Normal'){
            weightType = 'Normal Weighing';
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

        var type = '';
        if (isContainer == 'Y'){
            type = 'Container';
        }else{
            type = 'Weight'
        }

        $.post('php/getWeight.php', {userID: id, type: type}, function(data)
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
                    $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
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
                    $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2).trigger('change');
                    $('#manualVehicle2').val(0);
                    $('#manualVehicle2').prop("checked", false);
                    $('.index-vehicle2').show();
                    $('#vehicleNoTxt2').hide();
                }
                
                $('#addModal').find('#productCode').val(obj.message.product_code);
                if (obj.message.ex_del == 'EX'){
                    $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true);
                }else{
                    $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true);
                }
                
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change')
                $('#addModal').find('#siteCode').val(obj.message.site_code);
                $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                $('#addModal').find('#agentCode').val(obj.message.agent_code);
                $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
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
                $('#addModal').find('#grossIncomingDate').val(formatDate3(new Date(obj.message.gross_weight1_date)));
                $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date != null ? formatDate3(new Date(obj.message.tare_weight1_date)) : '');
                $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                $('#addModal').find('#grossIncomingDate2').val(obj.message.gross_weight2_date != null ? formatDate3(new Date(obj.message.gross_weight2_date)) : '');
                $('#addModal').find('#grossWeightBy2').val(obj.message.gross_weight_by2);
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                $('#addModal').find('#tareOutgoingDate2').val(obj.message.tare_weight2_date != null ? formatDate3(new Date(obj.message.tare_weight2_date)) : '');
                $('#addModal').find('#tareWeightBy2').val(obj.message.tare_weight_by2);
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);
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

                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').find('#unitPrice').val(obj.message.unit_price);
                $('#addModal').find('#subTotalPrice').val(obj.message.sub_total);
                $('#addModal').find('#sstPrice').val(obj.message.sst);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);

                if (obj.message.load_drum == 'LOAD'){
                    $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
                }else{
                    $('#addModal').find("input[name='loadDrum'][value='false']").prop("checked", true).trigger('change');
                }
                
                $('#addModal').find('#noOfDrum').val(obj.message.no_of_drum);
                $('#addModal').find('#containerNoInput').val(obj.message.container_no);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#containerNo2').val(obj.message.container_no2);
                $('#addModal').find('#sealNo').val(obj.message.seal_no);
                $('#addModal').find('#sealNo2').val(obj.message.seal_no2);

                // Load container data and update the emptyContainerNo field if it's a container
                if(obj.message.weight_type == 'Container' && obj.message.container_no){
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
                    $('#addModal').find('#siteCode').val(obj.message.site_code);
                    $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                    $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                    $('#addModal').find('#agentCode').val(obj.message.agent_code);
                    $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
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
        $.post('php/getContainers.php', {userID: transactionStatus}, function (data){
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

    // function deactivate(id){
        
    //     $('#spinnerLoading').show();
    //     $.post('php/deleteWeight.php', {userID: id}, function(data){
    //         var obj = JSON.parse(data);
            
    //         if(obj.status === 'success'){
    //             table.ajax.reload();
    //             $('#spinnerLoading').hide();
    //             $("#successBtn").attr('data-toast-text', obj.message);
    //             $("#successBtn").click();
    //         }
    //         else if(obj.status === 'failed'){
    //             $('#spinnerLoading').hide();
    //             $("#failBtn").attr('data-toast-text', obj.message );
    //             $("#failBtn").click();
    //         }
    //         else{
    //             $('#spinnerLoading').hide();
    //             $("#failBtn").attr('data-toast-text', obj.message );
    //             $("#failBtn").click();
    //         }
    //     });
    // }

    function print(id, transactionStatus) {
        /*if (transactionStatus == "Sales"){
            $('#prePrintModal').find('#id').val(id);
            $('#prePrintModal').find('#prePrint').val("");
            $("#prePrintModal").modal("show");

            $('#prePrintForm').validate({
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
        }else{
            $.post('php/print.php', {userID: id, file: 'weight'}, function(data){
                var obj = JSON.parse(data);

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
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $("#failBtn").attr('data-toast-text', "Something wrong when print");
                    $("#failBtn").click();
                }
            });
        }*/
        //var id = $('#prePrintModal').find('#id').val();
        var prePrintStatus = 'N';

        $.post('php/print.php', {userID: id, file: 'weight', prePrint: prePrintStatus}, function(data){
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
    </script>
</body>
</html>