<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

$user = $_SESSION['id'];
$username = $_SESSION["username"];
$plantId = $_SESSION['plant'];
$locationId = $_SESSION['location_id'];
$stmt = $db->prepare("SELECT * from Location WHERE id = ?");
$stmt->bind_param('s', $locationId);
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
$allowManual = 'N';
$allowEdit = 'N';
if ($user != null && $user != ''){
    $stmt3 = $db->prepare("SELECT * from Users WHERE id = ?");
    $stmt3->bind_param('s', $user);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
        
    if(($row3 = $result3->fetch_assoc()) !== null){
        $role = $row3['role'];
        $allowManual = $row3['allow_manual'];
        $allowEdit = $row3['allow_edit'];
    }
}

//$lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer3 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer4 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$product = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product3 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product4 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0' ORDER BY name ASC");
$transporter2 = $db->query("SELECT * FROM Transporter WHERE status = '0' ORDER BY name ASC");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$destination2 = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier3 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$unit = $db->query("SELECT * FROM Unit WHERE status = '0' ORDER BY unit ASC");
$purchaseOrder = $db->query("SELECT * FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$salesOrder = $db->query("SELECT * FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$agent = $db->query("SELECT * FROM Agents WHERE status = '0' ORDER BY name ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial3 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$site = $db->query("SELECT * FROM Site WHERE status = '0' ORDER BY name ASC");
$container = $db->query("SELECT * FROM Weight_Container WHERE status = '0' AND is_complete = 'Y' AND is_cancel = 'N'");
$company = $db->query("SELECT * FROM Company WHERE status = '0' ORDER BY name ASC");
$company2 = $db->query("SELECT * FROM Company WHERE status = '0' ORDER BY name ASC");
$company3 = $db->query("SELECT * FROM Company WHERE status = '0' ORDER BY name ASC");
$projects = $db->query("SELECT * FROM Projects WHERE status = '0' ORDER BY project ASC");
$projects2 = $db->query("SELECT * FROM Projects WHERE status = '0' ORDER BY project ASC");
$projects3 = $db->query("SELECT * FROM Projects WHERE status = '0' ORDER BY project ASC");
$projects4 = $db->query("SELECT * FROM Projects WHERE status = '0' ORDER BY project ASC");
$transportCap = $db->query("SELECT * FROM Transport_Cap WHERE status = '0'");
$transportCap2 = $db->query("SELECT * FROM Transport_Cap WHERE status = '0'");

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

        .select2-container--default .select2-selection--multiple .select2-selection__choice,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #000 !important;
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
                                                                <option value="Sales" selected>Dispatch</option>
                                                                <option value="Purchase">Receiving</option>
                                                                <option value="Local">Internal Transfer</option>
                                                                <option value="Misc">Miscellaneous</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="customerSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer Name</label>
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
                                                            <label for="supplierSearch" class="form-label">Supplier Name</label>
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
                                                                <option value="Empty Container">Primer Mover + Container</option>
                                                                <option value="Container">Primer Mover</option>
                                                                <!-- <option value="Different Container">Primer Mover + Different Bins</option> -->
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label">Status</label>
                                                            <select id="batchNoSearch" class="form-select select2">
                                                                <option value="N" selected>Pending</option>
                                                                <option value="Y">Complete</option>
                                                                <option value="C">Cancelled</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->                                                
                                                    <div class="col-3" id="productSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="productSearch" class="form-label">Product Code</label>
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
                                                            <label for="rawMatSearch" class="form-label">Raw Material Code</label>
                                                            <select id="rawMatSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowRawMatF=mysqli_fetch_assoc($rawMaterial2)){ ?>
                                                                    <option value="<?=$rowRawMatF['raw_mat_code'] ?>"><?=$rowRawMatF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="projectSearch" class="form-label">Project Code</label>
                                                            <select id="projectSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowProjectF=mysqli_fetch_assoc($projects2)){ ?>
                                                                    <option value="<?=$rowProjectF['id'] ?>"><?=$rowProjectF['project'] . ' - ' . $rowProjectF['project_name'] ?></option>
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
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invDelPoSearch" class="form-label">INV/DO/PO No</label>
                                                            <input type="text" class="form-control" id="invDelPoSearch" name="invDelPoSearch" placeholder="INV/DO/PO No">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="companySearch" class="form-label">Company</label>
                                                            <select id="companySearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowCompF=mysqli_fetch_assoc($company2)){ ?>
                                                                    <option value="<?=$rowCompF['id'] ?>"><?=$rowCompF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
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
                                <div class="col-xl-3 col-md-6 add-new-weight">
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
                                                                        <div class="card bg-primary">
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
                                                                        <div class="card bg-primary">
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
                                                                    <div class="card-body py-2 px-3">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4">
                                                                                <div class="row">
                                                                                    <label for="company" class="col-sm-4 col-form-label">Company</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="company" name="company" class="form-select select2" required>
                                                                                        <?php while($rowComp=mysqli_fetch_assoc($company)){ ?>
                                                                                            <option value="<?=$rowComp['id'] ?>"><?=$rowComp['name'] ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                             <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                                            <option value="Sales">Dispatch</option>
                                                                                            <option value="Purchase">Receiving</option>
                                                                                            <option value="Local">Internal Transfer</option>
                                                                                            <option value="Misc">Miscellaneous</option>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4"  <?php 
                                                                                if($_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'ADMIN' && $allowManual == 'N'){
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
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row col-12" id="weighingDetailsSection"></div>

                                                        <div class="row col-12" id="multiCustomerSection" style="display:none;">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <button type="button" class="btn btn-success" id="addCustomer">Add Customer</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <table class="table table-primary" style="text-align: center;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Delivery No</th>
                                                                                        <th>Customer Name</th>
                                                                                        <th>Product Code</th>
                                                                                        <th>Project Code</th>
                                                                                        <th>Internal Doc. No.</th>
                                                                                        <th>Ref. No.</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="customerTable"></tbody>
                                                                            </table>                                            
                                                                        </div><!-- end row -->     
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row col-12" style="display:none;">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <button type="button" class="btn btn-success add-product" id="addProduct">Add Product</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <table class="table table-primary" style="text-align: center;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th width="5%">No</th>
                                                                                        <th>Product</th>
                                                                                        <th>Packing</th>
                                                                                        <th>Gross Weight (kg)</th>
                                                                                        <th>Tare Weight (kg)</th>
                                                                                        <th>Nett Weight (kg)</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="productTable"></tbody>
                                                                            </table>                                            
                                                                        </div><!-- end row -->     
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-12" id="vehicle1Section" style="display:none;">
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
                                                                                    </div>-->
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-success fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate">
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
                                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate">
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
                                                                        <div class="row mb-3" id="vehicleWeight2Display" style="display:none">
                                                                            <label for="vehicleWeight2" class="col-sm-4 col-form-label">Vehicle Weight</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="vehicleWeight2" name="vehicleWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
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
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="container2WeightDisplay" style="display:none">
                                                                            <label for="emptyContainerWeight2" class="col-sm-4 col-form-label">Empty Container Weight</label>
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
                                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2">
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
                                                            <div class="col-xxl-4 col-lg-4" id="priceCard" style="display:none;">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3" id="unitPriceDisplay">
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
                                                                        <div class="row mb-3" id="sstDisplay">
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
                                                                        <div class="row mb-3" id="subTotalPriceDisplay">
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
                                                                        <div class="row mb-3" id="totalPriceDisplay">
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
                                                                    <div class="row mb-3" id="vehicleWeight2Display" style="display:none">
                                                                        <label for="vehicleWeight2" class="col-sm-4 col-form-label">Vehicle Weight</label>
                                                                        <div class="col-sm-8">
                                                                            <div class="input-group">
                                                                                <input type="number" class="form-control input-readonly" id="vehicleWeight2" name="vehicleWeight2" placeholder="0" readonly>
                                                                                <div class="input-group-text">Kg</div>
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
                                                                            <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3" id="container2WeightDisplay" style="display:none">
                                                                        <label for="emptyContainerWeight2" class="col-sm-4 col-form-label">Empty Container Weight</label>
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
                                                                            <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2">
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
                                                        <div class="col-xxl-4 col-lg-4 mb-3" id="replacementContainerDisplay" style="display:none">
                                                            <div class="row">
                                                                <label for="replacementContainer" class="col-sm-4 col-form-label">New Empty Entrance Bin</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control" id="replacementContainer" name="replacementContainer" placeholder="Replacement Container" required>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                        <!--<div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                            <div class="row">
                                                                <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                <div class="col-sm-8">
                                                                    <select id="indicatorId" name="indicatorId" class="form-select select2" >
                                                                        <option selected>ind12345</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>-->
                                                        <div class="col-xxl-4 col-lg-4 mb-3" style="display:none">
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
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="<?=$indicator ?>">
                                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                                        <input type="hidden" id="grossWeightBy1" name="grossWeightBy1">
                                                        <input type="hidden" id="tareWeightBy1" name="tareWeightBy1">
                                                        <input type="hidden" id="grossWeightBy2" name="grossWeightBy2">
                                                        <input type="hidden" id="tareWeightBy2" name="tareWeightBy2">
                                                        <input type="hidden" id="indicatorId" name="indicatorId" value="<?=$locationId ?>">
                                                        <input type="hidden" id="isWeightOut" name="isWeightOut">

                                                        <div style="display:none;">
                                                            <select class="form-select js-choice select2" id="clonedCustomer" name="clonedCustomer">
                                                                <?php while($rowCustomer=mysqli_fetch_assoc($customer4)){ ?>
                                                                    <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <select class="form-select select2" id="clonedSupplier" name="clonedSupplier">
                                                                <?php while($rowSupplier=mysqli_fetch_assoc($supplier3)){ ?>
                                                                    <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <select class="form-select select2" id="clonedProduct" name="clonedProduct" multiple>
                                                                <?php while($rowProduct=mysqli_fetch_assoc($product4)){ ?>
                                                                    <option 
                                                                        value="<?=$rowProduct['name'] ?>" 
                                                                        data-price="<?=$rowProduct['price'] ?>" 
                                                                        data-code="<?=$rowProduct['product_code'] ?>" 
                                                                        data-high="<?=$rowProduct['high'] ?>" 
                                                                        data-low="<?=$rowProduct['low'] ?>" 
                                                                        data-variance="<?=$rowProduct['variance'] ?>" 
                                                                        data-description="<?=$rowProduct['description'] ?>">
                                                                        <?=$rowProduct['product_code'] ?> - <?=$rowProduct['name'] ?>
                                                                    </option>
                                                                <?php } ?>
                                                                <option value="Other" data-code="Other">Other</option>
                                                            </select>      

                                                            <select class="form-select select2" id="clonedRawMaterial" name="clonedRawMaterial" multiple>
                                                                <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial3)){ ?>
                                                                    <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] . ' - ' . $rowRowMat['name'] ?></option>
                                                                <?php } ?>
                                                                <option value="Other" data-code="Other">Other</option>
                                                            </select>    

                                                            <select class="form-select select2" id="clonedProjects" name="clonedProjects[]" multiple>
                                                                <?php while($rowProject=mysqli_fetch_assoc($projects4)){ ?>
                                                                    <option value="<?=$rowProject['id'] ?>"><?=$rowProject['project'] . ' - ' . $rowProject['project_name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
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

                                    <!-- Pre Printed Modal -->
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
                                                            <input type="hidden" class="form-control" id="containerId" name="containerId">                                   
                                                            <input type="hidden" class="form-control" id="isEmptyContainer" name="isEmptyContainer">                                   
                                                            <input type="hidden" class="form-control" id="isMulti" name="isMulti">                                   
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

                                    <div class="modal fade" id="companyModal">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
                                            <div class="modal-content">
                                                <!-- Header -->
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body px-5 py-4">
                                                    <!-- Icon & Intro -->
                                                    <div class="text-center mb-5">
                                                        <div class="avatar-sm mx-auto mb-4">
                                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-2 shadow-sm">
                                                                <i class="ri-building-line"></i>
                                                            </div>
                                                        </div>
                                                        <h5 class="mb-3 fw-semibold">Please select your company to proceed</h5>
                                                        <p class="text-muted mb-0">
                                                            Your selection determines which data and options are loaded.
                                                        </p>
                                                    </div>

                                                    <!-- Company Select -->
                                                    <div class="mb-4 px-5">
                                                        <select id="companySelect" name="companySelect" class="form-select select2 py-2" required>
                                                            <option value="" selected disabled>-- Choose a company --</option>
                                                            <?php while($rowComp = mysqli_fetch_assoc($company3)) { ?>
                                                                <option value="<?= $rowComp['id'] ?>"><?= htmlspecialchars($rowComp['name']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="modal-footer">
                                                    <small class="text-muted me-auto">Need help? Contact your system admin.</small>
                                                    <button type="button" class="btn btn-success" onclick="confirmCompanySelection()">
                                                        Confirm
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="dispatchCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Dispatch</p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                                        <i class="bx bx-export text-success"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>

                                            <!-- Status breakdown -->
                                            <div class="mt-4">
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="N" data-transaction="Sales" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Pending</span>
                                                    <span class="fw-semibold" id="salesPending">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="Y" data-transaction="Sales" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Complete</span>
                                                    <span class="fw-semibold text-success" id="salesComplete">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between status-item" data-status="C" data-transaction="Sales" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Cancel</span>
                                                    <span class="fw-semibold text-danger" id="salesCancel">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="receivingCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Receiving</p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                                        <i class="bx bx-import text-info"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>

                                            <!-- Status breakdown -->
                                            <div class="mt-4">
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="N" data-transaction="Purchase" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Pending</span>
                                                    <span class="fw-semibold" id="purchasePending">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="Y" data-transaction="Purchase" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Complete</span>
                                                    <span class="fw-semibold text-success" id="purchaseComplete">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between status-item" data-status="C" data-transaction="Purchase" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Cancel</span>
                                                    <span class="fw-semibold text-danger" id="purchaseCancel">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate"  id="intTransCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Internal Transfer</p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-transfer text-warning"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>

                                            <!-- Status breakdown -->
                                            <div class="mt-4">
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="N" data-transaction="Local" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Pending</span>
                                                    <span class="fw-semibold" id="localPending">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="Y" data-transaction="Local" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Complete</span>
                                                    <span class="fw-semibold text-success" id="localComplete">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between status-item" data-status="C" data-transaction="Local" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Cancel</span>
                                                    <span class="fw-semibold text-danger" id="localCancel">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="card card-animate" id="miscCard">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Miscellaneous</p>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-secondary rounded fs-3">
                                                        <i class="bx bx-cog text-secondary"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr>

                                            <!-- Status breakdown -->
                                            <div class="mt-4">
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="N" data-transaction="Misc" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Pending</span>
                                                    <span class="fw-semibold" id="miscPending">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2 status-item" data-status="Y" data-transaction="Misc" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Complete</span>
                                                    <span class="fw-semibold text-success" id="miscComplete">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between status-item" data-status="C" data-transaction="Misc" style="cursor: pointer; padding: 4px; border-radius: 4px;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                    <span class="text-muted">Cancel</span>
                                                    <span class="fw-semibold text-danger" id="miscCancel">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                                                <h5 class="card-title mb-0 text-white" id="lorryTableLabel">Pending Records (Lorry)</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="exportPdf" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export PDF
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-info waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <button type="button" id="multiDeleteLorry" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Delete Weight
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
                                                                <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" >
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
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>From <br> Companies</th>
                                                                    <th>Transaction <br>Id</th>
                                                                    <th>Weight <br>Type</th>
                                                                    <th>Weight <br> Status</th>
                                                                    <th>Gate</th>
                                                                    <th>Vehicle</th>
                                                                    <th>Incoming <br>Gross</th>
                                                                    <th>Incoming <br>Date</th>
                                                                    <th>Weigh <br>By</th>
                                                                    <!--th>Tare <br>Outgoing</th>
                                                                    <th>Outgoing <br>Date</th>
                                                                    <th>Nett <br>Weight</th>
                                                                    <th>Vehicle 2</th>
                                                                    <th>Gross <br>Incoming 2</th>
                                                                    <th>Incoming <br>Date 2</th>
                                                                    <th>Tare <br>Outgoing 2</th>
                                                                    <th>Outgoing <br>Date 2</th>
                                                                    <th>Nett <br>Weight 2</th-->
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
                                                                <button type="button" id="multiDeleteContainer" class="btn btn-warning waves-effect waves-light" >
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Delete Weight
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="emptyContainerTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllContainerCheckbox" class="selectAllContainerCheckbox"></th>
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

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>

    <script type="text/html" id="receivingSection">
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
                            <div class="row" id="containerDisplay">
                                <label for="containerNoInput" class="col-sm-4 col-form-label">Container No 1</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="containerNoInput" name="containerNoInput" placeholder="Container No">
                                </div>
                            </div>
                            <div class="row" id="emptyContainerDisplay" style="display:none" >
                                <label for="emptyContainerNo" class="col-sm-4 col-form-label" id="containerNo1Label">Container No 1</label>
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
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="purchaseOrder" class="col-sm-4 col-form-label">P/O No.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder" placeholder="Purchase No">
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
                                        <option value="Empty Container">Primer Mover + Container</option>
                                        <option value="Container">Primer Mover</option>
                                        <!-- <option value="Different Container">Primer Mover + Different Bins</option> -->
                                    </select>   
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNoDisplay">
                            <div class="row">
                                <label for="sealNo" class="col-sm-4 col-form-label">Seal No 1</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sealNo" name="sealNo" placeholder="Seal No">
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNoReplaceDisplay" style="display:none;">
                            <div class="row">
                                
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No.">
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
                        <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2Display">
                            <div class="row">
                                <label for="containerNo2" class="col-sm-4 col-form-label">Container No 2</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="containerNo2" name="containerNo2" placeholder="Container No 2">
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2ReplaceDisplay" style="display:none;">
                            <div class="row">
                                
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="doDisplay">
                            <div class="row">
                                <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                            <div class="row">
                                <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                <div class="col-sm-8">
                                    <select class="form-select js-choice select2" id="customerName" name="customerName" required>
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
                                        <?php while($rowSupplier=mysqli_fetch_assoc($supplier)){ ?>
                                            <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2Display">
                            <div class="row">
                                <label for="sealNo2" class="col-sm-4 col-form-label">Seal No 2</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sealNo2" name="sealNo2" placeholder="Seal No 2">
                                </div>
                            </div>
                        </div> 
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2ReplaceDisplay" style="display:none;">
                            <div class="row">
                                
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
                            <div class="row" id="mrnDisplay">
                                <label for="mrnNo" class="col-sm-4 col-form-label">MRN No.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="mrnNo" name="mrnNo" placeholder="MRN No">
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
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="transportCap" class="col-sm-4 col-form-label">Transport Cap *</label>
                                <div class="col-sm-8">
                                    <select class="form-select select2" id="transportCap" name="transportCap" required>
                                        <?php while($rowTransportCap=mysqli_fetch_assoc($transportCap)){ ?>
                                            <option 
                                                value="<?=$rowTransportCap['id'] ?>">
                                                <?=$rowTransportCap['transport_fit'] ?> - <?=$rowTransportCap['transport_load'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>      
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
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row" id="productNameDisplay">
                                <label for="productName" class="col-sm-1 col-form-label" style="width: 11%;">Product Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="productName" name="productName[]" multiple required>
                                        <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                            <option 
                                                value="<?=$rowProduct['name'] ?>" 
                                                data-price="<?=$rowProduct['price'] ?>" 
                                                data-code="<?=$rowProduct['product_code'] ?>" 
                                                data-high="<?=$rowProduct['high'] ?>" 
                                                data-low="<?=$rowProduct['low'] ?>" 
                                                data-variance="<?=$rowProduct['variance'] ?>" 
                                                data-description="<?=$rowProduct['description'] ?>">
                                                <?=$rowProduct['product_code'] ?> - <?=$rowProduct['name'] ?>
                                            </option>
                                        <?php } ?>
                                        <option value="Other" data-code="Other">Other</option>
                                    </select>      
                                </div>
                            </div>
                            <div class="row" id="rawMaterialDisplay" style="display:none;">
                                <label for="rawMaterialName" class="col-sm-1 col-form-label" style="width: 11%;">Raw Material Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="rawMaterialName" name="rawMaterialName[]" multiple required>
                                        <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                            <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] . ' - ' . $rowRowMat['name'] ?></option>
                                        <?php } ?>
                                        <option value="Other" data-code="Other">Other</option>
                                    </select>           
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row">
                                <label for="projectCode" class="col-sm-1 col-form-label" style="width: 11%;">Project Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="projectCode" name="projectCode[]" multiple required>
                                        <?php while($rowProject=mysqli_fetch_assoc($projects)){ ?>
                                            <option value="<?=$rowProject['id'] ?>"><?=$rowProject['project'] . ' - ' . $rowProject['project_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row">
                                <label for="otherRemarks" class="col-sm-1 col-form-label" style="width: 11%;">Other Remarks</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="dispatchSection">
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
                            <div class="row" id="containerDisplay">
                                <label for="containerNoInput" class="col-sm-4 col-form-label">Container No 1</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="containerNoInput" name="containerNoInput" placeholder="Container No">
                                </div>
                            </div>
                            <div class="row" id="emptyContainerDisplay" style="display:none" >
                                <label for="emptyContainerNo" class="col-sm-4 col-form-label" id="containerNo1Label">Container No 1</label>
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
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="purchaseOrder" class="col-sm-4 col-form-label">P/O No.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder">
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
                                        <option value="Empty Container">Primer Mover + Container</option>
                                        <option value="Container">Primer Mover</option>
                                        <!-- <option value="Different Container">Primer Mover + Different Bins</option> -->
                                    </select>   
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNoDisplay">
                            <div class="row">
                                <label for="sealNo" class="col-sm-4 col-form-label">Seal No 1</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sealNo" name="sealNo" placeholder="Seal No">
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNoReplaceDisplay" style="display:none;">
                            <div class="row">
                                
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No.">
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
                        <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2Display">
                            <div class="row">
                                <label for="containerNo2" class="col-sm-4 col-form-label">Container No 2</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="containerNo2" name="containerNo2" placeholder="Container No 2">
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="containerNo2ReplaceDisplay" style="display:none;">
                            <div class="row">
                                
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
                                <label for="destination" class="col-sm-4 col-form-label">Destination</label>
                                <div class="col-sm-8">
                                    <select class="form-select select2" id="destination" name="destination" required>
                                        <option selected="-">-</option>
                                        <?php while($rowDestination=mysqli_fetch_assoc($destination2)){ ?>
                                            <option value="<?=$rowDestination['name'] ?>" data-code="<?=$rowDestination['destination_code'] ?>"><?=$rowDestination['name'] ?></option>
                                        <?php } ?>
                                    </select>            
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2Display">
                            <div class="row">
                                <label for="sealNo2" class="col-sm-4 col-form-label">Seal No 2</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sealNo2" name="sealNo2" placeholder="Seal No 2">
                                </div>
                            </div>
                        </div> 
                        <div class="col-xxl-4 col-lg-4 mb-3" id="sealNo2ReplaceDisplay" style="display:none;">
                            <div class="row">
                                
                            </div>
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
                                <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                <div class="col-sm-8">
                                    <select class="form-select select2" id="transporter" name="transporter" required>
                                        <option selected="-">-</option>
                                        <?php while($rowTransporter=mysqli_fetch_assoc($transporter2)){ ?>
                                            <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                        <?php } ?>
                                    </select>                                                                                          
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-4 col-lg-4 mb-3">
                            <div class="row">
                                <label for="transportCap" class="col-sm-4 col-form-label">Transport Cap *</label>
                                <div class="col-sm-8">
                                    <select class="form-select select2" id="transportCap" name="transportCap" required>
                                        <?php while($rowTransportCap=mysqli_fetch_assoc($transportCap2)){ ?>
                                            <option 
                                                value="<?=$rowTransportCap['id'] ?>">
                                                <?=$rowTransportCap['transport_fit'] ?> - <?=$rowTransportCap['transport_load'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>      
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
                    <div class="row" style="display:none;">
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row" id="productNameDisplay">
                                <label for="productName" class="col-sm-1 col-form-label" style="width: 11%;">Product Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="productName" name="productName[]" multiple required>
                                        <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                            <option 
                                                value="<?=$rowProduct['name'] ?>" 
                                                data-price="<?=$rowProduct['price'] ?>" 
                                                data-code="<?=$rowProduct['product_code'] ?>" 
                                                data-high="<?=$rowProduct['high'] ?>" 
                                                data-low="<?=$rowProduct['low'] ?>" 
                                                data-variance="<?=$rowProduct['variance'] ?>" 
                                                data-description="<?=$rowProduct['description'] ?>">
                                                <?=$rowProduct['product_code'] ?> - <?=$rowProduct['name'] ?>
                                            </option>
                                        <?php } ?>
                                        <option value="Other" data-code="Other">Other</option>
                                    </select>      
                                </div>
                            </div>
                            <div class="row" id="rawMaterialDisplay" style="display:none;">
                                <label for="rawMaterialName" class="col-sm-1 col-form-label" style="width: 11%;">Raw Material Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="rawMaterialName" name="rawMaterialName[]" multiple required>
                                        <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                            <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['raw_mat_code'] . ' - ' . $rowRowMat['name'] ?></option>
                                        <?php } ?>
                                        <option value="Other" data-code="Other">Other</option>
                                    </select>           
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row">
                                <label for="projectCode" class="col-sm-1 col-form-label" style="width: 11%;">Project Code</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <select class="form-select select2" id="projectCode" name="projectCode[]" multiple required>
                                        <?php while($rowProject=mysqli_fetch_assoc($projects)){ ?>
                                            <option value="<?=$rowProject['id'] ?>"><?=$rowProject['project'] . ' - ' . $rowProject['project_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-12 col-lg-12 mb-3">
                            <div class="row">
                                <label for="otherRemarks" class="col-sm-1 col-form-label" style="width: 11%;">Other Remarks</label>
                                <div class="col-sm-11" style="width: 89%;">
                                    <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="productDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="no" name="no" readonly>
                <input type="text" class="form-control" id="weightProductId" name="weightProductId" hidden>
            </td>
            <td>
                <input type="text" class="form-control" id="product" name="product" style="background-color:white;" required>
            </td>
            <td>
                <input type="text" class="form-control" id="productPacking" name="productPacking" style="background-color:white;" required>
            </td>
            <td>
                <input type="number" class="form-control" id="productGross" name="productGross" style="background-color:white;" value="0" required>
            </td>
            <td>
                <input type="number" class="form-control" id="productTare" name="productTare" style="background-color:white;" value="0" required>
            </td>
            <td>
                <input type="number" class="form-control" id="productNett" name="productNett" style="background-color:white;" value="0" readonly required>
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="customerDetail">
        <tr class="details">
            <td style="width: 15%;">
                <input type="hidden" class="form-control" id="customerWeightId" name="customerWeightId">
                <input type="text" class="form-control" id="customerDeliveryNo" name="customerDeliveryNo" style="background-color:white;" required>
            </td>
            <td> <!-- customer -->
                <select class="form-control form-select select2" id="customer" name="customer" required>
                    <?php while($rowCustomer=mysqli_fetch_assoc($customer3)){ ?>
                        <option value="<?=$rowCustomer['id'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
            <td> <!-- product -->
                <select class="form-control form-select select2" id="customerProduct" name="customerProduct[]" multiple required>
                    <?php while($rowProduct=mysqli_fetch_assoc($product3)){ ?>
                        <option value="<?=$rowProduct['id'] ?>" ><?=$rowProduct['product_code'] ?> - <?=$rowProduct['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
            <td> <!-- project code -->
                <select class="form-control form-select select2" style="width: 100%; background-color:white;" id="customerProjectCode" name="customerProjectCode[]" multiple required>
                    <?php while($rowProject=mysqli_fetch_assoc($projects3)){ ?>
                        <option value="<?=$rowProject['id'] ?>"><?=$rowProject['project'] . ' - ' . $rowProject['project_name'] ?></option>
                    <?php } ?>
                </select>
            </td>
            <td style="width: 15%;">
                <input type="text" class="form-control" id="customerInternalDocNo" name="customerInternalDocNo" style="background-color:white;" required>
            </td>
            <td style="width: 15%;">
                <input type="text" class="form-control" id="customerRefNo" name="customerRefNo" style="background-color:white;" required>
            </td>
            <td style="text-align:center; width: 2%;">
                <button class="btn btn-danger form-control" id="remove" style="background-color: #f06548; height: 38px;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>
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
    let clickTimer = null;
    var companyData = null; // Global variable to store company data

    var grossIncomingDatePicker;
    var tareOutgoingDatePicker; 
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2; 
    var rowCount = $("#productTable").find(".details").length;
    var customerRowCount = 0;
    var productCodes = [];
    var clonedCustomerOptions = $('#clonedCustomer option').clone();
    var clonedSupplierOptions = $('#clonedSupplier option').clone();
    var clonedProductOptions = $('#clonedProduct option').clone();
    var clonedRawMatOptions = $('#clonedRawMaterial option').clone();
    var clonedProjectOptions = $('#clonedProjects option').clone();

    $(function () {
        var userRole = '<?=$role ?>';
        var ind = '<?=$indicator ?>';
        var userAllowEdit = '<?=$allowEdit ?>';
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        const last30 = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);
        last30.setDate(today.getDate() - 30);

        // Initialize all Select2 elements in the search bar
        $('#collapseSearch .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
        });

        $('#companyModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select a Company",
            dropdownParent: $('#companyModal') // Ensures dropdown is not cut off
        });

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });

        // Apply custom styling to Select2 elements in search bar
        resetSelect2Css();
        
        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: last30
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: '',
            clickOpens: false,
            allowInput: false
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

        grossIncomingDatePicker2 = $('#grossIncomingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
        });

        tareOutgoingDatePicker2 = $('#tareOutgoingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
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

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        $('#selectAllContainerCheckbox').on('change', function() {
            var checkboxes = $('#emptyContainerTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Handle status item clicks
        $('.status-item').on('click', function() {
            var status = $(this).data('status');
            var transaction = $(this).data('transaction');
            
            // Update the search form fields
            $('#statusSearch').val(transaction).trigger('change');
            $('#batchNoSearch').val(status).trigger('change');
            
            // Expand the search section if collapsed
            if (!$('#collapseSearch').hasClass('show')) {
                $('#collapseSearch').collapse('show');
            }
            
            // Trigger the search
            $('#filterSearch').click();
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
        var projectI = $('#projectSearch').val() ? $('#projectSearch').val() : '';
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
        var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
        var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';
        var invDelPoI = $('#invDelPoSearch').val() ? $('#invDelPoSearch').val() : '';
        var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';

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
                    supplier: supplierI,
                    vehicle: vehicleNoI,
                    invoice: invoiceNoI,
                    batch: batchNoI,
                    product: productSearchI,
                    rawMaterial: rawMaterialI,
                    project: projectI,
                    plant: plantNoI,
                    transactionId: transactionIdI,
                    containerNo: containerNoI,
                    sealNo: sealNoI,
                    invDelPo: invDelPoI,
                    company: companyI
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
                { data: 'company' },
                { data: 'transaction_id' },                
                { data: 'weight_type' },
                { data: 'transaction_status' },
                { data: 'gate', sortable: false },
                { data: 'lorry_plate_no1' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { data: 'gross_weight_by1' },
                /*{ data: 'tare_weight1' },
                { data: 'tare_weight1_date' },
                { data: 'nett_weight1' },
                { data: 'lorry_plate_no2' },
                { data: 'gross_weight2' },
                { data: 'gross_weight2_date' },
                { data: 'tare_weight2' },
                { data: 'tare_weight2_date' },
                { data: 'nett_weight2' },*/
                { 
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        let buttons = `<div class="row g-1 d-flex">`;

                        if (userRole == 'SADMIN' || userRole == 'ADMIN') {
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
                        }else {
                            if (row.is_complete != 'Y' ){
                                if (row.weight_type == 'Primer Mover + Container'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data}, 'Y')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-weight-hanging"></i>
                                        </button>
                                    </div>`;    
                                }else{
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data}, 'N')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-weight-hanging"></i>
                                        </button>
                                    </div>`;  
                                }
                            }
                        }

                        buttons += `
                        <div class="col-auto">
                            <button title="Complete" type="button" id="complete${data}" onclick="complete('${data}')" class="btn btn-success btn-sm">
                                <i class="ri-task-fill"></i>
                            </button>
                        </div>`;

                        if (row.is_approved == 'Y') {
                            if (row.weight_type != 'Primer Mover + Container'){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>`;
                            }
                        }

                        if (row.is_approved == 'N') {
                            buttons += `
                            <div class="col-auto">
                                <button title="Approve" type="button" id="approve${data}" onclick="approve(${data})" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i>
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
                $('#salesPending').text(settings.json.salesTotalPending);
                $('#salesComplete').text(settings.json.salesTotalComplete);
                $('#salesCancel').text(settings.json.salesTotalCancel);
                $('#purchasePending').text(settings.json.purchaseTotalPending);
                $('#purchaseComplete').text(settings.json.purchaseTotalComplete);
                $('#purchaseCancel').text(settings.json.purchaseTotalCancel);
                $('#localPending').text(settings.json.localTotalPending);
                $('#localComplete').text(settings.json.localTotalComplete);
                $('#localCancel').text(settings.json.localTotalCancel);
                $('#miscPending').text(settings.json.miscTotalPending);
                $('#miscComplete').text(settings.json.miscTotalComplete);
                $('#miscCancel').text(settings.json.miscTotalCancel);

                if(batchNoI == 'Y'){
                    $('#lorryTableLabel').text('Completed Records (Lorry)');
                }
                else{
                    $('#lorryTableLabel').text('Pending Records (Lorry)');
                }
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

                        if (userRole == 'SADMIN' || userRole == 'ADMIN') {
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
                                    <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data},'Y')" class="btn btn-warning btn-sm">
                                        <i class="fas fa-weight-hanging"></i>
                                    </button>
                                </div>`;
                            }
                        }

                        buttons += `
                        <div class="col-auto">
                            <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}', 'Y')" class="btn btn-info btn-sm">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>`;

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
            var grossIncoming = $('#grossIncoming').val() || 0;

            if($('#transactionStatus').val() == 'Sales' && ($('#customerTable').find('.details').length == 0)){
                alert('Please add at least one customer record.');
                return;
            }

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

            /*if (isComplete == 'Y' && variance != '') {
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
            }*/

            if(!grossIncoming || grossIncoming == 0){
                pass = false;
            }
            
            var isValid = true;

            // Remove any existing jQuery validation error labels for customer table fields
            $('#customerTable').find('label.error').remove();

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";
                var fieldValue = select2Field.val();

                // Check if the value is empty (for both single and multiple select)
                var isEmpty = fieldValue === "" || fieldValue === null || (Array.isArray(fieldValue) && fieldValue.length === 0);

                if (isEmpty) {
                    // Handle both single and multiple select2 elements
                    select2Container.find('.select2-selection--single, .select2-selection--multiple').each(function() {
                        this.style.setProperty('border', '1px solid red', 'important');
                    });

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    isValid = false;
                } else {
                    select2Container.find('.select2-selection--single, .select2-selection--multiple').each(function() {
                        this.style.removeProperty('border');
                    });
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
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Something wrong when saving!');
                        $("#failBtn").click();
                    }
                });
            }
            else{
                alert('Please filled in all the mandatory fields or make sure gross incoming weight is not "0"!!!');
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
            var grossIncoming = $('#grossIncoming').val() || "0";

            if($('#transactionStatus').val() == 'Sales' && ($('#customerTable').find('.details').length == 0)){
                alert('Please add at least one customer record.');
                return;
            }

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

            /*if (isComplete == 'Y' && variance != '') {
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
            }*/

            if(!grossIncoming || grossIncoming == "0"){
                pass = false;
            }

            var isEmptyContainer = 'N';
            if ($('#weightType').val() == 'Empty Container'){
                isEmptyContainer = 'Y';
            }

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";
                var fieldValue = select2Field.val();

                // Check if the value is empty (for both single and multiple select)
                var isEmpty = fieldValue === "" || fieldValue === null || (Array.isArray(fieldValue) && fieldValue.length === 0);

                if (isEmpty) {
                    // Handle both single and multiple select2 elements
                    select2Container.find('.select2-selection--single, .select2-selection--multiple').each(function() {
                        this.style.setProperty('border', '1px solid red', 'important');
                    });

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    isValid = false;
                } else {
                    select2Container.find('.select2-selection--single, .select2-selection--multiple').each(function() {
                        this.style.removeProperty('border');
                    });
                    select2Container.next('.select2-error').remove(); // Remove error message
                }
            });

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        $.post('php/print.php', {userID: obj.id, file: 'weight', isEmptyContainer: isEmptyContainer}, function(data){
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
            else{
                alert('Please filled in all the mandatory fields or make sure gross incoming weight is not "0"!!!');
            }
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
                        $("#failBtn").attr('data-toast-text', 'Something wrong when saving!');
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
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Something wrong when saving!');
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
                        $("#failBtn").attr('data-toast-text', 'Something wrong when saving!');
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
            var supplierI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
            var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var projectI = $('#projectSearch').val() ? $('#projectSearch').val() : '';
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';
            var containerNoI = $('#containerNoSearch').val() ? $('#containerNoSearch').val() : '';
            var sealNoI = $('#sealNoSearch').val() ? $('#sealNoSearch').val() : '';
            var invDelPoI = $('#invDelPoSearch').val() ? $('#invDelPoSearch').val() : '';
            var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';

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
                        supplier: supplierI,
                        vehicle: vehicleNoI,
                        invoice: invoiceNoI,
                        batch: batchNoI,
                        product: productSearchI,
                        rawMaterial: rawMaterialI,
                        project: projectI,
                        plant: plantNoI,
                        transactionId: transactionIdI,
                        containerNo: containerNoI,
                        sealNo: sealNoI,
                        invDelPo: invDelPoI,
                        company: companyI
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
                    { data: 'company' },
                    { data: 'transaction_id' },                
                    { data: 'weight_type' },
                    { data: 'transaction_status' },
                    { data: 'gate', sortable: false },
                    { data: 'lorry_plate_no1' },
                    { data: 'gross_weight1' },
                    { data: 'gross_weight1_date' },
                    { data: 'gross_weight_by1' },
                    /*{ data: 'tare_weight1' },
                    { data: 'tare_weight1_date' },
                    { data: 'nett_weight1' },
                    { data: 'lorry_plate_no2' },
                    { data: 'gross_weight2' },
                    { data: 'gross_weight2_date' },
                    { data: 'tare_weight2' },
                    { data: 'tare_weight2_date' },
                    { data: 'nett_weight2' },*/
                    { 
                        data: 'id',
                        class: 'action-button',
                        render: function (data, type, row) {
                            let buttons = `<div class="row g-1 d-flex">`;

                            if (userRole == 'SADMIN' || userRole == 'ADMIN') {
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
                                            <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data}, 'Y')" class="btn btn-warning btn-sm">
                                                <i class="fas fa-weight-hanging"></i>
                                            </button>
                                        </div>`;    
                                    }else{
                                        buttons += `
                                        <div class="col-auto">
                                            <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data}, 'N')" class="btn btn-warning btn-sm">
                                                <i class="fas fa-weight-hanging"></i>
                                            </button>
                                        </div>`;  
                                    }
                                }
                            }

                            buttons += `
                            <div class="col-auto">
                                <button title="Complete" type="button" id="complete${data}" onclick="complete('${data}')" class="btn btn-success btn-sm">
                                    <i class="ri-task-fill"></i>
                                </button>
                            </div>`;

                            if (row.is_approved == 'Y') {
                                if (row.weight_type != 'Primer Mover + Container'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}')" class="btn btn-info btn-sm">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>`;
                                }
                            }

                            if (row.is_approved == 'N') {
                                buttons += `
                                <div class="col-auto">
                                    <button title="Approve" type="button" id="approve${data}" onclick="approve(${data})" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i>
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
                    $('#salesPending').text(settings.json.salesTotalPending);
                    $('#salesComplete').text(settings.json.salesTotalComplete);
                    $('#salesCancel').text(settings.json.salesTotalCancel);
                    $('#purchasePending').text(settings.json.purchaseTotalPending);
                    $('#purchaseComplete').text(settings.json.purchaseTotalComplete);
                    $('#purchaseCancel').text(settings.json.purchaseTotalCancel);
                    $('#localPending').text(settings.json.localTotalPending);
                    $('#localComplete').text(settings.json.localTotalComplete);
                    $('#localCancel').text(settings.json.localTotalCancel);
                    $('#miscPending').text(settings.json.miscTotalPending);
                    $('#miscComplete').text(settings.json.miscTotalComplete);
                    $('#miscCancel').text(settings.json.miscTotalCancel);

                    if(batchNoI == 'Y'){
                        $('#lorryTableLabel').text('Completed Records (Lorry)');
                    }
                    else{
                        $('#lorryTableLabel').text('Pending Records (Lorry)');
                    }
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

                            if (userRole == 'SADMIN' || userRole == 'ADMIN') {
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
                                        <button title="Weight Out" type="button" id="weightOut${data}" onclick="weightOut(${data},'Y')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-weight-hanging"></i>
                                        </button>
                                    </div>`;
                                }
                            }

                            buttons += `
                            <div class="col-auto">
                                <button title="Print" type="button" id="print${data}" onclick="print('${data}', '${row.transaction_status}', 'Y')" class="btn btn-info btn-sm">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>`;

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
            /*$('#addModal').find('#grossCapture').show();
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
            $('#addModal').find('#company').val("").trigger('change');
            $('#addModal').find('#agent').val("").trigger('change');
            $('#addModal').find('#agentCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#destination').val("-").trigger('change');
            $('#addModal').find('#replacementContainer').val('').trigger('keyup');
            $('#addModal').find('#otherRemarks').val("");
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val("");
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
            $('#addModal').find('#projectCode').val("").trigger('change');

            // Show select and hide input readonly
            $('#addModal').find('#salesOrderEdit').val("").hide();
            $('#addModal').find('#purchaseOrderEdit').val("").hide();
            $('#addModal').find('#salesOrder').next('.select2-container').show();

            $('#addModal').find('#productTable').html('');
            rowCount = 0;

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            

            // $('#addModal').modal('show');
            
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
            });*/

            $('#isWeightOut').val(false);
            $('#companyModal').find('#companySelect').val("").trigger('change');
            $('#companyModal').modal('show');
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

        $('#addModal').on('change', '#weightType', function(){
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
                $('#addModal').find('#containerNo1Label').text("Pending Dispatch Bin");
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

        $('#addModal').on('keyup', '#replacementContainer', function(){
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

        $('#addModal').on('change', '#manualVehicle', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('.index-vehicle').hide();
                $('#vehiclePlateNo1').val('').prop('required', false).trigger('change');
                $('#vehicleNoTxt').show().attr('required', true);
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt').hide().prop('required', false).val('');
                $('.index-vehicle').show();
                $('#vehiclePlateNo1').attr('required', true);
            }
        });

        $('#addModal').on('keyup', '#vehicleNoTxt', function(){
            var x = $('#vehicleNoTxt').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt').val(x);
            var transactionStatus = $('#transactionStatus').val();

            if (x){
                $.post('php/getVehicle.php', {userID: x, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message && Object.keys(obj.message).length > 0) {
                            var customerName = obj.message.customer_name;
                            var customerCode = obj.message.customer_code;
                            var supplierName = obj.message.supplier_name;
                            var supplierCode = obj.message.supplier_code;

                            if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                                $('#addModal').find('#customerName').val(customerName).trigger('change');
                                $('#addModal').find('#customerCode').val(customerCode);
                            }else{
                                $('#addModal').find('#supplierName').val(supplierName).trigger('change');
                                $('#addModal').find('#supplierCode').val(supplierCode);
                            }
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
        });

        $('#addModal').on('change', '#vehiclePlateNo1', function(){
            var vehiclePlateNo1 = $(this).val();
            var transactionStatus = $('#transactionStatus').val();
            if (vehiclePlateNo1){
                $.post('php/getVehicle.php', {userID: vehiclePlateNo1, type: 'pullCustomer'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message && Object.keys(obj.message).length > 0) {
                            var customerName = obj.message.customer_name;   
                            var customerCode = obj.message.customer_code;
                            var supplierName = obj.message.supplier_name;
                            var supplierCode = obj.message.supplier_code;
                            var vehicleWeight= obj.message.vehicle_weight ?? '';

                            if (transactionStatus == 'Sales' || transactionStatus == 'Misc'){
                                $('#addModal').find('#customerName').val(customerName).trigger('change');
                                $('#addModal').find('#customerCode').val(customerCode);
                                $('#addModal').find('#grossIncoming').val(vehicleWeight).trigger('keyup');
                            }else{
                                $('#addModal').find('#supplierName').val(supplierName).trigger('change');
                                $('#addModal').find('#supplierCode').val(supplierCode);
                                $('#addModal').find('#tareOutgoing').val(vehicleWeight).trigger('keyup');
                            }
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
        });

        $('#addModal').on('change', '#manualVehicle2', function(){
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

        $('#addModal').on('keyup', '#vehicleNoTxt2', function(){
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

        $('#addModal').on('change', '#vehiclePlateNo2', function(){
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

        $('#addModal').on('keyup', '#grossIncoming', function(){
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

        $('#addModal').on('click', '#grossCapture', function(){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
        });

        $('#addModal').on('keyup', '#tareOutgoing', function(){
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

        $('#addModal').on('click', '#tareCapture', function(){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#addModal').on('change', '#nettWeight', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            }else{
                var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
                var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }

            // Enhancement to add additional product weight
            // if ($('#productTable tr').length > 0){
            //     let totalNett = 0;
            //     $('#productTable tr').each(function () {
            //         let nettVal = parseFloat($(this).find('input[id^="productNett"]').val()) || 0;
            //         totalNett += nettVal;
            //     });

            //     current = current + totalNett;
            // }

            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#reduceWeight').trigger('change');
            //$('#finalWeight').trigger('change');
        });
        
        $('#addModal').on('change', '#reduceWeight', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            }else{
                var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
                var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }

            // Enhancement to add additional product weight
            // if ($('#productTable tr').length > 0){
            //     let totalNett = 0;
            //     $('#productTable tr').each(function () {
            //         let nettVal = parseFloat($(this).find('input[id^="productNett"]').val()) || 0;
            //         totalNett += nettVal;
            //     });

            //     current = current + totalNett;
            // }

            var reduce = $(this).val() ? parseFloat($(this).val()) : 0;
            //var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var final = Math.abs(current - reduce);
            $('#currentWeight').text(final.toFixed(0));
            $('#finalWeight').val(final.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#addModal').on('change', '#finalWeight', function(){
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

        $('#addModal').on('change', '#orderWeight', function(){
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

        $('#addModal').on('change', '#supplierWeight', function(){
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

        $('#addModal').on('keyup', '#grossIncoming2', function(){
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

        $('#addModal').on('click', '#grossCapture2', function(){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#grossIncoming2').val(parseFloat(text).toFixed(0));
            $('#grossIncoming2').trigger('keyup');
        });

        $('#addModal').on('keyup', '#tareOutgoing2', function(){
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

        $('#addModal').on('click', '#tareCapture2', function(){
            event.preventDefault();
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing2').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing2').trigger('keyup');
        });

        $('#addModal').on('change', '#nettWeight2', function(){
            var weightType = $('#weightType').val();

            if (weightType == 'Different Container'){
                var current = $(this).val() ? parseFloat($(this).val()) : 0;
            }else{
                var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
                var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
                var current = Math.abs(nett1 - nett2);
            }

            // Enhancement to add additional product weight
            // if ($('#productTable tr').length > 0){
            //     let totalNett = 0;
            //     $('#productTable tr').each(function () {
            //         let nettVal = parseFloat($(this).find('input[id^="productNett"]').val()) || 0;
            //         totalNett += nettVal;
            //     });

            //     current = current + totalNett;
            // }

            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#reduceWeight').trigger('change');
            //$('#finalWeight').trigger('change');
        });

        $('#addModal').on('change', '#currentWeight', function(){
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
            handleTransactionStatusChange($(this).val());
        });

        //productName
        $('#addModal').on('change', '#productName', function(){
            var selectedOptions = $('#productName option:selected');
            var codes = [];
            selectedOptions.each(function() {
                var code = $(this).data('code');
                if (code) codes.push(code);
            });

            // Store codes as JSON string or comma-separated (both valid)
            $('#productCode').val(JSON.stringify(codes));
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
        $('#addModal').on('change', '#supplierName', function(){
            $('#supplierCode').val($('#supplierName :selected').data('code'));
        });

        //transporter
        $('#addModal').on('change', '#transporter', function(){
            $('#transporterCode').val($('#transporter :selected').data('code'));
        });

        //destination
        $('#addModal').on('change', '#destination', function(){
            $('#destinationCode').val($('#destination :selected').data('code'));
        });

        //plant
        $('#addModal').on('change', '#plant', function(){
            $('#plantCode').val($('#plant :selected').data('code'));
        });

        // SRP
        $('#addModal').on('change', '#agent', function(){
            $('#agentCode').val($('#agent :selected').data('code'));
        });

        //siteName
        $('#addModal').on('change', '#siteName', function(){
            $('#siteCode').val($('#siteName :selected').data('code'));
        });

        //customerName
        $('#addModal').on('change', '#customerName', function(){
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
        $('#addModal').on('change', '#rawMaterialName', function(){
            var selectedOptions = $('#rawMaterialName option:selected');
            var codes = [];
            selectedOptions.each(function() {
                var code = $(this).data('code');
                if (code) codes.push(code);
            });

            // Store codes as JSON string or comma-separated (both valid)
            $('#rawMaterialCode').val(JSON.stringify(codes));
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
        $('#addModal').on('change', '#emptyContainerNo', function(){
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
            } else if (emptyContainerNo) { 
                $.post('php/getEmptyContainer.php', {userID: emptyContainerNo}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){ 
                        $('#addModal').find('#company').val(obj.message.company).trigger('change');
                        var projectCodes = JSON.parse(obj.message.project_code);
                        $('#addModal').find('#projectCode').val(projectCodes).trigger('change');
                        $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                        $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                        $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                        $('#addModal').find('#sealNo').val(obj.message.seal_no);
                        $('#addModal').find('#otherRemarks').val(obj.message.remarks);

                        if (weightType != 'Different Container'){
                            $('#addModal').find('#containerNo2').val(obj.message.container_no2);
                            $('#addModal').find('#sealNo2').val(obj.message.seal_no2);
                        }

                        if (obj.message.transaction_status == 'Sales' || obj.message.transaction_status == 'Misc'){
                            $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                            var products = JSON.parse(obj.message.product_name);
                            $('#addModal').find('#productName').val(products).trigger('change');
                        }else{
                            $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change');
                            var rawMaterials = JSON.parse(obj.message.raw_mat_name);
                            $('#addModal').find('#rawMaterialName').val(rawMaterials).trigger('change');
                        }
                        $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                        $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                        $('#addModal').find('#destination').val(obj.message.destination).trigger('change');

            
                        $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                        $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                        grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date)); 
                        // $('#addModal').find('#grossIncomingDate').val(obj.message.gross_weight1_date);
                        $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                        tareOutgoingDatePicker.setDate(new Date(obj.message.tare_weight1_date));
                        // $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date);
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
                        
                        $('#tareOutgoing2').trigger('keyup');
                        
                        $('#normalCard').show();
                        $('#grossCapture').hide();
                        $('#tareCapture').hide();
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
            }
        });

        //Container No
        $('#addModal').on('keyup', '#containerNoInput', function(){
            var x = $('#containerNoInput').val();
            x = x.toUpperCase();
            $('#containerNoInput').val(x);
            $('#containerNo').val(x);
        });
        
        $('#addModal').on('change', '#containerNoInput', function(){
            $('#containerNo').val($(this).val());
        });

        //Seal No
        $('#addModal').on('keyup', '#sealNo', function(){
            var x = $('#sealNo').val();
            x = x.toUpperCase();
            $('#sealNo').val(x);
        });

        //Container No 2
        $('#addModal').on('keyup', '#containerNo2', function(){
            var x = $('#containerNo2').val();
            x = x.toUpperCase();
            $('#containerNo2').val(x);
        });

        //Seal No 2
        $('#addModal').on('keyup', '#sealNo2', function(){
            var x = $('#sealNo2').val();
            x = x.toUpperCase();
            $('#sealNo2').val(x);
        });

        //Container No Search
        $('#addModal').on('keyup', '#containerNoSearch', function(){
            var x = $('#containerNoSearch').val();
            x = x.toUpperCase();
            $('#containerNoSearch').val(x);
        });

        //Seal No Search
        $('#addModal').on('keyup', '#sealNoSearch', function(){
            var x = $('#sealNoSearch').val();
            x = x.toUpperCase();
            $('#sealNoSearch').val(x);
        });

        // Find and remove selected table rows
        $("#productTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#productTable tr").each(function (index) {
                $(this).find('input[name^="no"]').val(index + 1);
            });

            rowCount--;
        });

        // Event delegation for gross weight to calculate nett weight
        $("#productTable").on('change', 'input[id^="productGross"]', function(){
            // Retrieve the input's attributes
            var gross = parseFloat($(this).val());
            var tare = parseFloat($(this).closest('.details').find('input[id^="productTare"]').val());
            var nettWeight = Math.abs(gross - tare);

            // Update the respective inputs for productNett
            $(this).closest('.details').find('input[id^="productNett"]').val(nettWeight);
            $('#nettWeight').trigger('change');
        });

        // Event delegation for tare weight to calculate nett weight
        $("#productTable").on('change', 'input[id^="productTare"]', function(){
            // Retrieve the input's attributes
            var tare = $(this).val();
            var gross = parseFloat($(this).closest('.details').find('input[id^="productGross"]').val());
            var nettWeight = Math.abs(gross - tare);

            // Update the respective inputs for productNett
            $(this).closest('.details').find('input[id^="productNett"]').val(nettWeight);
            $('#nettWeight').trigger('change');
        });

        // Add additional products
        $(".add-product").click(function(){
            var $addContents = $("#productDetail").clone();
            $("#productTable").append($addContents.html());

            $("#productTable").find('.details:last').attr("id", "detail" + rowCount);
            $("#productTable").find('.details:last').attr("data-index", rowCount);
            $("#productTable").find('#remove:last').attr("id", "remove" + rowCount);

            $("#productTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowCount + 1);
            $("#productTable").find('#weightProductId:last').attr('name', 'weightProductId['+rowCount+']').attr("id", "weightProductId" + rowCount);
            $("#productTable").find('#product:last').attr('name', 'product['+rowCount+']').attr("id", "product" + rowCount);
            $("#productTable").find('#productPacking:last').attr('name', 'productPacking['+rowCount+']').attr("id", "productPacking" + rowCount);
            $("#productTable").find('#productGross:last').attr('name', 'productGross['+rowCount+']').attr("id", "productGross" + rowCount);
            $("#productTable").find('#productTare:last').attr('name', 'productTare['+rowCount+']').attr("id", "productTare" + rowCount);
            $("#productTable").find('#productNett:last').attr('name', 'productNett['+rowCount+']').attr("id", "productNett" + rowCount);

            rowCount++;
        });

        // Find and remove selected table rows
        $("#customerTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            customerRowCount--;
        });

        // Add additional customer
        $("#addCustomer").click(function(){
            var $addContents = $("#customerDetail").clone();
            $("#customerTable").append($addContents.html());

            $("#customerTable").find('.details:last').attr("id", "detail" + customerRowCount);
            $("#customerTable").find('.details:last').attr("data-index", customerRowCount);
            $("#customerTable").find('#remove:last').attr("id", "remove" + customerRowCount);

            $("#customerTable").find('#customerWeightId:last').attr('name', 'customerWeightId['+customerRowCount+']').attr("id", "customerWeightId" + customerRowCount);
            $("#customerTable").find('#customerDeliveryNo:last').attr('name', 'customerDeliveryNo['+customerRowCount+']').attr("id", "customerDeliveryNo" + customerRowCount);
            $("#customerTable").find('#customer:last').attr('name', 'customer['+customerRowCount+']').attr("id", "customer" + customerRowCount).val('');
            $("#customerTable").find('#customerProduct:last').attr('name', 'customerProduct['+customerRowCount+'][]').attr("id", "customerProduct" + customerRowCount).val('');
            $("#customerTable").find('#customerProjectCode:last').attr('name', 'customerProjectCode['+customerRowCount+'][]').attr("id", "customerProjectCode" + customerRowCount).val('');
            $("#customerTable").find('#customerInternalDocNo:last').attr('name', 'customerInternalDocNo['+customerRowCount+']').attr("id", "customerInternalDocNo" + customerRowCount);
            $("#customerTable").find('#customerRefNo:last').attr('name', 'customerRefNo['+customerRowCount+']').attr("id", "customerRefNo" + customerRowCount);

            customerRowCount++;

            reinitSelect2($('#addModal'));
            
            // Populate the new row with current company data if available
            if (companyData) {
                populateNewCustomerRow(customerRowCount - 1, companyData);
            }
        });

        $('#company').on('change', function(){
            var companyId = $(this).val();

            if (companyId != '' && companyId != null){
                // Get customers, suppliers, products, raw materials, project codes based on selected company
                $.post('php/getCompanyRelatedData.php', {companyId: companyId}, function(data)
                {
                    var obj = JSON.parse(data);
                    if(obj.status == 'success'){
                        companyData = obj.message;
                        populateCompanyData(companyData);
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

                $('#vehicle1Section').show();
                $('#companyModal').modal('hide');
                $('#addModal').modal('show');
            }else{
                $('#vehicle1Section').hide();
                $('#companyModal').modal('hide');
                $('#addModal').modal('hide');
            }
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
    
    function confirmCompanySelection(){
        var selectedCompanyId = $('#companySelect').val();
        //$('#addModal').modal('hide');

        if (selectedCompanyId != '' && selectedCompanyId != null){
            $('#company').val(selectedCompanyId);
            $('#addModal').find('#transactionStatus').val("Sales").trigger('change'); // Reset to default sales
            $('#companyModal').modal('hide');
            // $('#vehicle1Section').hide();
            // $('#weighingDetailsSection').empty();
            // $('#multiCustomerSection').hide();
            $('#addModal').modal('show');
        }else{
            alert('Please select a company to proceed.');
        }
    }

    function populateNewCustomerRow(rowIndex, companyData) {
        if (!companyData) return;
        
        // Populate customer dropdown for specific row
        if (companyData.Customer && companyData.Customer.length > 0){
            var customerOptions = buildOptions(companyData.Customer, 'customer_code', 'id', 'name');
            $('#customer' + rowIndex).empty().append(customerOptions).val('').trigger('change');
        }else{
            $('#customer' + rowIndex).empty().val('').trigger('change');
        }
        
        // Populate product dropdown for specific row
        if (companyData.Product && companyData.Product.length > 0){
            var customerProductOptions = '';
            for (var i = 0; i < companyData.Product.length; i++) {
                customerProductOptions += '<option value="' + companyData.Product[i]['id'] + '">' + companyData.Product[i]['product_code'] + ' - ' + companyData.Product[i]['name'] + '</option>';
            }
            $('#customerProduct' + rowIndex).empty().append(customerProductOptions).val('').trigger('change');
        }else{
            $('#customerProduct' + rowIndex).empty().val('').trigger('change');
        }
        
        // Populate project dropdown for specific row
        if (companyData.Projects && companyData.Projects.length > 0){
            var projectCodeOptions = buildOptions(companyData.Projects, '', 'id', ['project', 'project_name']);
            $('#customerProjectCode' + rowIndex).empty().append(projectCodeOptions).val('').trigger('change');
        }else{
            $('#customerProjectCode' + rowIndex).empty().val('').trigger('change');
        }
    }

    function populateCompanyData(companyData) {
        if (!companyData) return;
        
        // empty customer field and append new options
        if (companyData.Customer && companyData.Customer.length > 0){
            var customerOptions = buildOptions(companyData.Customer, 'customer_code', 'name', 'name');
            $('#customerName').empty().append(customerOptions).val('').trigger('change');
            
            // Also populate customer options for all customerDetail script rows
            var customerOptionsForScript = buildOptions(companyData.Customer, 'customer_code', 'id', 'name');
            $('#customerTable').find('select[id^="customer"]:not([id*="Product"]):not([id*="ProjectCode"])').each(function() {
                $(this).empty().append(customerOptionsForScript).val('').trigger('change');
            });
        }
        else{
            $('#customerName').empty().val('').trigger('change');
        }

        // empty supplier field and append new options
        if (companyData.Supplier && companyData.Supplier.length > 0){
            var supplierOptions = buildOptions(companyData.Supplier, 'supplier_code', 'name', 'name');
            $('#supplierName').empty().append(supplierOptions).val('').trigger('change');
        }
        else{
            $('#supplierName').empty().val('').trigger('change');
        }

        // empty raw mat field and append new options
        if (companyData.Raw_Mat && companyData.Raw_Mat.length > 0){
            var rawMaterialOptions = buildOptions(companyData.Raw_Mat, 'raw_mat_code', 'name', ['raw_mat_code', 'name']);
            $('#rawMaterialName').empty().append(rawMaterialOptions).val('').trigger('change');
        }
        else{
            $('#rawMaterialName').empty().val('').trigger('change');
        }

        // empty project code field and append new options
        if (companyData.Projects && companyData.Projects.length > 0){
            var projectCodeOptions = buildOptions(companyData.Projects, '', 'id', ['project', 'project_name']);
            $('#projectCode').empty().append(projectCodeOptions).val('').trigger('change');
            
            // Also populate project options for all customerDetail script rows
            $('#customerTable').find('select[id^="customerProjectCode"]').each(function() {
                $(this).empty().append(projectCodeOptions).val('').trigger('change');
            });
        }
        else{
            $('#projectCode').empty().val('').trigger('change');
        }
        
        // empty product name field and append new options
        if (companyData.Product && companyData.Product.length > 0){
            var productOptions = '';
            var customerProductOptions = '';
            for (var i = 0; i < companyData.Product.length; i++) {
                productOptions += '<option value="' + companyData.Product[i]['name'] + '" data-price="' + companyData.Product[i]['price'] + '" data-code="' + companyData.Product[i]['product_code'] + '" data-high="' + companyData.Product[i]['high'] + '" data-low="' + companyData.Product[i]['low'] + '" data-variance="' + companyData.Product[i]['variance'] + '" data-description="' + companyData.Product[i]['description'] + '">' + companyData.Product[i]['product_code'] + ' - ' + companyData.Product[i]['name'] + '</option>';
                customerProductOptions += '<option value="' + companyData.Product[i]['id'] + '">' + companyData.Product[i]['product_code'] + ' - ' + companyData.Product[i]['name'] + '</option>';
            }
            $('#productName').empty().append(productOptions).val('').trigger('change');
            
            // Also populate product options for all customerDetail script rows
            $('#customerTable').find('select[id^="customerProduct"]').each(function() {
                $(this).empty().append(customerProductOptions).val('').trigger('change');
            });
        }
        else{
            $('#productName').empty().val('').trigger('change');
        }
    }

    function buildOptions(items, codeKey, valueKey, textKey, separator = ' - ') {
        var options = '';
        for (var i = 0; i < items.length; i++) {
            var item = items[i];

            // Support single key (string) or multiple keys (array)
            var label = '';
            if (Array.isArray(textKey)) {
                // Join multiple fields with separator (e.g., " - ")
                label = textKey.map(k => item[k]).filter(Boolean).join(separator);
            } else {
                label = item[textKey] ?? '';
            }

            // Build data-code attribute only if codeKey is valid and not empty/null
            var dataCodeAttr = '';
            if (codeKey && item[codeKey] !== null && item[codeKey] !== undefined && item[codeKey] !== '') {
                dataCodeAttr = ' data-code="' + item[codeKey] + '"';
            }

            options += '<option value="' + items[i][valueKey] + '"' + dataCodeAttr + '>' + label + '</option>';
        }
        return options;
    }

    function addNewWeight(today){
        // Show Capture Buttons When Add New
        $('#addModal').find('#grossCapture').show();
        $('#addModal').find('#tareCapture').show();
        $('#addModal').find('#id').val("");
        $('#addModal').find('#currentWeight').text("0");
        $('#addModal').find('#transactionId').val("");
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
        $('#addModal').find('#replacementContainer').val('').trigger('keyup');
        $('#addModal').find('#otherRemarks').val("");
        $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
        $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
        $('#addModal').find('#grossIncoming').val("");
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
        $('#addModal').find('#manualWeightNo').trigger('click');
        //$('#addModal').find('#weighbridge').val("");
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
        $('#addModal').find('#projectCode').val("").trigger('change');
        $('#addModal').find('#transportCap').val("").trigger('change');
        $('#addModal').find('#mrnNo').val("");

        // Show select and hide input readonly
        $('#addModal').find('#salesOrderEdit').val("").hide();
        $('#addModal').find('#purchaseOrderEdit').val("").hide();
        $('#addModal').find('#salesOrder').next('.select2-container').show();

        $('#addModal').find('#productTable').html('');
        $('#addModal').find('#customerTable').html('');
        rowCount = 0;

        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal .select2[required]').each(function () {
            var select2Field = $(this);
            var select2Container = select2Field.next('.select2-container');
            
            select2Container.find('.select2-selection').css('border', ''); // Remove red border
            select2Container.next('.select2-error').remove(); // Remove error message
        });
        
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
        }else if(row.weight_type == 'Different Container'){
            weightType = 'Primer Mover + Different Bins';
        }else{
            weightType = row.weight_type;
        }

        var returnString = '';

        if (row.transaction_status != 'Sales') {
            returnString += `
                <!-- Customer/Supplier Section -->
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
            `;
        }else{
            var custTable = row.customer_table;

            returnString += `
                <p><span><strong style="font-size:120%; text-decoration: underline;">Customer</strong></span><br>
                <table class="table table-bordered" style="text-align: center;">
                    <thead class="table-primary">
                        <tr>
                            <th>Delivery No</th>
                            <th>Customer Name</th>
                            <th>Product Code</th>
                            <th>Project Code</th>
                            <th>Internal Doc. No.</th>
                            <th>Ref. No.</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #fff;">`;

                    custTable.forEach(function(item, index) {
                        returnString += `
                            <tr>
                                <td>
                                    ${item.delivery_no}
                                </td>
                                <td>
                                    ${item.customer_name}
                                </td>
                                <td>
                                    ${item.products}
                                </td>
                                <td> 
                                    ${item.projects}
                                </td>
                                <td>
                                    ${item.internal_doc_no}
                                </td>
                                <td>
                                    ${item.ref_no}
                                </td>
                            </tr>
                        `;
                    });

                    returnString += `
                    </tbody>
                </table>
            `;
        }
        
        returnString += `
        <hr>
        <!-- Delivery Order Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Delivery Order Information</strong></span><br>
            <div class="col-4">
                <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>WEIGHT TYPE:</strong> ${weightType}</p>
                <p><strong>TRANSACTION DATE:</strong> ${row.transaction_date}</p>
                <p><strong>MRN NO:</strong> ${row.mrn_no}</p>
                <p><strong>DESTINATION NAME:</strong> ${row.destination}</p>
                <p><strong>PLANT NAME:</strong> ${row.plant_name}</p>`;
                if (row.transaction_status != 'Sales'){
                    if (row.transaction_status == 'Purchase' || row.transaction_status == 'Local'){
                        returnString += `<p><strong>PURCHASE PRODUCT:</strong> ${row.product_rawmat_name}</p>`;
                    }else{
                        returnString += `<p><strong>SALES PRODUCT:</strong> ${row.product_rawmat_name}</p>`;
                    }

                    returnString += `<p><strong>PROJECT CODE:</strong> ${row.project_code}</p>`;
                }
                
        
            returnString += `
                <p><strong>OTHER REMARKS:</strong> ${row.remarks}</p>
            </div>
            <div class="col-4">
                <p><strong>CONTAINER NO:</strong> ${row.container_no}</p>
                <p><strong>SEAL NO:</strong> ${row.seal_no}</p>
                <p><strong>CONTAINER NO 2:</strong> ${row.container_no2}</p>
                <p><strong>SEAL NO 2:</strong> ${row.seal_no2}</p>
                <p style="text-wrap: auto;"><strong>TRANSPORTER NAME:</strong> ${row.transporter}</p>
                <p><strong>TRANSPORT CAPACITY:</strong> ${row.transport_cap}</p>
                <!--<p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>WEIGHT STATUS:</strong> ${transactionStatus}</p>
                <p><strong>WEIGHT TYPE:</strong> ${weightType}</p>
                <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
                <p><strong>PURCHASE ORDER:</strong> ${row.purchase_order}</p>
                <p><strong>CONTAINER NO 2:</strong> ${row.container_no2}</p>
                <p><strong>SEAL NO 2:</strong> ${row.seal_no2}</p>-->
            </div>
            <div class="col-4">
                <p><strong>P/O NO:</strong> ${row.purchase_order}</p>
                <p><strong>INVOICE NO:</strong> ${row.invoice_no}</p>
                ${row.transaction_status !== 'Sales' ? `<p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>` : ''}
                <p><strong>${row.transaction_status == 'Sales' || row.transaction_status == 'Misc' ? 'ORDER WEIGHT' : 'SUPPLIER WEIGHT'}:</strong>${row.transaction_status == 'Sales' || row.transaction_status == 'Misc' ? row.order_weight : row.supplier_weight}</p>
                <p><strong>WEIGHT DIFFERENCE:</strong> ${row.weight_different} kg</p>
                <p><strong>REDUCE WEIGHT:</strong> ${row.reduce_weight} kg</p>
            </div>
            <div class="col-12">
            </div>
        </div>
        <hr>

        <!-- Weighing Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Weighing Information</strong></span><br>
            <!-- Normal -->
            <div class="col-4">
                <p><strong>VEHICLE PLATE:</strong> ${row.lorry_plate_no1}</p>
                <p><strong>IN WEIGH BY:</strong> ${row.gross_weight_by1}</p>
                <p><strong>OUT WEIGH BY:</strong> ${row.tare_weight_by1}</p>
            </div>
            <div class="col-4">
                <p><strong>IN WEIGHT:</strong> ${row.gross_weight1}</p>
                <p><strong>OUT WEIGHT:</strong> ${row.tare_weight1}</p>
                <p><strong>NETT WEIGHT:</strong> ${row.nett_weight1}</p>
            </div>
            <div class="col-4">
                <p><strong>IN DATE / TIME:</strong> ${row.gross_weight1_date}</p>
                <p><strong>OUT DATE / TIME:</strong> ${row.tare_weight1_date}</p>
                <p><strong>SUB TOTAL WEIGHT:</strong> ${row.final_weight}</p>
            </div>
            <!-- Container -->
            <div class="col-4">
                <p><strong>VEHICLE PLATE 2:</strong> ${row.lorry_plate_no2}</p>
                <p><strong>IN WEIGH BY 2:</strong> ${row.gross_weight_by2}</p>
                <p><strong>OUT WEIGH BY 2:</strong> ${row.tare_weight_by2}</p>
            </div>
            <div class="col-4">
                <p><strong>IN WEIGHT 2:</strong> ${row.gross_weight2}</p>
                <p><strong>OUT WEIGHT 2:</strong> ${row.tare_weight2}</p>
                <p><strong>NETT WEIGHT 2:</strong> ${row.nett_weight2}</p>
            </div>
            <div class="col-4">
                <p><strong>IN DATE / TIME 2:</strong> ${row.gross_weight2_date}</p>
                <p><strong>OUT DATE / TIME 2:</strong> ${row.tare_weight2_date}</p>
            </div>
        </div>
        `;

        if (row.transaction_status == 'Sales'){

        }
        
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

    // Normal users just weight out
    function weightOut(id, isContainer){
        $('#spinnerLoading').show();
        $('#isWeightOut').val(true);

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
                // populate company append first
                $('#addModal').find('#company').val(obj.message.company);

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

                handleTransactionStatusChange(obj.message.transaction_status, function() {
                    // hide or show weighing details section
                    if (obj.message.company && obj.message.company != null && obj.message.company != ''){
                        $('#weighingDetailsSection').show();
                        $('#vehicle1Section').show();
                    }else{
                        $('#weighingDetailsSection').hide();
                        $('#vehicle1Section').hide();
                    }

                    setTimeout(() => {
                        $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).select2('destroy').select2();
                        $('#addModal').find('#id').val(obj.message.id);
                        $('#addModal').find('#transactionId').val(obj.message.transaction_id);
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
                        $('#addModal').find('#customerName').val(obj.message.customer_name).select2('destroy').select2();
                        $('#addModal').find('#customerCode').val(obj.message.customer_code);
                        $('#addModal').find('#supplierName').val(obj.message.supplier_name).select2('destroy').select2();
                        $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                        $('#addModal').find('#siteCode').val(obj.message.site_code);
                        $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                        $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                        $('#addModal').find('#agentCode').val(obj.message.agent_code);
                        var rawMatNames = JSON.parse(obj.message.raw_mat_name);
                        $('#addModal').find('#rawMaterialName').val(rawMatNames).trigger('change');
                        $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                        var productNames = JSON.parse(obj.message.product_name);
                        $('#addModal').find('#productName').val(productNames).trigger('change');
                        $('#addModal').find('#productCode').val(obj.message.product_code);
                        $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                        $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                        $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                        $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                        $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                        $('#addModal').find('#plantCode').val(obj.message.plant_code);
                        
                        $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                        var projectCodes = JSON.parse(obj.message.project_code);
                        $('#addModal').find('#projectCode').val(projectCodes).trigger('change');
                        $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                        grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date));
                        $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                        tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                        $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
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

                        //$('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                        //$('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
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
                        $('#addModal').find('#mrnNo').val(obj.message.mrn_no);
                        $('#addModal').find('#transportCap').val(obj.message.transport_cap).trigger('change');

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
                                resetSelect2Css();
                            });
                        }

                        $('#productTable').html('');
                        rowCount = 0;

                        if (obj.message.products.length > 0){
                            for(var i = 0; i < obj.message.products.length; i++){
                                var item = obj.message.products[i];
                                var $addContents = $("#productDetail").clone();
                                $("#productTable").append($addContents.html());

                                $("#productTable").find('.details:last').attr("id", "detail" + rowCount);
                                $("#productTable").find('.details:last').attr("data-index", rowCount);
                                $("#productTable").find('#remove:last').attr("id", "remove" + rowCount);

                                $("#productTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowCount + 1);
                                $("#productTable").find('#weightProductId:last').attr('name', 'weightProductId['+rowCount+']').attr("id", "weightProductId" + rowCount).val(item.id);
                                $("#productTable").find('#product:last').attr('name', 'product['+rowCount+']').attr("id", "product" + rowCount).val(item.product);
                                $("#productTable").find('#productPacking:last').attr('name', 'productPacking['+rowCount+']').attr("id", "productPacking" + rowCount).val(item.product_packing);
                                $("#productTable").find('#productGross:last').attr('name', 'productGross['+rowCount+']').attr("id", "productGross" + rowCount).val(item.product_gross);
                                $("#productTable").find('#productTare:last').attr('name', 'productTare['+rowCount+']').attr("id", "productTare" + rowCount).val(item.product_tare);
                                $("#productTable").find('#productNett:last').attr('name', 'productNett['+rowCount+']').attr("id", "productNett" + rowCount).val(item.product_nett);

                                rowCount++;
                            }
                        }

                        $('#customerTable').html('');
                        customerRowCount = 0;

                        setTimeout(function() {
                            if (obj.message.customers.length > 0){
                                for(var i = 0; i < obj.message.customers.length; i++){
                                    var item = obj.message.customers[i];
                                    var customerProducts = JSON.parse(item.product_id);
                                    var customerProjects = JSON.parse(item.project_id);

                                    var $addContents = $("#customerDetail").clone();
                                    $("#customerTable").append($addContents.html()); 

                                    // Populate the new row with current company data if available
                                    if (companyData) {
                                        populateNewCustomerRow(customerRowCount, companyData);
                                    }

                                    $("#customerTable").find('.details:last').attr("id", "detail" + customerRowCount);
                                    $("#customerTable").find('.details:last').attr("data-index", customerRowCount);
                                    $("#customerTable").find('#remove:last').attr("id", "remove" + customerRowCount);

                                    $("#customerTable").find('#customerWeightId:last').attr('name', 'customerWeightId['+customerRowCount+']').attr("id", "customerWeightId" + customerRowCount).val(item.id);
                                    $("#customerTable").find('#customerDeliveryNo:last').attr('name', 'customerDeliveryNo['+customerRowCount+']').attr("id", "customerDeliveryNo" + customerRowCount).val(item.delivery_no);
                                    $("#customerTable").find('#customer:last').attr('name', 'customer['+customerRowCount+']').attr("id", "customer" + customerRowCount).val(item.customer_id);
                                    $("#customerTable").find('#customerProduct:last').attr('name', 'customerProduct['+customerRowCount+'][]').attr("id", "customerProduct" + customerRowCount).val(customerProducts);
                                    $("#customerTable").find('#customerProjectCode:last').attr('name', 'customerProjectCode['+customerRowCount+'][]').attr("id", "customerProjectCode" + customerRowCount).val(customerProjects);
                                    $("#customerTable").find('#customerInternalDocNo:last').attr('name', 'customerInternalDocNo['+customerRowCount+']').attr("id", "customerInternalDocNo" + customerRowCount).val(item.internal_doc_no);
                                    $("#customerTable").find('#customerRefNo:last').attr('name', 'customerRefNo['+customerRowCount+']').attr("id", "customerRefNo" + customerRowCount).val(item.ref_no);

                                    customerRowCount++;

                                    // Initialize Select2 once after all rows are added
                                    reinitSelect2($('#addModal'));
                                }
                            }
                            
                            // Make all customerTable fields readonly and disabled
                            $('#customerTable').find('input, select, textarea, button').each(function() {
                                if ($(this).is('select')) {
                                    $(this).prop('disabled', true).trigger('change');
                                } else {
                                    $(this).prop('readonly', true).prop('disabled', true);
                                }
                            });
                        }, 500);
                        
                        // Initialize Select2 once after all rows are added
                        reinitSelect2($('#addModal'));
                    }, 500);
                });
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
                resetSelect2Css();

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

                // Make all inputs, selects, and textareas readonly-like except specific ones
                $('#addModal').find('input, select, textarea').each(function() {
                    var id = $(this).attr('id');

                    // Skip editable fields
                    if (id === 'grossIncoming' || id === 'tareOutgoing' || id === 'grossIncoming2' || id === 'tareOutgoing2') {
                        $(this).prop('readonly', false).prop('disabled', false);
                        if ($(this).hasClass('select2')) {
                            $(this).select2('enable', true);
                        }
                    } else {
                        if ($(this).is('select') && $(this).hasClass('select2')) {
                            $(this).select2('enable', false);
                        } else if ($(this).is('select')) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('readonly', true);
                        }
                    }
                });

                $('#addModal').find('#addCustomer').hide();

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

    // Admin can edit
    function edit(id, isContainer){
        $('#spinnerLoading').show();
        $('#isWeightOut').val(false);

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
                // populate company append first
                $('#addModal').find('#company').val(obj.message.company);

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

                handleTransactionStatusChange(obj.message.transaction_status, function() {
                    // hide or show weighing details section AFTER UI is set up
                    if (obj.message.company && obj.message.company != null && obj.message.company != ''){
                        $('#weighingDetailsSection').show();
                        $('#vehicle1Section').show();
                    }else{
                        $('#weighingDetailsSection').hide();
                        $('#vehicle1Section').hide();
                    }

                    setTimeout(() => {
                        $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).select2('destroy').select2();
                        $('#addModal').find('#id').val(obj.message.id);
                        $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                        $('#addModal').find('#weightType').val(obj.message.weight_type);
                        $('#addModal').find('#customerType').val(obj.message.customer_type);
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
                        $('#addModal').find('#customerName').val(obj.message.customer_name).select2('destroy').select2();
                        $('#addModal').find('#customerCode').val(obj.message.customer_code);
                        $('#addModal').find('#supplierName').val(obj.message.supplier_name).select2('destroy').select2();
                        $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                        $('#addModal').find('#siteCode').val(obj.message.site_code);
                        $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                        $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                        $('#addModal').find('#agentCode').val(obj.message.agent_code);
                        var rawMatNames = JSON.parse(obj.message.raw_mat_name);
                        $('#addModal').find('#rawMaterialName').val(rawMatNames).trigger('change');
                        $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                        var productNames = JSON.parse(obj.message.product_name);
                        $('#addModal').find('#productName').val(productNames).trigger('change');
                        $('#addModal').find('#productCode').val(obj.message.product_code);
                        $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                        $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                        $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                        $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                        $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                        $('#addModal').find('#plantCode').val(obj.message.plant_code);
                        $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                        var projectCodes = JSON.parse(obj.message.project_code);
                        $('#addModal').find('#projectCode').val(projectCodes).trigger('change');
                        $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                        grossIncomingDatePicker.setDate(new Date(obj.message.gross_weight1_date));
                        $('#addModal').find('#grossWeightBy1').val(obj.message.gross_weight_by1);
                        $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                        tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                        $('#addModal').find('#tareWeightBy1').val(obj.message.tare_weight_by1);
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

                        //$('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                        //$('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
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
                        $('#addModal').find('#mrnNo').val(obj.message.mrn_no);
                        $('#addModal').find('#transportCap').val(obj.message.transport_cap).trigger('change');
                        
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
                                resetSelect2Css();
                            });
                        }

                        $('#productTable').html('');
                        rowCount = 0;

                        if (obj.message.products.length > 0){
                            for(var i = 0; i < obj.message.products.length; i++){
                                var item = obj.message.products[i];
                                var $addContents = $("#productDetail").clone();
                                $("#productTable").append($addContents.html());

                                $("#productTable").find('.details:last').attr("id", "detail" + rowCount);
                                $("#productTable").find('.details:last').attr("data-index", rowCount);
                                $("#productTable").find('#remove:last').attr("id", "remove" + rowCount);

                                $("#productTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowCount + 1);
                                $("#productTable").find('#weightProductId:last').attr('name', 'weightProductId['+rowCount+']').attr("id", "weightProductId" + rowCount).val(item.id);
                                $("#productTable").find('#product:last').attr('name', 'product['+rowCount+']').attr("id", "product" + rowCount).val(item.product);
                                $("#productTable").find('#productPacking:last').attr('name', 'productPacking['+rowCount+']').attr("id", "productPacking" + rowCount).val(item.product_packing);
                                $("#productTable").find('#productGross:last').attr('name', 'productGross['+rowCount+']').attr("id", "productGross" + rowCount).val(item.product_gross);
                                $("#productTable").find('#productTare:last').attr('name', 'productTare['+rowCount+']').attr("id", "productTare" + rowCount).val(item.product_tare);
                                $("#productTable").find('#productNett:last').attr('name', 'productNett['+rowCount+']').attr("id", "productNett" + rowCount).val(item.product_nett);

                                rowCount++;
                            }
                        }

                        $('#customerTable').html('');
                        customerRowCount = 0;

                        setTimeout(function() {
                            if (obj.message.customers.length > 0){
                                for(var i = 0; i < obj.message.customers.length; i++){
                                    var item = obj.message.customers[i];
                                    var customerProducts = JSON.parse(item.product_id);
                                    var customerProjects = JSON.parse(item.project_id);

                                    var $addContents = $("#customerDetail").clone();
                                    $("#customerTable").append($addContents.html()); 

                                    // Populate the new row with current company data if available
                                    if (companyData) {
                                        populateNewCustomerRow(customerRowCount, companyData);
                                    }

                                    $("#customerTable").find('.details:last').attr("id", "detail" + customerRowCount);
                                    $("#customerTable").find('.details:last').attr("data-index", customerRowCount);
                                    $("#customerTable").find('#remove:last').attr("id", "remove" + customerRowCount);

                                    $("#customerTable").find('#customerWeightId:last').attr('name', 'customerWeightId['+customerRowCount+']').attr("id", "customerWeightId" + customerRowCount).val(item.id);
                                    $("#customerTable").find('#customerDeliveryNo:last').attr('name', 'customerDeliveryNo['+customerRowCount+']').attr("id", "customerDeliveryNo" + customerRowCount).val(item.delivery_no);
                                    $("#customerTable").find('#customer:last').attr('name', 'customer['+customerRowCount+']').attr("id", "customer" + customerRowCount).val(item.customer_id);
                                    $("#customerTable").find('#customerProduct:last').attr('name', 'customerProduct['+customerRowCount+'][]').attr("id", "customerProduct" + customerRowCount).val(customerProducts);
                                    $("#customerTable").find('#customerProjectCode:last').attr('name', 'customerProjectCode['+customerRowCount+'][]').attr("id", "customerProjectCode" + customerRowCount).val(customerProjects);
                                    $("#customerTable").find('#customerInternalDocNo:last').attr('name', 'customerInternalDocNo['+customerRowCount+']').attr("id", "customerInternalDocNo" + customerRowCount).val(item.internal_doc_no);
                                    $("#customerTable").find('#customerRefNo:last').attr('name', 'customerRefNo['+customerRowCount+']').attr("id", "customerRefNo" + customerRowCount).val(item.ref_no);

                                    customerRowCount++;
                                }
                                
                                // Initialize Select2 for customerTable elements after all rows are added
                                $('#customerTable').find('select').each(function() {
                                    $(this).select2({
                                        allowClear: true,
                                        placeholder: "Please Select",
                                        dropdownParent: $('#addModal')
                                    });
                                });

                                // Apply custom styling to Select2 elements in search bar
                                resetSelect2Css();
                            }
                        }, 500);
                        
                        // Initialize Select2 once after all rows are added
                        reinitSelect2($('#addModal'));
                    }, 500);

                    
                });

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
                resetSelect2Css();

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

    function handleTransactionStatusChange(transactionStatus, callback) {
        var today = new Date();
        var customerType = $('#addModal').find('#customerType').val();
        var weightType = $('#addModal').find('#weightType').val();

        if(weightType == 'Container'){
            $.post('php/getContainers.php', {userID: transactionStatus}, function (data){
                var obj = JSON.parse(data);

                if (obj.status == 'success'){
                    if (obj.message.length > 0){
                        $('#addModal').find('#emptyContainerNo').empty();
                        $('#addModal').find('#emptyContainerNo').append(`<option selected="-">-</option>`);

                        for (var i = 0; i < obj.message.length; i++) {
                            var container_no = obj.message[i].container_no;
                            $('#addModal').find('#emptyContainerNo').append(
                                '<option value="'+container_no+'">'+container_no+'</option>'
                            );  
                        }
                    }
                }
                else {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
            });
        }

        if(transactionStatus == "Purchase" || transactionStatus == "Local" || transactionStatus == "Misc"){            
            $('#weighingDetailsSection').empty();
            var receivingContent = $('#receivingSection').html();
            if (receivingContent) {
                $('#weighingDetailsSection').html(receivingContent);
            } else {
                console.log('ERROR: receivingSection is empty or not found');
            }
            $('#multiCustomerSection').hide();

            reinitSelect2($('#addModal'));
            $('#transactionDate').flatpickr({
                dateFormat: "d-m-Y",
                defaultDate: '',
                clickOpens: false,
                allowInput: false
            });

            addNewWeight(today);
            $('#company').trigger('change');

            if (transactionStatus == "Purchase" || transactionStatus == "Local") {
                $('#mrnDisplay').show();
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
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
            } else {
                $('#mrnDisplay').hide();
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divSupplierName').hide();
                $('#divOrderWeight').show();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#addModal').find('#divPoSupplyWeight').hide();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
            }
        }
        else if (transactionStatus == "Sales"){            
            $('#multiCustomerSection').show();
            $('#weighingDetailsSection').empty();
            var dispatchContent = $('#dispatchSection').html();
            if (dispatchContent) {
                $('#weighingDetailsSection').append(dispatchContent);
            } else {
                console.log('ERROR: dispatchSection is empty or not found');
            }
            
            reinitSelect2($('#addModal'));
            $('#transactionDate').flatpickr({
                dateFormat: "d-m-Y",
                defaultDate: '',
                clickOpens: false,
                allowInput: false
            });

            addNewWeight(today);
            $('#company').trigger('change');
            $('#divWeightDifference').show();
            $('#divSupplierWeight').hide();
            $('#addModal').find('#orderWeight').val("0");
            $('#addModal').find('#supplierWeight').val("");
            $('#divSupplierName').hide();
            $('#divOrderWeight').show();
            $('#divCustomerName').show();
            $('#rawMaterialDisplay').hide();
            $('#productNameDisplay').show();
            $('#addModal').find('#divPoSupplyWeight').hide();
            $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
            $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
        }
        
        if (callback) callback();
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

    function print(id, transactionStatus, isEmptyContainer = 'N') {
        var prePrintStatus = 'N';

        $.post('php/print.php', {userID: id, file: 'weight', prePrint: prePrintStatus, isEmptyContainer: isEmptyContainer}, function(data){
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

    function complete(id){
        $('#spinnerLoading').show();
        if (confirm('Are you sure you want to complete this item?')) {
            $.post('php/completeWeight.php', {userID: id}, function(data){
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    $('#spinnerLoading').hide();
                    table.ajax.reload();
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
                    $("#failBtn").attr('data-toast-text', "Something Wrong");
                    $("#failBtn").click();
                }
                $('#spinnerLoading').hide();
            });
        }
    }
    </script>
</body>
</html>