<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$plantId = $_SESSION['plant'];

$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
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

    <title><?=$languageArray['cash_book_code'][$language]?> | PWS - Weighing System</title>
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
                                                                        <label class="form-label"><?=$languageArray['cash_book_no_code'][$language]?></label>
                                                                        <input type="text" class="form-control input-readonly" id="cashBookNo" name="cashBookNo" placeholder="<?=$languageArray['cash_book_no_code'][$language]?>" readonly>
                                                                    </div>
                                                                    <div class="col-xxl-4 col-lg-4">
                                                                        <label class="form-label"><?=$languageArray['date_code'][$language]?></label>
                                                                        <input type="text" class="form-control" id="date" name="date" required>
                                                                    </div>
                                                                </div>

                                                                <div class="row mt-4">
                                                                    <div class="col-xxl-6 col-lg-6" style="display:none">
                                                                        <h6 class="mb-2"><?=$languageArray['cash_book_code'][$language]?> (-)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%"><?=$languageArray['number_short_code'][$language]?></th>
                                                                                    <th width="30%"><?=$languageArray['type_code'][$language]?></th>
                                                                                    <th width="45%"><?=$languageArray['description_code'][$language]?></th>
                                                                                    <th width="15%"><?=$languageArray['amount_code'][$language]?></th>
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
                                                                    <div class="col-xxl-12 col-lg-12">
                                                                        <h6 class="mb-2"><?=$languageArray['cash_book_code'][$language]?> (+)</h6>
                                                                        <table class="table table-bordered align-middle">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%"><?=$languageArray['number_short_code'][$language]?></th>
                                                                                    <th width="35%"><?=$languageArray['type_code'][$language]?></th>
                                                                                    <th width="30%"><?=$languageArray['description_code'][$language]?></th>
                                                                                    <th width="25%"><?=$languageArray['amount_code'][$language]?></th>
                                                                                    <th width="10%"><button type="button" class="btn btn-sm btn-success" id="addAdditionRow"><i class="bx bx-plus"></i></button></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="additionTable">
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="3" class="text-end"><?=$languageArray['total_code'][$language]?>:</th>
                                                                                    <th><input type="number" class="form-control form-control-sm" id="totalAddition" name="totalAddition" readonly></th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
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
                                                                <h5 class="card-title text-white mb-0"><?=$languageArray['cash_book_payment_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!-- <button type="button" id="exportPdf" class="btn btn-info waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export Report
                                                                </button> -->
                                                                <button type="button" id="addCashBook" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
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
                                                                    <th><?=$languageArray['cash_book_no_code'][$language]?></th>
                                                                    <!-- <th><?=$languageArray['total_deduction_code'][$language]?> (RM)</th> -->
                                                                    <th><?=$languageArray['total_addition_code'][$language]?> (RM)</th>
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

    <script type="text/html" id="deductionDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="deductionNo" name="deductionNo" readonly>
            </td>
            <td>
                <select class="form-select" id="deductionType" name="deductionType" required>
                    <option value="CASHOUT">Cash Out (RM)</option>
                    <option value="CREDITNOTE">Credit Note (RM)</option>
                    <option value="CREDITFFBRAM">Credit FFB Advance (Ramp) (RM)</option>
                    <option value="CREDITFFBHQ">Credit FFB Advance (HQ) (RM)</option>
                    <option value="CREDITFFBADV">Credit FFB Advance (RM)</option>
                    <option value="GENERAL">General Expenses (RM)</option>
                    <option value="PETROL_DIESEL">Petrol/Diesel (RM)</option>
                    <option value="STAFFADV">Staff Advance (RM)</option>
                    <option value="STAFFSAL">Staff Salary (RM)</option>
                    <option value="FFBSHORTAGE">FFB Shortage (MT)</option>
                    <option value="CREDITFFBPAY">Credit FFB payment (RM)</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="deductionDesc" name="deductionDesc" style="background-color:white;" required>
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

    <script type="text/html" id="additionDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="additionNo" name="additionNo" readonly>
            </td>
            <td>
                <select class="form-select" id="additionType" name="additionType" required>
                    <option value="CASHHQ">Cash From HQ (RM)</option>
                    <option value="CASHRAM">Cash Received By Ramp (RM)</option>
                    <option value="CASHFFBCUST">Cash Received By FFB Customer (RM)</option>
                    <option value="DEBITNOTE">Debit Note (RM)</option>
                    <option value="CASHIN">Cash In (RM)</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="additionDesc" name="additionDesc" style="background-color:white;" required>
            </td>
            <td>
                <input type="number" class="form-control" id="additionAmt" name="additionAmt" style="background-color:white;" value="0" required>
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

    <script type="text/javascript">
    
    var deductionRowCount = 0;
    var additionRowCount = 0;
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
                'url':'php/filterCashBook.php',
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
                { data: 'cash_book_no' },
                // { data: 'total_deduction' },
                { data: 'total_addition' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        return '<div class="dropdown d-inline-block">' +
                            '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                '<i class="ri-more-fill align-middle"></i>' +
                            '</button>' +
                            '<ul class="dropdown-menu dropdown-menu-end">' +
                                // '<li>' +
                                //     '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')">' +
                                //         '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                //     '</a>' +
                                // '</li>' +
                                '<li>' +
                                    '<a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')">' +
                                        '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit' +
                                    '</a>' +
                                '</li>' +
                                // '<li>' +
                                //     '<a class="dropdown-item remove-item-btn" id="delete'+data+'" onclick="deactivate('+data+')">' +
                                //         '<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete' +
                                //     '</a>' +
                                // '</li>' +
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
                    'url':'php/filterCashBook.php',
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
                    { data: 'cash_book_no' },
                    // { data: 'total_deduction' },
                    { data: 'total_addition' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            return '<div class="dropdown d-inline-block">' +
                                '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                    '<i class="ri-more-fill align-middle"></i>' +
                                '</button>' +
                                '<ul class="dropdown-menu dropdown-menu-end">' +
                                    // '<li>' +
                                    //     '<a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')">' +
                                    //         '<i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print' +
                                    //     '</a>' +
                                    // '</li>' +
                                    '<li>' +
                                        '<a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')">' +
                                            '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit' +
                                        '</a>' +
                                    '</li>' +
                                    // '<li>' +
                                    //     '<a class="dropdown-item remove-item-btn" id="delete'+data+'" onclick="deactivate('+data+')">' +
                                    //         '<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete' +
                                    //     '</a>' +
                                    // '</li>' +
                                '</ul>' +
                            '</div>';
                        }
                    }
                ]   
            });
        });

        $('#addCashBook').on('click', function(){
            // Show Capture Buttons When Add New
            $('#addModal').find('#id').val("");
            $('#addModal').find('#date')[0]._flatpickr.setDate(formatDate2(today), true);
            $('#addModal').find('#date')[0]._flatpickr.set('clickOpens', true);
            $('#addModal').find('#cashBookNo').val("");
            $('#addModal').find('#totalDeduction').val("0");
            $('#addModal').find('#totalAddition').val("0");

            deductionRowCount = 0;
            additionRowCount = 0;
            $('#addModal').find('#deductionTable').html('');
            $('#addModal').find('#additionTable').html('');

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
                url: 'php/cashbook.php',
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

        // Remove deduction row
        $("#deductionTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#deductionTable tr").each(function (index) {
                $(this).find('input[name^="deductionNo"]').val(index + 1);
            });

            deductionRowCount--;
            calculateTotals();
        });

        // Add deduction row
        $("#addDeductionRow").click(function(){
            var $addContents = $("#deductionDetail").clone();
            $("#deductionTable").append($addContents.html());

            $("#deductionTable").find('.details:last').attr("id", "detail" + deductionRowCount);
            $("#deductionTable").find('.details:last').attr("data-index", deductionRowCount);
            $("#deductionTable").find('#remove:last').attr("id", "remove" + deductionRowCount);

            $("#deductionTable").find('#deductionNo:last').attr('name', 'deductionNo['+deductionRowCount+']').attr("id", "deductionNo" + deductionRowCount).val(deductionRowCount + 1);
            $("#deductionTable").find('#deductionType:last').attr('name', 'deductionType['+deductionRowCount+']').attr("id", "deductionType" + deductionRowCount);
            $("#deductionTable").find('#deductionDesc:last').attr('name', 'deductionDesc['+deductionRowCount+']').attr("id", "deductionDesc" + deductionRowCount);
            $("#deductionTable").find('#deductionAmt:last').attr('name', 'deductionAmt['+deductionRowCount+']').attr("id", "deductionAmt" + deductionRowCount);

            deductionRowCount++;
            calculateTotals();
        });

        // Remove addition row
        $("#additionTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#additionTable tr").each(function (index) {
                $(this).find('input[name^="additionNo"]').val(index + 1);
            });

            additionRowCount--;
            calculateTotals();
        });

        // Add addition row
        $("#addAdditionRow").click(function(){
            var $addContents = $("#additionDetail").clone();
            $("#additionTable").append($addContents.html());

            $("#additionTable").find('.details:last').attr("id", "detail" + additionRowCount);
            $("#additionTable").find('.details:last').attr("data-index", additionRowCount);
            $("#additionTable").find('#remove:last').attr("id", "remove" + additionRowCount);
            
            $("#additionTable").find('#additionNo:last').attr('name', 'additionNo['+additionRowCount+']').attr("id", "additionNo" + additionRowCount).val(additionRowCount + 1);
            $("#additionTable").find('#additionType:last').attr('name', 'additionType['+additionRowCount+']').attr("id", "additionType" + additionRowCount);
            $("#additionTable").find('#additionDesc:last').attr('name', 'additionDesc['+additionRowCount+']').attr("id", "additionDesc" + additionRowCount);
            $("#additionTable").find('#additionAmt:last').attr('name', 'additionAmt['+additionRowCount+']').attr("id", "additionAmt" + additionRowCount);

            additionRowCount++;
            calculateTotals();
        });

        // Trigger calculation on input change
        $(document).on('input', 'input[id^="deductionAmt"], input[id^="additionAmt"]', function() {
            calculateTotals();
        });

        // Handle deductionType change for CREDITFFBPAY
        $(document).on('change', 'select[id^="deductionType"]', function() {
            var rowIndex = $(this).attr('id').replace('deductionType', '');
            var descField = $('#deductionDesc' + rowIndex);
            
            if($(this).val() == 'CREDITFFBPAY') {
                var descFieldParent = descField.closest('td');
                
                // Add d-flex class to parent td if not exists
                if(!descFieldParent.hasClass('d-flex')) {
                    descFieldParent.addClass('d-flex gap-2');
                }
                
                // Change description field to col-6
                if(!descField.parent().hasClass('col-6')) {
                    descField.wrap('<div class="col-6"></div>');
                }
                
                // Add payment voucher select if not exists
                if(descFieldParent.find('select[id^="pvSelect"]').length === 0) {
                    $.post('php/getOutstandingPaymentVouchers.php', function(data) {
                        var obj = JSON.parse(data);
                        if(obj.status === 'success') {
                            var selectHtml = '<div class="col-6"><select class="form-select" id="pvSelect' + rowIndex + '" name="pvSelect[' + rowIndex + ']" required>';
                            selectHtml += '<option value="">Please Select Payment Voucher</option>';
                            $.each(obj.message, function(i, voucher) {
                                selectHtml += '<option value="' + voucher.id + '">' + voucher.voucher_no + ' - RM' + voucher.outstanding_amount + '</option>';
                            });
                            selectHtml += '</select></div>';
                            descField.parent().after(selectHtml);
                        }
                    });
                }
            } else {
                var descFieldParent = descField.closest('td');
                // Remove payment voucher select if exists
                descFieldParent.find('select[id^="pvSelect"]').parent().remove();
                
                // Remove d-flex class from parent td
                descFieldParent.removeClass('d-flex gap-2');
                
                // Unwrap description field if wrapped
                if(descField.parent().hasClass('col-6')) {
                    descField.unwrap();
                }
            }
        });

    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getCashBook.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#cashBookNo').val(obj.message.cash_book_no);
                $('#addModal').find('#date')[0]._flatpickr.setDate(formatDate2(new Date(obj.message.date)), true);
                $('#addModal').find('#date')[0]._flatpickr.set('clickOpens', false);
                $('#addModal').find('#totalDeduction').val(obj.message.total_deduction);
                $('#addModal').find('#totalAddition').val(obj.message.total_addition);

                // Deduction Details
                $('#deductionTable').html('');
                deductionRowCount = 0;
                if (obj.message.deduction_details.length > 0){
                    for(var i = 0; i < obj.message.deduction_details.length; i++){
                        var item = obj.message.deduction_details[i];
                        var $addContents = $("#deductionDetail").clone();
                        $("#deductionTable").append($addContents.html());

                        $("#deductionTable").find('.details:last').attr("id", "detail" + deductionRowCount);
                        $("#deductionTable").find('.details:last').attr("data-index", deductionRowCount);
                        $("#deductionTable").find('#remove:last').attr("id", "remove" + deductionRowCount);

                        $("#deductionTable").find('#deductionNo:last').attr('name', 'deductionNo['+deductionRowCount+']').attr("id", "deductionNo" + deductionRowCount).val(deductionRowCount + 1);
                        $("#deductionTable").find('#deductionType:last').attr('name', 'deductionType['+deductionRowCount+']').attr("id", "deductionType" + deductionRowCount).val(item.type);
                        $("#deductionTable").find('#deductionDesc:last').attr('name', 'deductionDesc['+deductionRowCount+']').attr("id", "deductionDesc" + deductionRowCount);
                        $("#deductionTable").find('#deductionAmt:last').attr('name', 'deductionAmt['+deductionRowCount+']').attr("id", "deductionAmt" + deductionRowCount).val(item.amount);
                        
                        if(item.type == 'CREDITFFBPAY') {
                            var descFieldParent = $('#deductionDesc' + deductionRowCount).closest('td');
                            
                            // Add d-flex class to parent td
                            if(!descFieldParent.hasClass('d-flex')) {
                                descFieldParent.addClass('d-flex gap-2');
                            }
                            
                            // Wrap description field in col-6
                            if(!$('#deductionDesc' + deductionRowCount).parent().hasClass('col-6')) {
                                $('#deductionDesc' + deductionRowCount).wrap('<div class="col-6"></div>');
                            }
                            
                            $('#deductionDesc' + deductionRowCount).val(item.desc);
                            
                            (function(rowIdx, pvId) {
                                $.post('php/getOutstandingPaymentVouchers.php', {selectedId: pvId}, function(data) {
                                    var obj2 = JSON.parse(data);
                                    if(obj2.status === 'success') {
                                        var selectHtml = '<div class="col-6"><select class="form-select" id="pvSelect' + rowIdx + '" name="pvSelect[' + rowIdx + ']" required>';
                                        selectHtml += '<option value="">Please Select Payment Voucher</option>';
                                        $.each(obj2.message, function(j, voucher) {
                                            var selected = voucher.id == pvId ? 'selected' : '';
                                            selectHtml += '<option value="' + voucher.id + '" ' + selected + '>' + voucher.voucher_no + ' - RM' + voucher.outstanding_amount + '</option>';
                                        });
                                        selectHtml += '</select></div>';
                                        $('#deductionDesc' + rowIdx).parent().after(selectHtml);
                                    }
                                });
                            })(deductionRowCount, item.pv_id);
                        } else {
                            $("#deductionDesc" + deductionRowCount).val(item.desc);
                        }

                        deductionRowCount++;
                    }
                }

                // Addition Details
                $('#additionTable').html('');
                additionRowCount = 0;
                if (obj.message.addition_details.length > 0){
                    for(var i = 0; i < obj.message.addition_details.length; i++){
                        var item = obj.message.addition_details[i];
                        var $addContents = $("#additionDetail").clone();
                        $("#additionTable").append($addContents.html());

                        $("#additionTable").find('.details:last').attr("id", "detail" + additionRowCount);
                        $("#additionTable").find('.details:last').attr("data-index", additionRowCount);
                        $("#additionTable").find('#remove:last').attr("id", "remove" + additionRowCount);

                        $("#additionTable").find('#additionNo:last').attr('name', 'additionNo['+additionRowCount+']').attr("id", "additionNo" + additionRowCount).val(additionRowCount + 1);
                        $("#additionTable").find('#additionType:last').attr('name', 'additionType['+additionRowCount+']').attr("id", "additionType" + additionRowCount).val(item.type);
                        $("#additionTable").find('#additionDesc:last').attr('name', 'additionDesc['+additionRowCount+']').attr("id", "additionDesc" + additionRowCount).val(item.desc);
                        $("#additionTable").find('#additionAmt:last').attr('name', 'additionAmt['+additionRowCount+']').attr("id", "additionAmt" + additionRowCount).val(item.amount);

                        additionRowCount++;
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

    function calculateTotals() {
        var totalDeductions = 0;
        var totalAdditions = 0;
        $('input[id^="deductionAmt"]').each(function() {
            var index = $(this).attr('id').replace('deductionAmt', '');
            var type = $('#deductionType' + index).val();
            if (type != 'FFBSHORTAGE') {
                totalDeductions += parseFloat($(this).val()) || 0;
            }
        });
        $('input[id^="additionAmt"]').each(function() { 
            var index = $(this).attr('id').replace('additionAmt', '');
            var type = $('#additionType' + index).val();
            if (type != 'CASHHQ') {
                totalAdditions += parseFloat($(this).val()) || 0; 
            }
        });
        
        $('#totalDeduction').val(totalDeductions.toFixed(2));
        $('#totalAddition').val(totalAdditions.toFixed(2));
    }

    function print(id) {
        $.post('php/printExpenseReport.php', { userID: id }, function(response){
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
