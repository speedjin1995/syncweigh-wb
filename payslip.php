<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$plantId = $_SESSION['plant'];

$user = $db->query("SELECT * FROM Users WHERE status = '0' ORDER BY name ASC");
$cashbook = $db->query("SELECT * FROM Cash_Book WHERE deleted = '0' ORDER BY cash_book_no ASC");

// Get Company Detail
$stmt = $db->prepare("SELECT * from Company WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$epf = 0;
if(($row = $result->fetch_assoc()) !== null){
    $epf = $row['epf'];
}

// Get Payslip Setting
$socso = [];
$eis = [];
$tax = [];
$nonResidentTaxRate = null;
$individualReliefFund = null;
$individualEpfReliefFund = null;
$stmt2 = $db->prepare("SELECT name, value FROM miscellaneous WHERE name IN ('socso', 'eis', 'tax', 'non_res_epf', 'ind_relief', 'ind_epf_relief')");
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    if($row['name'] == 'socso') {
        $socso = !empty($row['value']) ? json_decode($row['value'], true) : [];
    } else if($row['name'] == 'eis') {
        $eis = !empty($row['value']) ? json_decode($row['value'], true) : [];
    } else if($row['name'] == 'tax') {
        $tax = !empty($row['value']) ? json_decode($row['value'], true) : [];
    } else if($row['name'] == 'non_res_epf') {
        $nonResidentTaxRate = $row['value'];
    } else if($row['name'] == 'ind_relief') {
        $individualReliefFund = $row['value'];
    } else if($row['name'] == 'ind_epf_relief') {
        $individualEpfReliefFund = $row['value'];
    }
}
$stmt2->close();

?>

<head>

    <title><?=$languageArray['payslip_code'][$language]?> | PWS - Weighing System</title>
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
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle"><?=$languageArray['add_new_code'][$language]?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="addForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['pay_slip_no_code'][$language]?></label>
                                                                        <input type="text" class="form-control input-readonly" id="paySlipNo" name="paySlipNo" placeholder="<?=$languageArray['pay_slip_no_code'][$language]?>" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['date_code'][$language]?> *</label>
                                                                        <input type="text" class="form-control" id="date" name="date" required>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['employee_code'][$language]?> *</label>
                                                                        <select id="employee" name="employee" class="form-select select2" required>
                                                                            <option selected>-</option>
                                                                            <?php while($rowUser=mysqli_fetch_assoc($user)){ ?>
                                                                                <option value="<?=$rowUser['id'] ?>"><?=$rowUser['employee_code'] . ' - ' . $rowUser['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['payment_type_code'][$language]?> *</label>
                                                                        <select id="paymentType" name="paymentType" class="form-select select2" required>
                                                                            <option value="CASH">Cash</option>
                                                                            <option value="BANK">Bank Transfer</option>
                                                                            <option value="CHEQUE">Cheque</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4" id="chequeNoDiv" style="display:none">
                                                                        <label class="form-label"><?=$languageArray['cheque_no_code'][$language]?></label>
                                                                        <input type="text" class="form-control" id="chequeNo" name="chequeNo">
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['cash_book_code'][$language]?></label>
                                                                        <select id="cashBook" name="cashBook" class="form-select select2" >
                                                                            <option selected>-</option>
                                                                            <?php while($rowCashBook=mysqli_fetch_assoc($cashbook)){ ?>
                                                                                <option value="<?=$rowCashBook['id'] ?>"><?=$rowCashBook['cash_book_no'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="row mt-4">
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2"><?=$languageArray['earnings_code'][$language]?> (+)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%"><?=$languageArray['number_short_code'][$language]?></th>
                                                                                    <th width="30%"><?=$languageArray['type_code'][$language]?></th>
                                                                                    <th width="45%"><?=$languageArray['description_code'][$language]?></th>
                                                                                    <th width="15%"><?=$languageArray['amount_code'][$language]?></th>
                                                                                    <th width="10%"><button type="button" class="btn btn-sm btn-success" id="addEarningsRow"><i class="bx bx-plus"></i></button></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="earningsTable">
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="3" class="text-end"><?=$languageArray['total_code'][$language]?>:</th>
                                                                                    <th><input type="number" class="form-control form-control-sm" id="grossPay" name="grossPay" readonly></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-xxl-6 col-lg-6">
                                                                        <h6 class="mb-2"><?=$languageArray['deductions_code'][$language]?> (-)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%"><?=$languageArray['number_short_code'][$language]?></th>
                                                                                    <th width="35%"><?=$languageArray['type_code'][$language]?></th>
                                                                                    <th width="30%"><?=$languageArray['description_code'][$language]?></th>
                                                                                    <th width="25%"><?=$languageArray['amount_code'][$language]?></th>
                                                                                    <th width="10%"><button type="button" class="btn btn-sm btn-success" id="addDeductionRow"><i class="bx bx-plus"></i></button></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="deductionTable">
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="3" class="text-end"><?=$languageArray['total_code'][$language]?>:</th>
                                                                                    <th><input type="number" class="form-control form-control-sm" id="totalDeduction" name="totalDeduction" readonly></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <div class="row mt-4">
                                                                    <div class="col-12">
                                                                        <div class="card bg-light border-0">
                                                                            <div class="card-body p-3">
                                                                                <div class="row align-items-center">
                                                                                    <div class="col-md-6">
                                                                                        <h5 class="mb-0 text-primary"><?=$languageArray['net_pay_code'][$language]?></h5>
                                                                                        <small class="text-muted"><?=$languageArray['gross_pay_code'][$language]?> - <?=$languageArray['deductions_code'][$language]?></small>
                                                                                    </div>
                                                                                    <div class="col-md-6 text-end">
                                                                                        <h3 class="mb-0 text-success fw-bold">RM <span id="netPayDisplay">0.00</span></h3>
                                                                                        <input type="hidden" id="netPay" name="netPay" value="0">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" id="id" name="id">
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

                                    <div class="modal fade" id="cancelModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="cancelForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title"><?=$languageArray['delete_reason_code'][$language]?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group">
                                                                <label><?=$languageArray['delete_reason_code'][$language]?> *</label>
                                                                <textarea class="form-control" id="deleteReason" name="deleteReason" rows="3"></textarea>
                                                            </div>
                                                            <input type="hidden" class="form-control" id="id" name="id">                                   
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
                                                    <div class="card-header" >
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title text-white mb-0"><?=$languageArray['payslip_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!-- <button type="button" id="exportPdf" class="btn btn-info waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export Report
                                                                </button> -->
                                                                <button type="button" id="addPayslip" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
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
                                                                    <th><?=$languageArray['date_code'][$language]?></th>
                                                                    <th><?=$languageArray['pay_slip_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['employee_code'][$language]?></th>
                                                                    <th><?=$languageArray['gross_pay_code'][$language]?> (RM)</th>
                                                                    <th><?=$languageArray['deductions_code'][$language]?> (RM)</th>
                                                                    <th><?=$languageArray['net_pay_code'][$language]?> (RM)</th>
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

    <script type="text/html" id="earningsDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="earningsNo" name="earningsNo" readonly>
            </td>
            <td>
                <select class="form-select" id="earningsType" name="earningsType" required>
                    <option value="BASIC">Basic Salary</option>
                    <option value="ALLOWANCE">Allowance</option>
                    <option value="OVERTIME">Overtime</option>
                    <option value="BONUS">Bonus</option>
                    <option value="COMMISSION">Commission</option>
                    <option value="CLAIMS">Claims</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="earningsDesc" name="earningsDesc">
            </td>
            <td>
                <input type="number" class="form-control" id="earningsAmt" name="earningsAmt" value="0" required>
            </td>
            <td>
                <button class="btn btn-sm btn-danger" id="remove">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="deductionDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="deductionNo" name="deductionNo" readonly>
            </td>
            <td>
                <select class="form-select" id="deductionType" name="deductionType" required>
                    <option value="EMP_EPF">Employee EPF</option>
                    <option value="EMP_SOCSO">Employee SOCSO</option>
                    <option value="TAX">Tax</option>
                    <option value="EMP_EIS">Employee EIS</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="deductionDesc" name="deductionDesc" style="background-color:white;">
            </td>
            <td>
                <input type="number" class="form-control" id="deductionAmt" name="deductionAmt" style="background-color:white;" value="0" required>
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-sm btn-danger" id="remove" style="background-color: #f06548;">
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
    <script src="assets/js/calculate_pcb.js"></script>

    <script type="text/javascript">
    
    var category = 1;
    var isResident = 'Y';
    var earningTypes = ['BASIC', 'ALLOWANCE', 'OVERTIME', 'BONUS', 'COMMISSION', 'CLAIMS'];
    var deductionTypes = ['EMP_EPF', 'EMP_SOCSO', 'TAX', 'EMP_EIS'];
    var epf = <?=$epf?>;
    var nonResidentTaxRate = <?=$nonResidentTaxRate?>;
    var individualReliefFund = <?=$individualReliefFund?>;
    var individualEpfReliefFund = <?=$individualEpfReliefFund?>;
    var socso = <?=json_encode($socso)?>;
    var eis = <?=json_encode($eis)?>;
    var tax = <?=json_encode($tax)?>;
    var earningsRowCount = 0;
    var deductionRowCount = 0;
    const today = new Date();
    const tomorrow = new Date(today);
    const yesterday = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    yesterday.setDate(yesterday.getDate() - 1);
    $(function () {
        // Initialize all Select2 elements in the search bar
        $('#collapseSearch .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
        });

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
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

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#date').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();

        var table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterPaySlip.php',
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
                { data: 'date' },
                { data: 'payslip_no' },
                { data: 'user_id' },
                { data: 'gross_pay' },
                { data: 'total_deductions' },
                { data: 'net_pay' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        return '<div class="dropdown d-inline-block">' +
                            '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                '<i class="ri-more-fill align-middle"></i>' +
                            '</button>' +
                            '<ul class="dropdown-menu dropdown-menu-end">' +
                                '<li>' +
                                    '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')">' +
                                        '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                    '</a>' +
                                '</li>' +
                                '<li>' +
                                    '<a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')">' +
                                        '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit' +
                                    '</a>' +
                                '</li>' +
                                '<li>' +
                                    '<a class="dropdown-item remove-item-btn" id="delete'+data+'" onclick="deactivate('+data+')">' +
                                        '<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete' +
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
                    'url':'php/filterPaySlip.php',
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
                    { data: 'date' },
                    { data: 'payslip_no' },
                    { data: 'user_id' },
                    { data: 'gross_pay' },
                    { data: 'total_deductions' },
                    { data: 'net_pay' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            return '<div class="dropdown d-inline-block">' +
                                '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                    '<i class="ri-more-fill align-middle"></i>' +
                                '</button>' +
                                '<ul class="dropdown-menu dropdown-menu-end">' +
                                    '<li>' +
                                        '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')">' +
                                            '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                        '</a>' +
                                    '</li>' +
                                    '<li>' +
                                        '<a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')">' +
                                            '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit' +
                                        '</a>' +
                                    '</li>' +
                                    '<li>' +
                                        '<a class="dropdown-item remove-item-btn" id="delete'+data+'" onclick="deactivate('+data+')">' +
                                            '<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete' +
                                        '</a>' +
                                    '</li>' +
                                '</ul>' +
                            '</div>';
                        }
                    }
                ]   
            });
        });

        $('#addPayslip').on('click', function(){
            // Show Capture Buttons When Add New
            $('#addModal').find('#id').val("");
            $('#addModal').find('#date')[0]._flatpickr.setDate(formatDate2(today), true);
            $('#addModal').find('#date')[0]._flatpickr.set('clickOpens', true);
            $('#addModal').find('#paymentType').val("BANK").trigger('change');
            $('#addModal').find('#cashBook').val("").trigger('change');
            $('#addModal').find('#employee').val("").trigger('change');
            $('#addModal').find('#grossPay').val("0");
            $('#addModal').find('#totalDeduction').val("0");
            $('#addModal').find('#netPay').val("0");
            $('#addModal').find('#netPayDisplay').text("0.00");

            earningsRowCount = 0;
            deductionRowCount = 0;
            $('#addModal').find('#earningsTable').html('');
            $('#addModal').find('#deductionTable').html('');

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

        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            $('#spinnerLoading').show();

            var formData = new FormData(this);
            
            $.ajax({
                url: 'php/payslip.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var obj = JSON.parse(response);
                    if(obj.status === 'success'){
                        $('#weightTable').DataTable().ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
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

        $('#submitCancel').on('click', function(){
            if($('#cancelForm').valid()){
                $('#spinnerLoading').show();
                var id = $('#cancelModal').find('#id').val();
                $.post('php/deleteCashBook.php', $('#cancelForm').serialize(), function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        $('#weightTable').DataTable().ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#cancelModal').modal('hide');
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                    else{
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                });
            }
        });

        $('#exportPdf').on('click', function(){
            var fromDate = $('#fromDateSearch').val();
            var toDate = $('#toDateSearch').val();

            $.post('php/exportAccountingPdf.php', { file: 'weight', fromDate: fromDate, toDate: toDate, reportType: 'DR' }, function(response){
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
        });

        // Payment Type Change
        $('#paymentType').on('change', function (){
            var paymentType = $(this).val();

            if (paymentType == 'CHEQUE') {
                $('#chequeNoDiv').show();
                $('#chequeNo').attr('required', true);
            } else {
                $('#chequeNoDiv').hide();
                $('#chequeNo').attr('required', false);
            }
        });

        // Remove deduction row
        $("#deductionTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#deductionTable tr").each(function (index) {
                $(this).find('input[name^="deductionNo"]').val(index + 1);
            });

            deductionRowCount--;
            updateDeductionTypeOptions();
            calculateNetPay();
        });

        $("#addDeductionRow").click(function(){
            // Count unique selected types in the table
            var selectedTypes = [];
            $('#deductionTable select[id^="deductionType"]').each(function() {
                var val = $(this).val();
                if (val && !selectedTypes.includes(val)) {
                    selectedTypes.push(val);
                }
            });
            
            // If all types are selected, show error
            if (selectedTypes.length >= deductionTypes.length) {
                alert('All deduction types have been added. Please remove a row to add a different type.');
                return;
            }
            
            addDeductionRow();
        });

        // Remove earnings row
        $("#earningsTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#earningsTable tr").each(function (index) {
                $(this).find('input[name^="earningsNo"]').val(index + 1);
            });

            earningsRowCount--;
            updateEarningsTypeOptions();
            calculateNetPay();
        });

        $("#addEarningsRow").click(function(){
            // Count unique selected types in the table
            var selectedTypes = [];
            $('#earningsTable select[id^="earningsType"]').each(function() {
                var val = $(this).val();
                if (val && !selectedTypes.includes(val)) {
                    selectedTypes.push(val);
                }
            });
            
            // If all types are selected, show error
            if (selectedTypes.length >= earningTypes.length) {
                alert('All earnings types have been added. Please remove a row to add a different type.');
                return;
            }
            
            addEarningsRow();
        });

        // Trigger calculation on input change
        $(document).on('input', 'input[id^="deductionAmt"]', function() {
            calculateNetPay();
        });

        // Auto default earnings and deduction when select employee
        $('#employee').on('change', function() {
            var employee = $(this).val();
            var payslipDate = $('#date').val();

            if (employee) {
                $('#spinnerLoading').show();
                $.post('php/getUser.php', {userID: employee}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        // Clear existing rows
                        $('#earningsTable').html('');
                        earningsRowCount = 0;
                        
                        // Add basic salary if exists
                        if(obj.message.basic_salary && parseFloat(obj.message.basic_salary) > 0) {
                            addEarningsRow('BASIC', 'Basic Salary', obj.message.basic_salary);
                        }

                        // Add deduction rows (EPF, SOCSO, TAX, EIS) if allowed
                        category = obj.message.pcb_category;
                        isResident = obj.message.is_resident;
                        $('#deductionTable').html('');
                        deductionRowCount = 0;
                        addDeductionRow('EMP_EPF', 'Employee EPF', calculateEPF(obj.message.basic_salary));
                        addDeductionRow('EMP_SOCSO', 'Employee SOCSO', calculateSOCSO(obj.message.basic_salary));
                        addDeductionRow('EMP_EIS', 'Employee EIS', calculateEIS(obj.message.basic_salary));
                        addDeductionRow('TAX', 'Tax', calculatePCB(payslipDate, category, obj.message.basic_salary, 0, isResident, nonResidentTaxRate, tax, individualReliefFund, individualEpfReliefFund));
                        
                        $('#spinnerLoading').hide();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                    else{
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                    }
                });
            }
        });

        // Handle deductionType change
        $(document).on('change', 'select[id^="deductionType"]', function() {
            updateDeductionTypeOptions();
        });

        // Handle earningsType change 
        $(document).on('change', 'select[id^="earningsType"]', function() {
            updateEarningsTypeOptions();
            recalculateStatutoryDeductions();
        });

        // Trigger recalculation on earnings amount change
        $(document).on('input', 'input[id^="earningsAmt"]', function() {
            recalculateStatutoryDeductions();
            calculateNetPay();
        });

        $('#date').on('change', function() {
            recalculateStatutoryDeductions();
            calculateNetPay();
        });
    });

    // Update earnings type options to disable already selected types
    function updateEarningsTypeOptions() {
        var selectedTypes = [];
        $('#earningsTable select[id^="earningsType"]').each(function() {
            var val = $(this).val();
            if (val) selectedTypes.push(val);
        });
        
        $('#earningsTable select[id^="earningsType"]').each(function() {
            var currentVal = $(this).val();
            $(this).find('option').each(function() {
                var optVal = $(this).val();
                $(this).prop('disabled', selectedTypes.includes(optVal) && optVal !== currentVal);
            });
        });
    }

    // Update deduction type options to disable already selected types
    function updateDeductionTypeOptions() {
        var selectedTypes = [];
        $('#deductionTable select[id^="deductionType"]').each(function() {
            var val = $(this).val();
            if (val) selectedTypes.push(val);
        });
        
        $('#deductionTable select[id^="deductionType"]').each(function() {
            var currentVal = $(this).val();
            $(this).find('option').each(function() {
                var optVal = $(this).val();
                $(this).prop('disabled', selectedTypes.includes(optVal) && optVal !== currentVal);
            });
        });
    }

    // Recalculate statutory deductions (EPF, SOCSO, EIS)
    function recalculateStatutoryDeductions() {
        var contributoryTypes = ['BASIC', 'ALLOWANCE', 'OVERTIME', 'BONUS', 'COMMISSION'];
        var totalContributory = 0;
        var basicSalary = 0;
        var additionalRemunerationThisMonth = 0;
        
        $('select[id^="earningsType"]').each(function() {
            var rowIndex = $(this).attr('id').replace('earningsType', '');
            var type = $(this).val();
            var amount = parseFloat($('#earningsAmt' + rowIndex).val()) || 0;
            
            if (type === 'BASIC') basicSalary = amount;
            if (contributoryTypes.includes(type)) {
                totalContributory += amount;
            }
        });

        additionalRemunerationThisMonth = totalContributory - basicSalary;
        
        // Update EPF
        $('select[id^="deductionType"]').each(function() {
            var rowIndex = $(this).attr('id').replace('deductionType', '');
            var type = $(this).val();
            
            if (type == 'EMP_EPF') {
                $('#deductionAmt' + rowIndex).val(calculateEPF(basicSalary));
            } else if (type == 'EMP_SOCSO') {
                $('#deductionAmt' + rowIndex).val(calculateSOCSO(basicSalary));
            } else if (type == 'EMP_EIS') {
                $('#deductionAmt' + rowIndex).val(calculateEIS(basicSalary));
            } else if (type == 'TAX') {
                $('#deductionAmt' + rowIndex).val(calculatePCB($('#date').val(), category, basicSalary, additionalRemunerationThisMonth, isResident, nonResidentTaxRate, tax, individualReliefFund, individualEpfReliefFund));
            }
        });
    }

    // Calculate Employee EPF
    function calculateEPF(amount) {
        var rate = epf ? parseFloat(epf) : 0;
        var amount = amount ? parseFloat(amount) : 0;
        var epfAmount = 0;
        if (amount > 0) {
            epfAmount = (amount * rate) / 100;
        }

        return epfAmount.toFixed(2);
    }

    // Calculate Employee SOCSO
    function calculateSOCSO(amount) {
        var amount = amount ? parseFloat(amount) : 0;
        if (amount <= 0 || !socso || socso.length === 0) return 0;
        
        for (var i = 0; i < socso.length; i++) {
            var min = parseFloat(socso[i].min);
            var max = socso[i].max === "" ? Infinity : parseFloat(socso[i].max);
            
            if (amount >= min && amount <= max) {
                return parseFloat(socso[i].employee).toFixed(2);
            }
        }
        
        return 0;
    }

    // Calculate Employee EIS
    function calculateEIS(amount) {
        var amount = amount ? parseFloat(amount) : 0;
        if (amount <= 0 || !eis || eis.length === 0) return 0;
        
        for (var i = 0; i < eis.length; i++) {
            var min = parseFloat(eis[i].min);
            var max = eis[i].max === "" ? Infinity : parseFloat(eis[i].max);
            
            if (amount >= min && amount <= max) {
                return parseFloat(eis[i].employee).toFixed(2);
            }
        }
        
        return 0;
    }

    // Calculate PCB
    function calculatePCB(date, userCategory, amount, additionalRemunerationThisMonth, isResident, companyNonResidentTaxRate, companyResidentTaxRate, individualReliefFund, individualEpfReliefFund) {
        if (isResident == 'N') {
            var rate = companyNonResidentTaxRate ? parseFloat(companyNonResidentTaxRate) : 0;
            var taxAmount = calculatePCBNonResident(parseFloat(amount) + parseFloat(additionalRemunerationThisMonth || 0), rate);
            return taxAmount.toFixed(2);
        } else {
            if (parseFloat(additionalRemunerationThisMonth) > 0) {
                // If there is additional remuneration
                var opts = {
                    // PCB category:
                    // - Use 1 for Category 1 & 3 (uses b1 from your table) — most employees.
                    // - Use 2 for Category 2 (uses b2 from your table) — special case.
                    category: userCategory || 1,

                    // Pnormal = annual chargeable income (WITHOUT additional remuneration like bonus),
                    // simplified as:
                    //   (monthly basic * 12) - individual relief - EPF relief
                    // IMPORTANT:
                    // - individualReliefFund example: 9000
                    // - individualEpfReliefFund example: 4000 (EPF relief cap)
                    // - if you have other taxable earnings/deductions, include them in Pnormal too.
                    Pnormal: (parseFloat(amount) * 12) - parseFloat(individualReliefFund || 0) - parseFloat(individualEpfReliefFund || 0),

                    // Ptotal = annual chargeable income (WITH additional remuneration like bonus),
                    // i.e. same as Pnormal but include bonus/commission/etc (annualized as needed).
                    // You should calculate Ptotal upstream and pass it in here.
                    PTotal: ((parseFloat(amount) * 12) + parseFloat(additionalRemunerationThisMonth || 0)) - parseFloat(individualReliefFund || 0) - parseFloat(individualEpfReliefFund || 0),

                    // X = accumulated PCB already deducted year-to-date (YTD),
                    // used to "catch up" correctly when calculating current month PCB.
                    // If employee joined mid-year or had previous employer, this matters.
                    X: 0,

                    // Z = accumulated zakat paid year-to-date (excluding current month zakat),
                    // used as an offset in the computerized PCB method (if you support zakat).
                    Z: 0,

                    // nPlus1 = remaining months in the year INCLUDING current month:
                    // Jan=12, Feb=11, ... Dec=1.
                    // Using your helper that derives this from a date.
                    nPlus1: getNPlus1(date)
                    };

                var taxAmount = calculatePCBAnnualizedRemuneration(amount, additionalRemunerationThisMonth, companyResidentTaxRate, opts)
            } else {
                // No additional remuneration
                var opts = {
                    // 1) Category selection for B value
                    // - If you don’t know, use 1 (Category 1 & 3 uses b1)
                    // - If employee is Category 2 (per LHDN spec table), use 2 (uses b2)
                    category: userCategory || 1,

                    // 2) Annual chargeable income P (if you want more accurate than salary*12)
                    // If omitted: P = monthlySalary * 12
                    // Use this when you include other taxable earnings (allowances/bonus/commission)
                    // and/or subtract deductions/reliefs (EPF, etc.) before annualising.
                    P: (parseFloat(amount) * 12) - parseFloat(individualReliefFund || 0) - parseFloat(individualEpfReliefFund || 0), // example: 66000

                    // 3) Accumulated PCB already deducted year-to-date (X)
                    // Needed when calculating mid-year or when employee joined later,
                    // or when there is previous employer MTD.
                    X: 0,

                    // 4) Accumulated zakat paid year-to-date excluding current month (Z)
                    // Only if you handle zakat offset.
                    Z: 0,

                    // 5) Remaining months in year INCLUDING current month (n+1)
                    // If omitted: 12
                    // Example:
                    //   Jan = 12, Feb = 11, ... Dec = 1
                    nPlus1: getNPlus1(date)
                };
                var taxAmount = calculatePCBNormal(amount, companyResidentTaxRate, opts)
            }

            return taxAmount.toFixed(2);
        }
    }

    // Add deduction row
    function addDeductionRow(type = null, desc = '', amt = 0) {
        var $addContents = $("#deductionDetail").clone();
        $("#deductionTable").append($addContents.html());

        $("#deductionTable").find('.details:last').attr("id", "detail" + deductionRowCount);
        $("#deductionTable").find('.details:last').attr("data-index", deductionRowCount);
        $("#deductionTable").find('#remove:last').attr("id", "remove" + deductionRowCount);

        $("#deductionTable").find('#deductionNo:last').attr('name', 'deductionNo['+deductionRowCount+']').attr("id", "deductionNo" + deductionRowCount).val(deductionRowCount + 1);
        var lastSelect = $("#deductionTable").find('#deductionType:last').attr('name', 'deductionType['+deductionRowCount+']').attr("id", "deductionType" + deductionRowCount);
        $("#deductionTable").find('#deductionDesc:last').attr('name', 'deductionDesc['+deductionRowCount+']').attr("id", "deductionDesc" + deductionRowCount).val(desc);
        $("#deductionTable").find('#deductionAmt:last').attr('name', 'deductionAmt['+deductionRowCount+']').attr("id", "deductionAmt" + deductionRowCount).val(amt);

        // Set the type first
        if (type) {
            lastSelect.val(type);
        } else {
            // Get already selected types to find first available
            var selectedTypes = [];
            $('#deductionTable select[id^="deductionType"]').not(lastSelect).each(function() {
                var val = $(this).val();
                if (val) selectedTypes.push(val);
            });
            
            // Find first non-selected option
            var firstAvailable = null;
            lastSelect.find('option').each(function() {
                var optVal = $(this).val();
                if (optVal && !selectedTypes.includes(optVal)) {
                    firstAvailable = optVal;
                    return false; // break
                }
            });
            lastSelect.val(firstAvailable);
        }
        
        deductionRowCount++;
        updateDeductionTypeOptions();
        calculateNetPay();
    }

    // Add earnings row
    function addEarningsRow(type = null, desc = '', amt = 0) {
        var $addContents = $("#earningsDetail").clone();
        $("#earningsTable").append($addContents.html());

        $("#earningsTable").find('.details:last').attr("id", "detail" + earningsRowCount);
        $("#earningsTable").find('.details:last').attr("data-index", earningsRowCount);
        $("#earningsTable").find('#remove:last').attr("id", "remove" + earningsRowCount);

        $("#earningsTable").find('#earningsNo:last').attr('name', 'earningsNo['+earningsRowCount+']').attr("id", "earningsNo" + earningsRowCount).val(earningsRowCount + 1);
        var lastSelect = $("#earningsTable").find('#earningsType:last').attr('name', 'earningsType['+earningsRowCount+']').attr("id", "earningsType" + earningsRowCount);
        $("#earningsTable").find('#earningsDesc:last').attr('name', 'earningsDesc['+earningsRowCount+']').attr("id", "earningsDesc" + earningsRowCount).val(desc);
        $("#earningsTable").find('#earningsAmt:last').attr('name', 'earningsAmt['+earningsRowCount+']').attr("id", "earningsAmt" + earningsRowCount).val(amt);

        // Set the type first
        if (type) {
            lastSelect.val(type);
        } else {
            // Get already selected types to find first available
            var selectedTypes = [];
            $('#earningsTable select[id^="earningsType"]').not(lastSelect).each(function() {
                var val = $(this).val();
                if (val) selectedTypes.push(val);
            });
            
            // Find first non-selected option
            var firstAvailable = null;
            lastSelect.find('option').each(function() {
                var optVal = $(this).val();
                if (optVal && !selectedTypes.includes(optVal)) {
                    firstAvailable = optVal;
                    return false; // break
                }
            });
            lastSelect.val(firstAvailable);
        }

        earningsRowCount++;
        updateEarningsTypeOptions();
        calculateNetPay();
    }

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getPayslip.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#paySlipNo').val(obj.message.payslip_no);
                $('#addModal').find('#date')[0]._flatpickr.setDate(formatDate2(new Date(obj.message.date)), true);
                $('#addModal').find('#date')[0]._flatpickr.set('clickOpens', false);
                $('#addModal').find('#employee').val(obj.message.user_id).select2('destroy').select2();
                $('#addModal').find('#paymentType').val(obj.message.payment_type).trigger('change');
                $('#addModal').find('#chequeNo').val(obj.message.cheque_no);
                $('#addModal').find('#cashBook').val(obj.message.cashbook_id).trigger('change');

                // Earnings Details
                $('#earningsTable').html('');
                earningsRowCount = 0;
                if (obj.message.earnings_detail && obj.message.earnings_detail.length > 0){
                    for(var i = 0; i < obj.message.earnings_detail.length; i++){
                        var item = obj.message.earnings_detail[i];
                        addEarningsRow(item.type, item.desc, item.amt);
                    }
                }

                // Deduction Details
                $('#deductionTable').html('');
                deductionRowCount = 0;
                if (obj.message.deductions_detail && obj.message.deductions_detail.length > 0){
                    for(var i = 0; i < obj.message.deductions_detail.length; i++){
                        var item = obj.message.deductions_detail[i];
                        addDeductionRow(item.type, item.desc, item.amt);
                    }
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

    function calculateNetPay() {
        var grossPay = 0;
        var totalDeduction = 0;
        
        $('input[id^="earningsAmt"]').each(function() {
            grossPay += parseFloat($(this).val()) || 0;
        });
        
        $('input[id^="deductionAmt"]').each(function() {
            totalDeduction += parseFloat($(this).val()) || 0;
        });
        
        var netPay = grossPay - totalDeduction;
        
        $('#grossPay').val(grossPay.toFixed(2));
        $('#totalDeduction').val(totalDeduction.toFixed(2));
        $('#netPay').val(netPay.toFixed(2));
        $('#netPayDisplay').text(netPay.toFixed(2));
    }

    function print(id) {
        $.post('php/printPayslip.php', { userID: id }, function(response){
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
    
    function deactivate(id) {
        if (confirm('Are you sure you want to cancel this item?')) {
            $('#cancelModal').find('#id').val(id);
            $('#cancelModal').find('#deleteReason').val('');
            $('#cancelModal').find('#isMulti').val('N');
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
    </script>
</body>
</html>
