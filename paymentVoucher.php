<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$plantId = $_SESSION['plant'];

$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$product = $db->query("SELECT * FROM Product WHERE status = '0'");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0'");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0'");
$unit = $db->query("SELECT * FROM Unit WHERE status = '0'");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0'");

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

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'AUTHORITY'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

// Get Company Detail
$stmt = $db->prepare("SELECT * from Company WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();

$includePrice = '';
$includeContainer = '';
if(($row = $result->fetch_assoc()) !== null){
    $includePrice = $row['include_price'];
    $includeContainer = $row['include_container'];
}
?>

<head>

    <title>Payment Voucher | PWS - Weighing System</title>
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
                                                            <label for="weighingTypeSearch" class="form-label">Weighing Type</label>
                                                            <select id="weighingTypeSearch" class="form-select">
                                                                <option selected>-</option>
                                                                <option value="Normal">Normal Weighing</option>
                                                                <?php if($includeContainer == 'Y'): ?>
                                                                <option value="Container">Primer Mover</option>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col--> 
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="transactionStatusSearch" class="form-label">Transaction Status</label>
                                                            <select id="transactionStatusSearch" class="form-select">
                                                                <option selected>-</option>
                                                                <option value="Sales" selected>Sales</option>
                                                                <option value="Purchase">Purchase</option>
                                                                <option value="Local">Public</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="customerSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer Name</label>
                                                            <select id="customerNoSearch" class="form-select" >
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
                                                            <select id="supplierSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowSF=mysqli_fetch_assoc($supplier2)){ ?>
                                                                    <option value="<?=$rowSF['supplier_code'] ?>"><?=$rowSF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invoiceSearch" class="form-label">Invoice No.</label>
                                                            <input type="text" class="form-control" id="invoiceSearch" name="invoiceSearch" placeholder="Invoice No.">                                                                                  
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
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

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header" >
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title text-white mb-0">Payment Vouchers</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!-- <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    <?=$languageArray['export_excel_code'][$language]?>
                                                                </button> -->
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['weighing_type_code'][$language]?></th>
                                                                    <th><?=$languageArray['transaction_status_code'][$language]?></th>
                                                                    <th><?=$languageArray['customer_supplier_code'][$language]?></th>
                                                                    <th><?=$languageArray['invoice_no_code'][$language]?></th>
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
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
    
    <div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Export Weighing Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportPdfForm" class="needs-validation" novalidate autocomplete="off">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="id" name="id"> 
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="reportType" class="col-sm-4 col-form-label">Report Type *</label>
                                                    <div class="col-sm-8">
                                                        <select id="reportType" name="reportType" class="form-select" required>
                                                            <!-- <option value="CUSTOMER">Customer Report</option> -->
                                                            <!--option value="SUMMARY">Summary Report - Date</option-->
                                                            <!--option value="PRODUCT">Product Report</option-->
                                                            <option value="S&P">Summary Report - By Product</option>
                                                            <option value="S&PC">Summary Report - By Customer</option>
                                                        </select>   
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" id="fromDate" name="fromDate">                                   
                                            <input type="hidden" class="form-control" id="toDate" name="toDate">                                   
                                            <input type="hidden" class="form-control" id="transactionStatus" name="transactionStatus">                                   
                                            <input type="hidden" class="form-control" id="customer" name="customer">     
                                            <input type="hidden" class="form-control" id="supplier" name="supplier"> 
                                            <input type="hidden" class="form-control" id="vehicle" name="vehicle">     
                                            <input type="hidden" class="form-control" id="weighingType" name="weighingType">     
                                            <input type="hidden" class="form-control" id="customerType" name="customerType">     
                                            <input type="hidden" class="form-control" id="product" name="product">  
                                            <input type="hidden" class="form-control" id="rawMat" name="rawMat">   
                                            <input type="hidden" class="form-control" id="destination" name="destination">     
                                            <input type="hidden" class="form-control" id="plant" name="plant">   
                                            <input type="hidden" class="form-control" id="status" name="status">                                     
                                            <input type="hidden" class="form-control" id="file" name="file">     
                                            <input type="hidden" class="form-control" id="isMulti" name="isMulti">     
                                            <input type="hidden" class="form-control" id="ids" name="ids">     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                <button type="submit" class="btn btn-success" id="submit"><?=$languageArray['submit_code'][$language]?></button>
                            </div>
                        </div><!--end col-->                                                               
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

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


        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var weighingTypeI = $('#weighingTypeSearch').val() ? $('#weighingTypeSearch').val() : '';
        var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
        var invoiceNoI = $('#invoiceSearch').val() ? $('#invoiceSearch').val() : '';

        var table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterPaymentVoucher.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    weighingType: weighingTypeI,
                    transactionStatus: transactionStatusI,
                    customer: customerNoI,
                    supplier: supplierNoI,
                    invoiceNo: invoiceNoI
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
                { data: 'weight_type' },
                { data: 'transaction_status' },
                { data: 'customer' },
                { data: 'invoice_no' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-success btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li></ul></div>';
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

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var weighingTypeI = $('#weighingTypeSearch').val() ? $('#weighingTypeSearch').val() : '';
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var invoiceNoI = $('#invoiceSearch').val() ? $('#invoiceSearch').val() : '';

            //Destroy the old Datatable
            $("#weightTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': false,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterPaymentVoucher.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        weighingType: weighingTypeI,
                        transactionStatus: transactionStatusI,
                        customer: customerNoI,
                        supplier: supplierNoI,
                        invoiceNo: invoiceNoI
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
                    { data: 'weight_type' },
                    { data: 'transaction_status' },
                    { data: 'customer' },
                    { data: 'invoice_no' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                            return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li></ul></div>';
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
        });

        $.validator.setDefaults({
            submitHandler: function () {
                if($('#exportPdfModal').hasClass('show')){   
                    var fromDateI = $('#fromDateSearch').val();
                    var toDateI = $('#toDateSearch').val();
                    var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
                    var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
                    var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
                    var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
                    var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
                    var customerTypeI = $('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '';
                    var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
                    var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
                    var destinationI = $('#destinationSearch').val() ? $('#destinationSearch').val() : '';
                    var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

                    $('#exportPdfForm').find('#fromDate').val(fromDateI);
                    $('#exportPdfForm').find('#toDate').val(toDateI);
                    $('#exportPdfForm').find('#transactionStatus').val(transactionStatusI);
                    $('#exportPdfForm').find('#customer').val(customerNoI);
                    $('#exportPdfForm').find('#supplier').val(supplierNoI);
                    $('#exportPdfForm').find('#vehicle').val(vehicleNoI);
                    $('#exportPdfForm').find('#customerType').val(customerTypeI);
                    $('#exportPdfForm').find('#product').val(productI);
                    $('#exportPdfForm').find('#rawMat').val(rawMatI);
                    $('#exportPdfForm').find('#destination').val(destinationI);
                    $('#exportPdfForm').find('#plant').val(plantI);
                    $('#exportPdfForm').find('#status').val(statusI);
                    $('#exportPdfForm').find('#file').val('weight');
                    $('#exportPdfModal').modal('hide');

                    $.post('php/exportPdf.php', $('#exportPdfForm').serialize(), function(response){
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
                else if($('#exportSoRepModal').hasClass('show')){   
                    var group1 = $('#exportSoRepModal').find('#group1').val();
                    var group2 = $('#exportSoRepModal').find('#group2').val();
                    var group3 = $('#exportSoRepModal').find('#group3').val();
                    var group4 = $('#exportSoRepModal').find('#group4').val();

                    // Added checking to ensure previous group is selected
                    if (group2 && !group1) {
                        alert("Please select Group 1 before selecting Group 2.");
                        return;
                    }
                    if (group3 && (!group1 || !group2)) {
                        alert("Please select Group 1 and Group 2 before selecting Group 3.");
                        return;
                    }

                    var fromDateI = $('#fromDateSearch').val();
                    var toDateI = $('#toDateSearch').val();
                    var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
                    var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
                    var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
                    var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
                    var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
                    var customerTypeI = $('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '';
                    var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
                    var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
                    var destinationI = $('#destinationSearch').val() ? $('#destinationSearch').val() : '';
                    var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
                    var batchDrumSearchI = $('#batchDrumSearch').val() ? $('#batchDrumSearch').val() : '';

                    var selectedIds = []; // An array to store the selected 'id' values
                    $("#weightTable tbody input[type='checkbox']").each(function () {
                        if (this.checked) {
                            selectedIds.push($(this).val());
                        }
                    });

                    if (selectedIds.length > 0) {
                        $('#exportSoRepForm').find('#id').val(selectedIds);
                        $('#exportSoRepForm').find('#isMulti').val('Y');
                    }else{
                        $('#exportSoRepForm').find('#isMulti').val('N');
                    }

                    $('#exportSoRepForm').find('#fromDate').val(fromDateI);
                    $('#exportSoRepForm').find('#toDate').val(toDateI);
                    $('#exportSoRepForm').find('#status').val(transactionStatusI);
                    $('#exportSoRepForm').find('#customer').val(customerNoI);
                    $('#exportSoRepForm').find('#supplier').val(supplierNoI);
                    $('#exportSoRepForm').find('#vehicle').val(vehicleNoI);
                    $('#exportSoRepForm').find('#customerType').val(customerTypeI);
                    $('#exportSoRepForm').find('#product').val(productI);
                    $('#exportSoRepForm').find('#rawMat').val(rawMatI);
                    $('#exportSoRepForm').find('#destination').val(destinationI);
                    $('#exportSoRepForm').find('#plant').val(plantI);
                    $('#exportSoRepForm').find('#batchDrum').val(batchDrumSearchI);
                    // $('#exportSoRepForm').find('#type').val('Sales');
                    $('#exportSoRepModal').modal('hide');

                    $.post('php/exportSoPoReport.php', $('#exportSoRepForm').serialize(), function(response){
                        var obj = JSON.parse(response);

                        if(obj.status === 'success'){
                            var previewWindow = window.open('', '_blank');
                            previewWindow.document.write(obj.message);
                            previewWindow.document.close();
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
            }
        });

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var weightTypeI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var customerTypeI = $('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var destinationI = $('#destinationSearch').val() ? $('#destinationSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+transactionStatusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+weightTypeI+"&product="+productI+"&rawMat="+rawMatI+
                "&destination="+destinationI+"&plant="+plantI+"&status="+statusI+"&isMulti=Y&ids="+selectedIds);
            } else {
                window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
                "&transactionStatus="+transactionStatusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&vehicle="+vehicleNoI+
                "&weighingType="+weightTypeI+"&product="+productI+"&rawMat="+rawMatI+
                "&destination="+destinationI+"&plant="+plantI+"&status="+statusI+"&isMulti=N");
            }
        });

        $('#transactionStatusSearch').on('change', function(){
            var status = $(this).val();

            if (status == 'Purchase'){
                $('#productSearchDisplay').hide();
                $('#rawMatSearchDisplay').show();
                $('#customerSearchDisplay').hide();
                $('#supplierSearchDisplay').show();
            }else{
                $('#productSearchDisplay').show();
                $('#rawMatSearchDisplay').hide();
                $('#customerSearchDisplay').show();
                $('#supplierSearchDisplay').hide();
            }
        });
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status);
                $('#addModal').find('#weightType').val(obj.message.weight_type);
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));
                $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1);

                if(obj.message.vehicleNoTxt != null)
                {
                    $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                }

                $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2);
                $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#customerName').val(obj.message.customer_name);
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name);
                $('#addModal').find('#productCode').val(obj.message.product_code);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#destination').val(obj.message.destination);
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                $('#addModal').find('#grossIncomingDate').val(formatDate2(new Date(obj.message.gross_weight1_date)));
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date != null ? formatDate2(new Date(obj.message.tare_weight1_date)) : '');
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                $('#addModal').find('#grossIncomingDate2').val(obj.message.gross_weight2_date != null ? formatDate2(new Date(obj.message.gross_weight2_date)) : '');
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                $('#addModal').find('#tareOutgoingDate2').val(obj.message.tare_weight2_date != null ? formatDate2(new Date(obj.message.tare_weight2_date)) : '');
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);
                // $('#addModal').find('#id').val(obj.message.is_complete);
                // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
                //$('#addModal').find('#manualWeight').val(obj.message.manual_weight);
                if(obj.message.manual_weight == 'true'){
                    $("#manualWeightYes").prop("checked", true);
                    $("#manualWeightNo").prop("checked", false);
                }
                else{
                    $("#manualWeightYes").prop("checked", false);
                    $("#manualWeightNo").prop("checked", true);
                }

                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').find('#subTotalPrice').val(obj.message.product_description);
                $('#addModal').find('#sstPrice').val(obj.message.product_description);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);
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

    function print(id) {
        $.post('php/print.php', {userID: id, file: 'weight', isEmptyContainer: 'N'}, function(data){
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
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
            }
        });
    }
    </script>
</body>
</html>
