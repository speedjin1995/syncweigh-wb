<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$plantId = $_SESSION['plant'];

$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");

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
                                                                <!-- <option value="Sales">Sales</option> -->
                                                                <option value="Purchase" selected>Purchase</option>
                                                                <!-- <option value="Local">Public</option> -->
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="customerSearchDisplay" style="display:none">
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
                                                    <div class="col-3" id="supplierSearchDisplay">
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
                                <div class="col-xl-3 col-md-6">
                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="pricingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Payment Voucher Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="pricingForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Voucher No</label>
                                                                        <input type="text" class="form-control input-readonly" id="voucherNo" name="voucherNo" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Voucher Date</label>
                                                                        <input type="text" class="form-control" id="voucherDate" name="voucherDate" required>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Unit Price (RM)</label>
                                                                        <input type="number" class="form-control" id="unitPrice" name="unitPrice" step="0.01" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Tax (%)</label>
                                                                        <input type="number" class="form-control" id="tax" name="tax" step="0.01" min="0" max="100" value="0" required>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Total Nett Weight (MT)</label>
                                                                        <input type="number" class="form-control" id="totalNettWeight" name="totalNettWeight" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label">Total Amount (RM)</label>
                                                                        <input type="number" class="form-control" id="totalAmount" name="totalAmount" readonly>
                                                                    </div>
                                                                </div>
                                                                <h6 class="mb-2">Transaction Details</h6>
                                                                <table class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Transaction <br>ID</th>
                                                                            <th>Supplier <br>Code</th>
                                                                            <th>Supplier <br>Name</th>
                                                                            <th>Weight <br>Type</th>
                                                                            <th>Invoice <br>No</th>
                                                                            <th>Gross <br>Incoming (MT)</th>
                                                                            <th>Incoming Date</th>
                                                                            <th>Tare <br>Outgoing (MT)</th>
                                                                            <th>Outgoing Date</th>
                                                                            <th>Nett <br>Weight (MT)</th>
                                                                            <th>Unit <br>Price (RM)</th>
                                                                            <th>Nett <br>Amount (RM)</th>
                                                                            <th>SST <br>(RM)</th>
                                                                            <th>Total <br>Price (RM)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="paymentDetailsTable">
                                                                    </tbody>
                                                                </table>

                                                                <div class="row mt-4">
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2">Deductions (-)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="50">BIL</th>
                                                                                    <th>Description</th>
                                                                                    <th width="150">Amount (RM)</th>
                                                                                    <th width="50"><button type="button" class="btn btn-sm btn-success" id="addDeductionRow"><i class="bx bx-plus"></i></button></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="deductionsTable">
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="2" class="text-end">Total:</th>
                                                                                    <th><input type="number" class="form-control form-control-sm" id="totalDeductions" name="totalDeductions" readonly></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2">Additions (+)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="50">BIL</th>
                                                                                    <th>Description</th>
                                                                                    <th width="150">Amount (RM)</th>
                                                                                    <th width="50"><button type="button" class="btn btn-sm btn-success" id="addAdditionRow"><i class="bx bx-plus"></i></button></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="additionsTable">
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="2" class="text-end">Total:</th>
                                                                                    <th><input type="number" class="form-control form-control-sm" id="totalAdditions" name="totalAdditions" readonly></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-3 mb-3">
                                                                    <div class="col-xxl-12 col-lg-12">
                                                                        <div class="alert alert-info mb-0">
                                                                            <div class="row">
                                                                                <div class="col-xxl-4 col-lg-4"><strong>Subtotal:</strong> RM <input type="text" class="form-control-plaintext d-inline-block w-auto" id="subtotal" name="subtotal" value="0.00" readonly></div>
                                                                                <div class="col-xxl-4 col-lg-4"><strong>Deductions:</strong> RM <input type="text" class="form-control-plaintext d-inline-block w-auto text-danger" id="displayDeductions" name="deductions" value="0.00" readonly></div>
                                                                                <div class="col-xxl-4 col-lg-4"><strong>Additions:</strong> RM <input type="text" class="form-control-plaintext d-inline-block w-auto text-success" id="displayAdditions" name="additions" value="0.00" readonly></div>
                                                                            </div>
                                                                            <hr class="my-2">
                                                                            <div class="text-center"><h5 class="mb-0"><strong>Final Amount:</strong> RM <input type="text" class="form-control-plaintext d-inline-block w-auto" id="finalAmount" name="finalAmount" value="0.00" readonly></h5></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" id="customerSupplier" name="customerSupplier">
                                                                <input type="hidden" id="invoiceNo" name="invoiceNo">
                                                                <input type="hidden" id="harvestingPrice" name="harvestingPrice">
                                                                <input type="hidden" id="transportPrice" name="transportPrice">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-xxl-12 col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                                <button type="submit" class="btn btn-success"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="printModalTitle">Select Slip to Print</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form id="printSlipForm">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="printSlipType" class="form-label">Slip Type *</label>
                                                            <select id="printSlipType" name="printSlipType" class="form-select" required>
                                                                <option value="pv">Payment Voucher</option>
                                                                <option value="ffbStatement">FFB Statement</option>
                                                            </select>
                                                        </div>

                                                        <input type="hidden" id="customerSupplierPrint" name="customerSupplierPrint">
                                                        <input type="hidden" id="transactionDatePrint" name="transactionDatePrint">
                                                        <input type="hidden" id="weighingTypePrint" name="weighingTypePrint">
                                                        <input type="hidden" id="transactionStatusPrint" name="transactionStatusPrint">
                                                    </div> 

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success">Print</button>
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
                                                                    <th><?=$languageArray['voucher_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['voucher_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['weighing_type_code'][$language]?></th>
                                                                    <th><?=$languageArray['transaction_status_code'][$language]?></th>
                                                                    <th><?=$languageArray['supplier_code'][$language]?></th>
                                                                    <th><?=$languageArray['invoice_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['outstanding_amount_code'][$language]?> (RM)</th>
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
    
    var deductionRowCount = 0;
    var additionRowCount = 0;
    const today = new Date();
    const tomorrow = new Date(today);
    const yesterday = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    yesterday.setDate(yesterday.getDate() - 1);
    $(function () {
        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#voucherDate').flatpickr({
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
                { data: 'transaction_date' },
                { data: 'voucher_no' },
                { data: 'weight_type' },
                { data: 'transaction_status' },
                { data: 'customer' },
                { data: 'invoice_no' },
                { data: 'outstanding_amount' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        return '<div class="dropdown d-inline-block">' +
                            '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                '<i class="ri-more-fill align-middle"></i>' +
                            '</button>' +
                            '<ul class="dropdown-menu dropdown-menu-end">' +
                                '<li>' +
                                    '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print(\'' + row.customer + '\', \'' + row.transaction_date + '\', \'' + row.transaction_status + '\', \'' + row.weight_type + '\')">' +
                                        '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                    '</a>' +
                                '</li>' +
                                '<li>' +
                                    '<a class="dropdown-item apply-unit-price-btn" id="updatePricing'+data+'" onclick="updatePricing(\'' + row.customer + '\', \'' + row.transaction_date + '\', \'' + row.invoice_no + '\')">' +
                                        '<i class="ri-calculator-fill align-bottom me-2 text-muted"></i> Update Pricing' +
                                    '</a>' +
                                '</li>' +
                            '</ul>' +
                        '</div>';
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
                    { data: 'transaction_date' },
                    { data: 'voucher_no' },
                    { data: 'weight_type' },
                    { data: 'transaction_status' },
                    { data: 'customer' },
                    { data: 'invoice_no' },
                    { data: 'outstanding_amount' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            return '<div class="dropdown d-inline-block">' +
                                '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                    '<i class="ri-more-fill align-middle"></i>' +
                                '</button>' +
                                '<ul class="dropdown-menu dropdown-menu-end">' +
                                    '<li>' +
                                        '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print(\'' + row.customer + '\', \'' + row.transaction_date + '\', \'' + row.transaction_status + '\', \'' + row.weight_type + '\')">' +
                                            '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                        '</a>' +
                                    '</li>' +
                                    '<li>' +
                                        '<a class="dropdown-item apply-unit-price-btn" onclick="updatePricing(\'' + row.customer + '\', \'' + row.transaction_date + '\', \'' + row.invoice_no + '\')">' +
                                            '<i class="ri-calculator-fill align-bottom me-2 text-muted"></i> Update Pricing' +
                                        '</a>' +
                                    '</li>' +
                                '</ul>' +
                            '</div>';
                        }
                    }
                ]   
            });
        });

        $.validator.setDefaults({
            submitHandler: function () {
                if($('#printModal').hasClass('show')){
                    var customerSupplierI = $('#printModal').find('#customerSupplierPrint').val();
                    var transactionDateI = $('#printModal').find('#transactionDatePrint').val();
                    var transactionStatusI = $('#printModal').find('#transactionStatusPrint').val();
                    var weightTypeI = $('#printModal').find('#weighingTypePrint').val();
                    var printSlipTypeI = $('#printModal').find('#printSlipType').val();

                    $.post('php/printPaymentVoucherSlip.php', {slipType: printSlipTypeI, customerSupplier: customerSupplierI, transactionDate: transactionDateI, transactionStatus: transactionStatusI, weightType: weightTypeI}, function(data){
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
                            alert(obj.message);
                        }
                        else{
                            alert("Something wrong when printing");
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
                $('#customerSearchDisplay').hide();
                $('#supplierSearchDisplay').show();
            }else{
                $('#customerSearchDisplay').show();
                $('#supplierSearchDisplay').hide();
            }
        });

        $('#unitPrice, #tax').on('input', function() {
            var unitPrice = parseFloat($('#unitPrice').val()) || 0;
            var taxRate = parseFloat($('#tax').val()) || 0;
            var totalAmount = 0;
            
            $('#paymentDetailsTable tr').each(function() {
                var nettWeight = parseFloat($(this).find('td:eq(9)').text()) || 0;
                var subTotal = (unitPrice * nettWeight);
                var taxAmount = (subTotal * (taxRate/100));
                var rowTotal = (subTotal + taxAmount);
                
                $(this).find('input[name="unit_price[]"]').val(unitPrice.toFixed(2));
                $(this).find('input[name="sub_total[]"]').val(subTotal.toFixed(2));
                $(this).find('input[name="sst[]"]').val(taxAmount.toFixed(2));
                $(this).find('input[name="total_price[]"]').val(rowTotal.toFixed(2));
                
                totalAmount += rowTotal;
            });
            
            $('#totalAmount').val(totalAmount.toFixed(2));
            calculateTotals();
        });

        $('#totalNettWeight').on('change', function() {
            $('#deductionsTable tr').each(function() {
                $(this).find('.deduction-amount').trigger('input');
            });

            $('#additionTable tr').each(function() {
                $(this).find('.addition-amount').trigger('input');
            });
        });

        $('#pricingForm').on('submit', function(e) {
            e.preventDefault();
            $('#spinnerLoading').show();

            var formData = new FormData(this);
            
            $.ajax({
                url: 'php/updatePaymentPricing.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var obj = JSON.parse(response);
                    if(obj.status === 'success'){
                        $('#weightTable').DataTable().ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#pricingModal').modal('hide');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                    else{
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                }
            });
        });

        $('#addDeductionRow').on('click', function(event, desc = '', amount = 0) {
            var newRow = `<tr>
                <td>${++deductionRowCount}</td>
                <td><input type="text" class="form-control form-control-sm" name="deduction_desc[]" value="${desc}"></td>
                <td><input type="number" class="form-control form-control-sm deduction-amount" name="deduction_amount[]" step="0.01" value="${amount}"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeDeductionRow"><i class="bx bx-trash"></i></button></td>
            </tr>`;
            $('#deductionsTable').append(newRow);
        });

        $(document).on('click', '.removeDeductionRow', function() {
            $(this).closest('tr').remove();
            $('#deductionsTable tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            deductionRowCount = $('#deductionsTable tr').length;
            calculateTotals();
        });

        $('#addAdditionRow').on('click', function() {
            var newRow = `<tr>
                <td>${++additionRowCount}</td>
                <td><input type="text" class="form-control form-control-sm" name="addition_desc[]"></td>
                <td><input type="number" class="form-control form-control-sm addition-amount" name="addition_amount[]" step="0.01" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeAdditionRow"><i class="bx bx-trash"></i></button></td>
            </tr>`;
            $('#additionsTable').append(newRow);
        });

        $(document).on('click', '.removeAdditionRow', function() {
            $(this).closest('tr').remove();
            $('#additionsTable tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            additionRowCount = $('#additionsTable tr').length;
            calculateTotals();
        });

        $(document).on('input', '.deduction-amount, .addition-amount', function() {
            var $row = $(this).closest('tr');
            var deductionDesc = $row.find('input[name="deduction_desc[]"]').val();
            var harvestingPrice = parseFloat($('#harvestingPrice').val()) || 0;
            var transportPrice = parseFloat($('#transportPrice').val()) || 0;
            var totalNettWeight = parseFloat($('#totalNettWeight').val()) || 0;

            if (deductionDesc && deductionDesc.toLowerCase() == 'harvesting') {
                // Calculate harvesting if they exist
                var totalHarvestingPrice = harvestingPrice * totalNettWeight;
                $row.find('input[name="deduction_amount[]"]').val(totalHarvestingPrice.toFixed(2));
            }

            if (deductionDesc && deductionDesc.toLowerCase() == 'transport') {
                // Calculate transport if they exist
                var totalTransportPrice = transportPrice * totalNettWeight;
                $row.find('input[name="deduction_amount[]"]').val(totalTransportPrice.toFixed(2));
            }

            calculateTotals();
        });

        $(document).on('input', '.deduction-amount, .addition-amount', calculateTotals);
        
        $('#unitPrice, #tax').on('input', function() {
            setTimeout(calculateTotals, 100);
        });
        
        $('#pricingModal').on('shown.bs.modal', calculateTotals);
    });

    function updatePricing(customerSupplier, transactionDate, invoiceNo) {
        var transactionStatus = $('#transactionStatusSearch').val();
        var weightType = $('#weighingTypeSearch').val();

        $('#pricingModal').find('#customerSupplier').val(customerSupplier);
        $('#pricingModal').find('#voucherDate').val(transactionDate);
        $('#pricingModal').find('#invoiceNo').val(invoiceNo == 'null' ? '' : invoiceNo);

        $.post('php/getPaymentWeight.php', {
            transactionStatus: transactionStatus,
            weightType: weightType,
            customerSupplier: customerSupplier,
            transactionDate: transactionDate,
            invoiceNo: invoiceNo
        }, function(response){
            var obj = JSON.parse(response);

            if (obj.status == 'success'){
                loadPricingModal(obj.message);
                
                // Populate Harvesting & Transport Price in the modal - only if they don't exist
                if (obj.message.cust_supp_detail && obj.message.cust_supp_detail.harvesting_price) {
                    $('#pricingModal').find('#harvestingPrice').val(obj.message.cust_supp_detail.harvesting_price);
                    
                    var harvestingExists = false;
                    $('#deductionsTable input[name="deduction_desc[]"]').each(function() {
                        if ($(this).val().toLowerCase() == 'harvesting') {
                            harvestingExists = true;
                            return false;
                        }
                    });
                    
                    if (!harvestingExists) {
                        $('#addDeductionRow').trigger('click', ['Harvesting', 0]);
                        $('#totalNettWeight').trigger('change');
                    }
                }
                
                if (obj.message.cust_supp_detail && obj.message.cust_supp_detail.transport_price) {
                    $('#pricingModal').find('#transportPrice').val(obj.message.cust_supp_detail.transport_price);
                    
                    var transportExists = false;
                    $('#deductionsTable input[name="deduction_desc[]"]').each(function() {
                        if ($(this).val().toLowerCase() == 'transport') {
                            transportExists = true;
                            return false;
                        }
                    });
                    
                    if (!transportExists) {
                        $('#addDeductionRow').trigger('click', ['Transport', 0]);
                        $('#totalNettWeight').trigger('change');
                    }
                }

                $('#pricingModal').modal('show');
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something went wrong", "Failed:");
            }
        });
    }

    function calculateTotals() {
        var totalDeductions = 0
        var totalAdditions = 0;
        $('.deduction-amount').each(function() { totalDeductions += parseFloat($(this).val()) || 0; });
        $('.addition-amount').each(function() { totalAdditions += parseFloat($(this).val()) || 0; });
        
        var subtotal = parseFloat($('#totalAmount').val()) || 0;
        var finalAmount = subtotal - totalDeductions + totalAdditions;

        $('#totalDeductions').val(totalDeductions.toFixed(2));
        $('#totalAdditions').val(totalAdditions.toFixed(2));
        $('#subtotal').val(subtotal.toFixed(2));
        $('#displayDeductions').val('RM ' + totalDeductions.toFixed(2));
        $('#displayAdditions').val('RM ' + totalAdditions.toFixed(2));
        $('#finalAmount').val(finalAmount.toFixed(2));
    }

    function loadPricingModal(data) {
        var tableBody = $('#paymentDetailsTable');
        tableBody.empty();
        
        data.weights.forEach(function(weight, index) {
            var row = '<tr>' +
                '<td>' + weight.transaction_id + '</td>' +
                '<td>' + weight.cust_supp_code + '</td>' +
                '<td>' + weight.cust_supp_name + '</td>' +
                '<td>' + weight.weight_type + '</td>' +
                '<td>' + (weight.invoice_no ?? '') + '</td>' +
                '<td>' + (parseFloat(weight.gross_weight1)/1000).toFixed(2) + '</td>' +
                '<td>' + weight.gross_weight1_date + '</td>' +
                '<td>' + (parseFloat(weight.tare_weight1)/1000).toFixed(2) + '</td>' +
                '<td>' + weight.tare_weight1_date + '</td>' +
                '<td>' + (parseFloat(weight.nett_weight1)/1000).toFixed(2) + '</td>' +
                '<td><input type="text" name="unit_price[]" value="' + (parseFloat(weight.unit_price).toFixed(2)) + '" readonly class="form-control-plaintext"></td>' +
                '<td><input type="text" name="sub_total[]" value="' + (parseFloat(weight.sub_total).toFixed(2)) + '" readonly class="form-control-plaintext"></td>' +
                '<td><input type="text" name="sst[]" value="' + (parseFloat(weight.sst).toFixed(2)) + '" readonly class="form-control-plaintext"></td>' +
                '<td><input type="text" name="total_price[]" value="' + (parseFloat(weight.total_price).toFixed(2)) + '" readonly class="form-control-plaintext"></td>' +
                '<td style="display:none;"><input type="hidden" name="id[]" value="' + weight.id + '"></td>' +
                '</tr>';
            tableBody.append(row);
        });
        
        // Load existing payment data if available
        if (data.paymentVoucher && Object.keys(data.paymentVoucher).length > 0) {
            $('#voucherNo').val(data.paymentVoucher.voucher_no);
            $('#voucherDate').val(formatDate2(new Date(data.paymentVoucher.voucher_date)));
            $('#unitPrice').val(data.paymentVoucher.unit_price);
            $('#tax').val(data.paymentVoucher.tax);
            $('#totalAmount').val(data.paymentVoucher.total_amount.replace('RM ', ''));
            
            // Load deductions
            $('#deductionsTable').empty();
            deductionRowCount = 0;
            var deductions = JSON.parse(data.paymentVoucher.deduction_details);
            deductions.forEach(function(deduction, index) {
                var row = '<tr><td>' + (++deductionRowCount) + '</td><td><input type="text" class="form-control form-control-sm" name="deduction_desc[]" value="' + deduction.deduction_desc + '"></td><td><input type="number" class="form-control form-control-sm deduction-amount" name="deduction_amount[]" step="0.01" value="' + deduction.deduction_amount + '"></td><td><button type="button" class="btn btn-sm btn-danger removeDeductionRow"><i class="bx bx-trash"></i></button></td></tr>';
                $('#deductionsTable').append(row);
            });
            
            // Load additions
            $('#additionsTable').empty();
            additionRowCount = 0;
            var additions = JSON.parse(data.paymentVoucher.addition_details);
            additions.forEach(function(addition, index) {
                var row = '<tr><td>' + (++additionRowCount) + '</td><td><input type="text" class="form-control form-control-sm" name="addition_desc[]" value="' + addition.addition_desc + '"></td><td><input type="number" class="form-control form-control-sm addition-amount" name="addition_amount[]" step="0.01" value="' + addition.addition_amount + '"></td><td><button type="button" class="btn btn-sm btn-danger removeAdditionRow"><i class="bx bx-trash"></i></button></td></tr>';
                $('#additionsTable').append(row);
            });
        } else {
            $('#voucherNo').val('');
            $('#unitPrice').val(0.00);
            $('#tax').val(0);
            $('#totalAmount').val(0.00);
        }
        
        $('#totalNettWeight').val((data.totalNettWeight).toFixed(2));
    }

    function print(customerSupplier, transactionDate, transactionStatus, weightType) {
        $('#printModal').find('#customerSupplierPrint').val(customerSupplier);
        $('#printModal').find('#transactionDatePrint').val(transactionDate);
        $('#printModal').find('#weighingTypePrint').val(weightType);
        $('#printModal').find('#transactionStatusPrint').val(transactionStatus);
        $('#printModal').modal('show');

        $('#printSlipForm').validate({
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
    </script>
</body>
</html>
