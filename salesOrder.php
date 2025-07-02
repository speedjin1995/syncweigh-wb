<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";
// $plantId = $_SESSION['plant'];

$customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0'");
$company = $db->query("SELECT * FROM Company");
$company2 = $db->query("SELECT * FROM Company");
$site = $db->query("SELECT * FROM Site WHERE status = '0'");
$site2 = $db->query("SELECT * FROM Site WHERE status = '0'");
$agent = $db->query("SELECT * FROM Agents WHERE status = '0'");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0'");
$product = $db->query("SELECT * FROM Product WHERE status = '0'");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0'");
$plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
$plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
$vehicle = $db->query("SELECT * FROM Vehicle WHERE status = '0'");

?>

<head>

    <title>Sales Order | Synctronix - Weighing System</title>
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

<!-- <div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div> -->

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
                                    <div class="card-header fs-5" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch">
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
                                                            <label for="statusSearch" class="form-label">Status</label>
                                                            <select id="statusSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <option value="Open">Open</option>
                                                                <option value="Close">Close</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->                                   
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="companySearch" class="form-label">Company</label>
                                                            <select id="companySearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowCompanyF=mysqli_fetch_assoc($company2)){ ?>
                                                                    <option value="<?=$rowCompanyF['company_code'] ?>"><?=$rowCompanyF['company_code']. ' - ' .$rowCompanyF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="siteSearch" class="form-label">Site</label>
                                                            <select id="siteSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowSiteF=mysqli_fetch_assoc($site2)){ ?>
                                                                    <option value="<?=$rowSiteF['site_code'] ?>"><?=$rowSiteF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="productSearch" class="form-label">Product</label>
                                                            <select id="productSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowProductF=mysqli_fetch_assoc($product2)){ ?>
                                                                    <option value="<?=$rowProductF['product_code'] ?>"><?=$rowProductF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer No</label>
                                                            <select id="customerNoSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowPF = mysqli_fetch_assoc($customer2)){ ?>
                                                                    <option value="<?=$rowPF['customer_code'] ?>"><?=$rowPF['name'] ?></option>
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
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Sales Order</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="company" class="col-sm-4 col-form-label">Company</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="company" name="company" required>
                                                                                            <?php while($rowCompany=mysqli_fetch_assoc($company)){ ?>
                                                                                                <option value="<?=$rowCompany['company_code'] ?>" data-name="<?=$rowCompany['name'] ?>"><?=$rowCompany['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="customer" class="col-sm-4 col-form-label">Customer</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="customer" name="customer">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['customer_code'] ?>" data-name="<?=$rowCustomer['name'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                                                            
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="site" class="col-sm-4 col-form-label">Site</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="site" name="site">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowSite=mysqli_fetch_assoc($site)){ ?>
                                                                                                <option value="<?=$rowSite['site_code'] ?>" data-name="<?=$rowSite['name'] ?>"><?=$rowSite['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="orderDate" class="col-sm-4 col-form-label">Order Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="orderDate" name="orderDate">
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="orderNo" class="col-sm-4 col-form-label">Customer P/O Number</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="orderNo" name="orderNo" placeholder="Customer P/O Number" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="soNo" class="col-sm-4 col-form-label">S/O Number</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="soNo" name="soNo" placeholder="S/O Number">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="agent" class="col-sm-4 col-form-label">Sales Representative</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="agent" name="agent">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowAgent=mysqli_fetch_assoc($agent)){ ?>
                                                                                                <option value="<?=$rowAgent['agent_code'] ?>" data-name="<?=$rowAgent['name'] ?>"><?=$rowAgent['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="destinationCode" class="col-sm-4 col-form-label">Destination</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="destinationCode" name="destinationCode">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                                <option value="<?=$rowDestination['destination_code'] ?>" data-name="<?=$rowDestination['name'] ?>"><?=$rowDestination['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="product" class="col-sm-4 col-form-label">Product</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="product" name="product" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option value="<?=$rowProduct['product_code'] ?>" data-name="<?=$rowProduct['name'] ?>"><?=$rowProduct['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="plant" name="plant">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                                                                                                <option value="<?=$rowPlant['plant_code'] ?>" data-name="<?=$rowPlant['name'] ?>"><?=$rowPlant['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="transporter" name="transporter" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['transporter_code'] ?>" data-name="<?=$rowTransporter['name'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehicle" class="col-sm-4 col-form-label">Vehicle</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="vehicle" name="vehicle" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowVehicle=mysqli_fetch_assoc($vehicle)){ ?>
                                                                                                <option value="<?=$rowVehicle['veh_number'] ?>"><?=$rowVehicle['veh_number'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="exDel" class="col-sm-4 col-form-label">EX-Quarry / Delivered</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="exDel" name="exDel" required>
                                                                                            <option value="E">E</option>
                                                                                            <option value="D">D</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="orderQty" class="col-sm-4 col-form-label">Order Quantity</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="orderQty" name="orderQty" required>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="remarks" class="col-sm-4 col-form-label">Remarks</label>
                                                                                    <div class="col-sm-8">
                                                                                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Remarks"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <input type="hidden" class="form-control" id="id" name="id">                                                                 
                                                                            <input type="hidden" class="form-control" id="companyName" name="companyName">                                                                   
                                                                            <input type="hidden" class="form-control" id="customerName" name="customerName">                                                                   
                                                                            <input type="hidden" class="form-control" id="siteName" name="siteName">                                                                   
                                                                            <input type="hidden" class="form-control" id="agentName" name="agentName">   
                                                                            <input type="hidden" class="form-control" id="destinationName" name="destinationName">
                                                                            <input type="hidden" class="form-control" id="productName" name="productName">
                                                                            <input type="hidden" class="form-control" id="plantName" name="plantName">
                                                                            <input type="hidden" class="form-control" id="transporterName" name="transporterName">                                               
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-success" id="submitSO">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="uploadModal" style="display:none">
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
                                                        <button type="button" class="btn btn-success" id="uploadSo">Save changes</button>
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
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Sales Orders</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="template/So_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        Download Template 
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExcel" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Import Sales Orders
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <button type="button" id="addSalesOrder" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New S/O
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Company Code</th>
                                                                    <th>Company Name</th>
                                                                    <th>Customer Code</th>
                                                                    <th>Customer Name</th>
                                                                    <th>Plant Code</th>
                                                                    <th>Plant Name</th>
                                                                    <th>Product Code</th>
                                                                    <th>Product Name</th>
                                                                    <th>Customer P/O No.</th>
                                                                    <th>S/O No.</th>
                                                                    <th>Order Date</th>
                                                                    <th>EXQ/DEL</th>
                                                                    <th>Balance</th>
                                                                    <th>Modified Date</th>
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

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#orderDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('#deliveryDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ''
        });

        $('.select2').each(function() {
            $(this).select2({
                allowClear: true,
                placeholder: "Please Select",
                // Conditionally set dropdownParent based on the element’s location
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal-body') : undefined
            });
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

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
        var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';
        var siteI = $('#siteSearch').val() ? $('#siteSearch').val() : '';
        var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var productI = $('#productSearch').val() ? $('#productSearch').val() : '';

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterSalesOrder.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    company: companyI,
                    site: siteI,
                    plant: plantI,
                    customer: customerNoI,
                    product: productI,
                } 
            },
            'columns': [
                { 
                    data: 'company_code',
                    class: 'company_column'
                },
                { data: 'company_name' },
                { data: 'customer_code' },
                { data: 'customer_name' },
                { data: 'plant_code' },
                { data: 'plant_name' },
                { data: 'product_code' },
                { data: 'product_name' },
                { data: 'order_no' },
                { data: 'so_no' },
                { data: 'order_date' },
                { data: 'exquarry_or_delivered' },
                { data: 'balance' },
                { data: 'modified_date' },
                {
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        let buttons = `
                            <div class="row g-1 d-flex">
                                <div class="col-auto">
                                    <button title="Edit" type="button" id="edit${data}" onclick="edit(${data})" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>`;

                            if (row.status == 'Open'){
                                buttons += `
                                <div class="col-auto">
                                    <button title="Complete" type="button" id="complete${data}" onclick="complete(${data})" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>`;
                            }
                            
                            buttons += `
                                <div class="col-auto">
                                    <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data})" class="btn btn-danger btn-sm">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>`;

                        return buttons;
                    }
                }

            ]
        });

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';
            var siteI = $('#siteSearch').val() ? $('#siteSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';

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
                'ajax': {
                    'url':'php/filterSalesOrder.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        company: companyI,
                        site: siteI,
                        plant: plantI,
                        customer: customerNoI,
                        product: productI,
                    } 
                },
                'columns': [
                    { 
                        data: 'company_code',
                        class: 'company_column'
                    },
                    { data: 'company_name' },
                    { data: 'customer_code' },
                    { data: 'customer_name' },
                    { data: 'plant_code' },
                    { data: 'plant_name' },
                    { data: 'product_code' },
                    { data: 'product_name' },
                    { data: 'order_no' },
                    { data: 'so_no' },
                    { data: 'order_date' },
                    { data: 'exquarry_or_delivered' },
                    { data: 'balance' },
                    { data: 'modified_date' },
                    {
                        data: 'id',
                        class: 'action-button',
                        render: function (data, type, row) {
                            let buttons = `
                                <div class="row g-1 d-flex">
                                    <div class="col-auto">
                                        <button title="Edit" type="button" id="edit${data}" onclick="edit(${data})" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>`;

                                if (row.status == 'Open'){
                                    buttons += `
                                    <div class="col-auto">
                                        <button title="Complete" type="button" id="complete${data}" onclick="complete(${data})" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>`;
                                }
                                
                                buttons += `
                                    <div class="col-auto">
                                        <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data})" class="btn btn-danger btn-sm">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>`;

                            return buttons;
                        }
                    }
                ]
            });
        });

        // Add event listener for opening and closing details on row click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this); // The row that was clicked
            var row = table.row(tr);

            // Exclude specific td elements by checking the event target
            if ($(e.target).closest('td').hasClass('company_column') || $(e.target).closest('td').hasClass('action-button')) {
                return;
            }

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.post('php/getSalesOrder.php', { userID: row.data().id, format: 'EXPANDABLE' }, function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status === 'success') {
                        row.child(format(obj.message)).show();
                        tr.addClass("shown");
                    }
                });
            }
        });

        $('#submitSO').on('click', function(){
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

            if($('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/salesOrder.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success')
                    {
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed')
                    {
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                    else
                    {
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#uploadSo').on('click', function(){
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
                url: 'php/uploadSo.php',
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
                        window.location.reload();
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

        $('#addSalesOrder').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#company').val($('#company option:first').val()).trigger('change');
            $('#addModal').find('#customer').val("").trigger('change');
            $('#addModal').find('#site').val("").trigger('change');
            $('#addModal').find('#orderDate').val("");
            $('#addModal').find('#orderNo').val("");
            $('#addModal').find('#soNo').val("");
            $('#addModal').find('#agent').val("").trigger('change');
            $('#addModal').find('#destinationCode').val("").trigger('change');
            $('#addModal').find('#product').val("").trigger('change');
            $('#addModal').find('#plant').val("").trigger('change');
            $('#addModal').find('#transporter').val("").trigger('change');
            $('#addModal').find('#vehicle').val("").trigger('change');
            $('#addModal').find('#exDel').val("E").trigger('change');
            $('#addModal').find('#orderQty').val("");
            $('#addModal').find('#remarks').val("");
            
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

        $('#uploadExcel').on('click', function(){
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

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';
            var siteI = $('#siteSearch').val() ? $('#siteSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            
            window.open("php/exportSoPo.php?type=Sales&fromDate="+fromDateI+"&toDate="+toDateI+
            "&status="+statusI+"&company="+companyI+"&site="+siteI+"&plant="+plantI+
            "&customer="+customerNoI+"&product="+productI);
        });

        $('#company').on('change', function(){
            $('#companyName').val($('#company :selected').data('name'));
        });

        $('#customer').on('change', function(){
            $('#customerName').val($('#customer :selected').data('name'));
        });

        $('#site').on('change', function(){
            $('#siteName').val($('#site :selected').data('name'));
        });

        $('#agent').on('change', function(){
            $('#agentName').val($('#agent :selected').data('name'));
        });
        
        $('#destinationCode').on('change', function(){
            $('#destinationName').val($('#destinationCode :selected').data('name'));
        });
        
        $('#product').on('change', function(){
            $('#productName').val($('#product :selected').data('name'));
        });
        
        $('#plant').on('change', function(){
            $('#plantName').val($('#plant :selected').data('name'));
        });

        $('#transporter').on('change', function(){
            $('#transporterName').val($('#transporter :selected').data('name'));
        });

    });

    function format (row) {
        var returnString = `
        <!-- Weighing Section -->
        <div class="row">
            <div class="col-6">
                <p><strong>COMPANY:</strong> ${row.company_code} - ${row.company_name}</p>
                <p><strong>CUSTOMER:</strong> ${row.customer_code} - ${row.customer_name}</p>
                <p><strong>SITE:</strong> ${row.site_code} - ${row.site_name}</p>
                <p><strong>AGENT:</strong> ${row.agent_code} - ${row.agent_name}</p>
                <p><strong>DESTINATION:</strong> ${row.destination_code} - ${row.destination_name}</p>
                <p><strong>PRODUCT:</strong> ${row.product_code} - ${row.product_name}</p>
                <p><strong>PLANT:</strong> ${row.plant_code} - ${row.plant_name}</p>
                <p><strong>REMARKS:</strong> ${row.remarks}</p>
            </div>
            <div class="col-6">
                <p><strong>ORDER DATE:</strong> ${row.order_date}</p>
                <p><strong>CUSTOMER P/O No:</strong> ${row.order_no}</p>
                <p><strong>S/O ORDER:</strong> ${row.so_no}</p>
                <p><strong>TRANSPORTER:</strong> ${row.transporter_code} - ${row.transporter_name}</p>
                <p><strong>VEHICLE NO:</strong> ${row.veh_number}</p>
                <p><strong>EX-QUARRY / DELIVERED:</strong> ${row.exquarry_or_delivered}</p>
                <p><strong>ORDER QUANTITY:</strong> ${row.order_quantity} KG</p>
                <p><strong>BALANCE:</strong> ${row.balance} KG</p>
            </div>
        </div>`;

        if (row.weights.length > 0) {
            returnString += `<div class="row">
                <table class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Delivery Order No</th>
                            <th>Vehicle No</th>
                            <th>Nett Weight</th>
                            <th>Weighted By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
                for (var i = 0; i < row.weights.length; i++) {
                    var weights = row.weights;

                    returnString += `
                        <tr>
                            <td>${weights[i].transaction_id}</td>
                            <td>${weights[i].product_code}</td>
                            <td>${weights[i].product_name}</td>
                            <td>${weights[i].delivery_no}</td>
                            <td>${weights[i].lorry_plate_no1}</td>
                            <td>${weights[i].nett_weight1} KG</td>
                            <td>${weights[i].created_by}</td>
                            <td>
                                <div class="col-auto">
                                    <button title="Print" type="button" id="print${weights[i].id}" onclick="print('${weights[i].id}')" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            returnString += `</tbody>
                        </table>
                    </div>`;
        }        

        return returnString;
    }

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getSalesOrder.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#company').val(obj.message.company_code).trigger('change');
                $('#addModal').find('#customer').val(obj.message.customer_code).trigger('change');
                $('#addModal').find('#site').val(obj.message.site_code).trigger('change');
                $('#addModal').find('#orderDate').val(formatDate2(new Date(obj.message.order_date)));
                $('#addModal').find('#orderNo').val(obj.message.order_no);
                $('#addModal').find('#soNo').val(obj.message.so_no);
                $('#addModal').find('#agent').val(obj.message.agent_code).trigger('change');
                $('#addModal').find('#destinationCode').val(obj.message.destination_code).trigger('change');
                $('#addModal').find('#product').val(obj.message.product_code).trigger('change');
                $('#addModal').find('#plant').val(obj.message.plant_code).trigger('change');
                $('#addModal').find('#transporter').val(obj.message.transporter_code).trigger('change');
                $('#addModal').find('#vehicle').val(obj.message.veh_number).trigger('change');
                $('#addModal').find('#exDel').val(obj.message.exquarry_or_delivered).trigger('change');
                $('#addModal').find('#orderQty').val(obj.message.order_quantity);
                $('#addModal').find('#remarks').val(obj.message.remarks);

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

    function complete(id){
        if (confirm('Are you sure you want to close this item?')) {
            $('#spinnerLoading').show();
            $.post('php/completeSalesOrder.php', {userID: id}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message);
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

    function deactivate(id){
        if (confirm('Are you sure you want to delete this item?')) {
            $('#spinnerLoading').show();
            $.post('php/deleteSalesOrder.php', {userID: id}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message);
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

        // Ensure we handle cases where there may be less than 22 columns
        while (headers.length < 22) {
            headers.push(''); // Adding empty headers to reach 22 columns
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

            // Ensure we handle cases where there may be less than 22 cells in a row
            while (rowData.length < 22) {
                rowData.push(''); // Adding empty cells to reach 22 columns
            }

            for (var j = 0; j < 22; j++) {
                var cellData = rowData[j];
                var formattedData = cellData;

                // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
                if (typeof cellData === 'number' && cellData > 0 && j == 0) {
                    var dateObj = XLSX.SSF.parse_date_code(cellData);
                    if (dateObj) {
                        // Format the date as DD/MM/YYYY
                        var day = String(dateObj.d).padStart(2, '0');
                        var month = String(dateObj.m).padStart(2, '0');
                        var year = dateObj.y;
                        formattedData = `${day}/${month}/${year}`;
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

    function print(id) {
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
    }

    </script>
</body>
</html>