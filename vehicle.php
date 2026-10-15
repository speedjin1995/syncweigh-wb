<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
    $customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
    $supplier = $db->query("SELECT * FROM Supplier WHERE status = '0'");
    $transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
    $contractor = $db->query("SELECT * FROM Contractor WHERE status = '0'");
?>

<head>
    <title><?=$languageArray['vehicle_code'][$language]?> | Synctronix - Weighing System</title>
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
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                            
                            <div class="row">
                                <div class="col-xl-3 col-md-6 add-new-weight">

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle"><?=$languageArray['add_new_code'][$language]?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="vehicleForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehicleNo" class="col-sm-4 col-form-label"><?=$languageArray['vehicle_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="vehicleNo" name="vehicleNo" placeholder="<?=$languageArray['vehicle_no_code'][$language]?>" required>
                                                                                        <div class="invalid-feedback">
                                                                                            <?=$languageArray['please_fill_in_the_field_code'][$language]?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehicleWeight" class="col-sm-4 col-form-label"><?=$languageArray['vehicle_weight_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="vehicleWeight" name="vehicleWeight" placeholder="<?=$languageArray['vehicle_weight_code'][$language]?>" required>
                                                                                        <div class="invalid-feedback">
                                                                                            <?=$languageArray['please_fill_in_the_field_code'][$language]?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="vehicleType" class="col-sm-4 col-form-label"><?=$languageArray['vehicle_type_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="vehicleType" name="vehicleType" placeholder="<?=$languageArray['vehicle_type_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="contractor" class="col-sm-4 col-form-label"><?=$languageArray['contractor_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="contractor" name="contractorId">
                                                                                            <option value="" selected>-</option>
                                                                                            <?php while($rowContractor=mysqli_fetch_assoc($contractor)){ ?>
                                                                                                <option value="<?=$rowContractor['id'] ?>"><?=$rowContractor['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3" style="display:none">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label"><?=$languageArray['transporter_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" id="transporter" name="transporter">
                                                                                            <option value="" selected disabled hidden>Please Select</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="customer" class="col-sm-4 col-form-label"><?=$languageArray['customer_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="customer" name="customer">
                                                                                            <option selected>-</option>
                                                                                            <?php while($rowC=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowC['name'] ?>" data-code="<?=$rowC['customer_code'] ?>"><?=$rowC['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="supplier" class="col-sm-4 col-form-label"><?=$languageArray['supplier_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="supplier" name="supplier">
                                                                                            <option selected>-</option>
                                                                                            <?php while($rowS=mysqli_fetch_assoc($supplier)){ ?>
                                                                                                <option value="<?=$rowS['name'] ?>" data-code="<?=$rowS['supplier_code'] ?>"><?=$rowS['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <input type="hidden" class="form-control" id="id" name="id">
                                                                            <input type="hidden" id="transporterCode" name="transporterCode">
                                                                            <input type="hidden" id="customerCode" name="customerCode">
                                                                            <input type="hidden" id="supplierCode" name="supplierCode">
                                                                                                                                                         
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                                <button type="button" class="btn btn-success" id="submitVehicle"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="entryLimitModal" tabindex="-1" role="dialog" aria-labelledby="entryLimitModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="entryLimitModalTitle"><?=$languageArray['entry_limit_code'][$language]?> - <span id="entryLimitVehicleNo"></span></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="entryLimitForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="card border h-100">
                                                                    <div class="card-header">
                                                                        <h5 class="card-title mb-0"><?=$languageArray['default_limit_code'][$language]?></h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="maxEntryPerDay" class="col-sm-5 col-form-label"><?=$languageArray['max_entry_per_day_code'][$language]?></label>
                                                                            <div class="col-sm-7">
                                                                                <input type="number" class="form-control" id="maxEntryPerDay" name="maxEntryPerDay" min="0" step="1" placeholder="<?=$languageArray['zero_unlimited_code'][$language]?>">
                                                                                <small class="text-muted"><?=$languageArray['max_entry_per_day_hint_code'][$language]?></small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="card border h-100">
                                                                    <div class="card-header">
                                                                        <h5 class="card-title mb-0"><?=$languageArray['date_limit_code'][$language]?></h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="limitDate" class="col-sm-5 col-form-label"><?=$languageArray['limit_date_code'][$language]?></label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" id="limitDate" name="limitDate" placeholder="<?=$languageArray['limit_date_code'][$language]?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="dateMaxEntry" class="col-sm-5 col-form-label"><?=$languageArray['max_entry_for_date_code'][$language]?></label>
                                                                            <div class="col-sm-7">
                                                                                <input type="number" class="form-control" id="dateMaxEntry" name="dateMaxEntry" min="0" step="1" placeholder="<?=$languageArray['blank_use_default_code'][$language]?>">
                                                                                <small class="text-muted"><?=$languageArray['max_entry_for_date_hint_code'][$language]?></small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <label class="col-sm-5 col-form-label"><?=$languageArray['used_code'][$language]?></label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" id="entryUsed" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="entryLimitId" name="id">
                                                        <div class="hstack gap-2 justify-content-end mt-3">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                            <button type="button" class="btn btn-success" id="submitEntryLimit"><?=$languageArray['submit_code'][$language]?></button>
                                                        </div>
                                                    </form>

                                                    <div class="card border mt-3 mb-0">
                                                        <div class="card-header">
                                                            <h5 class="card-title mb-0"><?=$languageArray['date_limit_records_code'][$language]?></h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <table id="entryLimitTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?=$languageArray['limit_date_code'][$language]?></th>
                                                                        <th><?=$languageArray['max_entry_code'][$language]?></th>
                                                                        <th><?=$languageArray['status_code'][$language]?></th>
                                                                        <th><?=$languageArray['created_by_code'][$language]?></th>
                                                                        <th><?=$languageArray['modified_by_code'][$language]?></th>
                                                                        <th><?=$languageArray['modified_date_code'][$language]?></th>
                                                                        <th><?=$languageArray['action_code'][$language]?></th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="uploadModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title"><?=$languageArray['upload_excel_code'][$language]?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton"><?=$languageArray['preview_data_code'][$language]?></button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                        <button type="button" class="btn btn-success" id="uploadVehicle"><?=$languageArray['submit_code'][$language]?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="modal fade" id="errorModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title">Error Log</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <ol id="errorList" class="text-danger mt-2" style="padding-left: 20px;"></ol>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                                <h5 class="card-title mb-0"><?=$languageArray['previous_records_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="template/Vehicle_Template.xlsx" download>
                                                                    <button type="button" id="downloadTemplate" class="btn btn-info waves-effect waves-light">
                                                                        <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                        <?=$languageArray['download_template_code'][$language]?>
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    <?=$languageArray['upload_excel_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="addVehicle" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="vehicleTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['vehicle_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_weight_code'][$language]?></th>
                                                                    <th><?=$languageArray['customer_code'][$language]?></th>
                                                                    <th><?=$languageArray['supplier_code'][$language]?></th>
                                                                    <th><?=$languageArray['status_code'][$language]?></th>
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

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->




    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <script src="assets/js/pages/form-validation.init.js"></script>
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



<script type="text/javascript">

var table;
var entryLimitDatePicker;
var entryLimitTable;

$(function () {
    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#vehicleTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
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

    table = $("#vehicleTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/modules/vehicle/loadVehicle.php'
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
            { data: 'veh_number' },
            { data: 'vehicle_weight' },
            { data: 'customer_name' },
            { data: 'supplier_name' },
            { data: 'status' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    if(row.status == 'Inactive'){
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item remove-item-btn" id="reactivate'+data+'" onclick="reactivate('+data+')">Reactivate </a></li></ul></div>';
                    }
                    else{
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> <?=$languageArray['edit_code'][$language] ?></a></li>' +
                        '<li><a class="dropdown-item" id="entryLimit'+data+'" onclick="entryLimit('+data+')"><i class="ri-timer-line align-bottom me-2 text-muted"></i> <?=$languageArray['entry_limit_code'][$language]?></a></li>' +
                        '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> <?=$languageArray['delete_code'][$language] ?> </a></li></ul></div>';
                    }
                }
            }
        ]       
    });
    
    // $.validator.setDefaults({
    //     submitHandler: function() {
    $('#submitVehicle').on('click', function(){
        if($('#vehicleForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/modules/vehicle/vehicle.php', $('#vehicleForm').serialize(), function(data){
                var obj = JSON.parse(data);
                if(obj.status === 'success') {
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                }
                else if(obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    toastr["error"](obj.message, "Failed:");
                }
                else {}
            });
        }
        // }
    });

    $('#addVehicle').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#vehicleNo').val("");
        $('#addModal').find('#vehicleWeight').val("");
        $('#addModal').find('#vehicleType').val("");
        $('#addModal').find('#contractor').val("").trigger('change');
        $('#addModal').find('#customer').val("").trigger('change');
        $('#addModal').find('#customerCode').val("");
        $('#addModal').find('#supplier').val("").trigger('change');
        $('#addModal').find('#supplierCode').val("");
        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal').modal('show');
        
        $('#vehicleForm').validate({
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

    entryLimitDatePicker = $('#limitDate').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: 'today'
    });

    $('#limitDate').on('change', function(){
        loadEntryLimit($('#entryLimitId').val(), false);
    });

    $('#submitEntryLimit').on('click', function(){
        var maxEntry = $('#maxEntryPerDay').val();
        var dateMax = $('#dateMaxEntry').val();

        if ((maxEntry !== '' && parseInt(maxEntry) < 0) || (dateMax !== '' && parseInt(dateMax) < 0)) {
            $('#maxEntryPerDay').toggleClass('is-invalid', maxEntry !== '' && parseInt(maxEntry) < 0);
            $('#dateMaxEntry').toggleClass('is-invalid', dateMax !== '' && parseInt(dateMax) < 0);
            return;
        }

        $('#entryLimitForm .is-invalid').removeClass('is-invalid');
        $('#spinnerLoading').show();
        $.post('php/modules/vehicle/vehicleEntryLimit.php', $('#entryLimitForm').serialize(), function(data){
            var obj = JSON.parse(data);
            if(obj.status === 'success') {
                table.ajax.reload();
                $('#spinnerLoading').hide();
                loadEntryLimit($('#entryLimitId').val(), false);
                loadEntryLimitTable($('#entryLimitId').val());
                toastr["success"](obj.message, "Success:");
            }
            else {
                $('#spinnerLoading').hide();
                toastr["error"](obj.message, "Failed:");
            }
        });
    });

    // View Log button: expand the date limit record to show its change log.
    // Bound on the table because its tbody only exists after DataTables draws it.
    $('#entryLimitTable').on('click', 'tbody .view-log-btn', function () {
        var tr = $(this).closest('tr');
        var row = entryLimitTable.row(tr);

        if (!row.data()) {
            return;
        }

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            $.post('php/modules/vehicle/vehicleEntryLimit.php', {mode: 'logs', id: $('#entryLimitId').val(), limitId: row.data().id}, function (data) {
                var obj = JSON.parse(data);
                if (obj.status === 'success') {
                    row.child(formatEntryLimitLog(obj.message)).show();
                    tr.addClass('shown');
                }
                else {
                    toastr["error"](obj.message, "Failed:");
                }
            });
        }
    });

    $('#vehicleNo').on('keyup', function(){
        var x = $('#vehicleNo').val();
        x = x.toUpperCase();
        $('#addModal').find('#vehicleNo').val(x);
    });

    //customer
    $('#customer').on('change', function(){
        $('#customerCode').val($('#customer :selected').data('code'));
    });

    //supplier
    $('#supplier').on('change', function(){
        $('#supplierCode').val($('#supplier :selected').data('code'));
    });

    $('#uploadVehicle').on('click', function(){
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
            url: 'php/modules/vehicle/uploadVehicle.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                var obj = JSON.parse(response);
                if (obj.status === 'success') {
                    $('#spinnerLoading').hide();
                    $('#uploadModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#vehicleTable').DataTable().ajax.reload(null, false);
                }
                else if (obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    toastr["error"](obj.message, "Failed:");
                }
                else if (obj.status === 'error') {
                    $('#spinnerLoading').hide();
                    $('#uploadModal').modal('hide');
                    $('#vehicleTable').DataTable().ajax.reload(null, false);
                    $('#errorModal').find('#errorList').empty();
                    var errorMessage = obj.message;
                    for (var i = 0; i < errorMessage.length; i++) {
                        $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);
                    }
                    $('#errorModal').modal('show');
                }
                else {
                    $('#spinnerLoading').hide();
                    toastr["error"]("Failed to save", "Failed:");
                }
            }
        });
    });

    $('#uploadExcel').on('click', function(){
        $('#previewTable').html('');
        $('#fileInput').val('');
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

    $('#multiDeactivate').on('click', function () {
        $('#spinnerLoading').show();
        var selectedIds = []; // An array to store the selected 'id' values

        $("#vehicleTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to delete these vehicles?')) {
                $.post('php/modules/vehicle/deleteVehicle.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        toastr["success"](obj.message, "Success:");
                        $('#spinnerLoading').hide();
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }

            $('#spinnerLoading').hide();
        } 
        else {
            // Optionally, you can display a message or take another action if no IDs are selected
            alert("Please select at least one vehicle to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/modules/vehicle/getVehicle.php', {userID: id}, function(data)
    {
        var obj = JSON.parse(data);
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#vehicleNo').val(obj.message.veh_number);
            $('#addModal').find('#vehicleWeight').val(obj.message.vehicle_weight);
            $('#addModal').find('#vehicleType').val(obj.message.vehicle_type);
            $('#addModal').find('#contractor').val(obj.message.contractor_id || '').trigger('change');
            $('#addModal').find('#customer').val(obj.message.customer_name).trigger('change');
            $('#addModal').find('#supplier').val(obj.message.supplier_name).trigger('change');

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal').modal('show');

            $('#vehicleForm').validate({
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
            toastr["error"](obj.message, "Failed:");
        }
        else{
            $('#spinnerLoading').hide();
            toastr["error"](obj.message, "Failed:");
        }
        $('#spinnerLoading').hide();
    });
}

function entryLimit(id){
    $('#entryLimitForm .is-invalid').removeClass('is-invalid');
    $('#entryLimitId').val(id);
    entryLimitDatePicker.setDate(new Date(), false);
    loadEntryLimit(id, true);
    loadEntryLimitTable(id);
}

function loadEntryLimitTable(id){
    if (entryLimitTable) {
        entryLimitTable.clear().destroy();
    }

    entryLimitTable = $("#entryLimitTable").DataTable({
        // Not responsive: its collapsed-column child rows would clash with the log child rows
        "responsive": false,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'searching': true,
        'serverMethod': 'post',
        'order': [[0, 'desc']],
        'ajax': {
            'url': 'php/modules/vehicle/loadVehicleEntryLimit.php',
            'data': {
                vehicleId: id
            }
        },
        'columns': [
            { data: 'limit_date', name: 'limit_date' },
            { data: 'max_entry' },
            { data: 'status' },
            { data: 'created_by' },
            { data: 'modified_by' },
            { data: 'modified_date' },
            {
                data: 'id',
                orderable: false,
                className: 'action-button',
                render: function (data, type, row) {
                    return '<button type="button" class="btn btn-soft-info btn-sm view-log-btn" title=<?=$languageArray['view_log_code'][$language]?>">' +
                        '<i class="ri-file-list-3-line align-middle"></i> <?=$languageArray['view_log_code'][$language]?></button>';
                }
            }
        ]
    });
}

function formatEntryLimitLog(logs){
    if (logs.length == 0) {
        return '<div class="p-2 text-muted">' + <?=json_encode($languageArray['no_log_found_code'][$language])?> + '</div>';
    }

    var actionText = {
        Create: <?=json_encode($languageArray['create_code'][$language])?>,
        Update: <?=json_encode($languageArray['update_code'][$language])?>,
        Delete: <?=json_encode($languageArray['delete_code'][$language])?>
    };
    var rows = '';

    for (var i = 0; i < logs.length; i++) {
        rows += '<tr><td>' + (actionText[logs[i].action] || logs[i].action) + '</td><td>' + logs[i].limit_date + '</td><td>' + logs[i].max_entry + '</td><td>' + (logs[i].action_by || '') + '</td><td>' + logs[i].event_date + '</td></tr>';
    }

    return '<table class="table table-sm table-bordered mb-0">' +
        '<thead><tr><th>' + <?=json_encode($languageArray['action_code'][$language])?> + '</th><th>' + <?=json_encode($languageArray['limit_date_code'][$language])?> + '</th><th>' + <?=json_encode($languageArray['max_entry_code'][$language])?> + '</th>' +
        '<th>' + <?=json_encode($languageArray['action_by_code'][$language])?> + '</th><th>' + <?=json_encode($languageArray['action_date_code'][$language])?> + '</th></tr></thead>' +
        '<tbody>' + rows + '</tbody></table>';
}

// Fills the entry limit modal for the selected date; showModal also loads the default
function loadEntryLimit(id, showModal){
    if (!id) {
        return;
    }

    $('#spinnerLoading').show();
    $.post('php/modules/vehicle/vehicleEntryLimit.php', {mode: 'get', id: id, limitDate: $('#limitDate').val()}, function(data){
        var obj = JSON.parse(data);
        $('#spinnerLoading').hide();

        if(obj.status === 'success'){
            if (showModal) {
                $('#entryLimitVehicleNo').text(obj.message.veh_number);
                $('#maxEntryPerDay').val(obj.message.max_entry_per_day);
            }

            $('#dateMaxEntry').val(obj.message.date_max_entry === null ? '' : obj.message.date_max_entry);
            $('#entryUsed').val(obj.message.count + ' / ' + (obj.message.max > 0 ? obj.message.max : <?=json_encode($languageArray['unlimited_code'][$language])?>));

            if (showModal) {
                $('#entryLimitModal').modal('show');
            }
        }
        else{
            toastr["error"](obj.message, "Failed:");
        }
    });
}

function deactivate(id){
    $('#spinnerLoading').show();
    if (confirm('Are you sure you want to delete this vehicle?')) {
        $.post('php/modules/vehicle/deleteVehicle.php', {userID: id}, function(data){
            var obj = JSON.parse(data);

            if(obj.status === 'success'){
                table.ajax.reload();
                $('#spinnerLoading').hide();
                toastr["success"](obj.message, "Success:");
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                toastr["error"](obj.message, "Failed:");
            }
            else{
                $('#spinnerLoading').hide();
                toastr["error"](obj.message, "Failed:");
            }
        });
    }
    $('#spinnerLoading').hide();
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


    // Create HTML table headers
    var htmlTable = '<table style="width:50%;"><thead><tr>';
    headers.forEach(function(header) {
        htmlTable += '<th>' + header + '</th>';
    });
    htmlTable += '</tr></thead><tbody>';

    // Iterate over the data and create table rows
    for (var i = 1; i < jsonData.length; i++) {
        htmlTable += '<tr>';
        var rowData = jsonData[i];

        for (var j = 0; j < headers.length; j++) {
            var cellData = rowData[j];
            var formattedData = cellData;

            // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
            if (typeof cellData === 'number' && cellData > 0) {
                var excelDate = XLSX.SSF.parse_date_code(cellData);
            }

            htmlTable += '<td><input type="text" id="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+(i-1)+'" name="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
        }
        htmlTable += '</tr>';
    }

    htmlTable += '</tbody></table>';

    var previewTable = document.getElementById('previewTable');
    previewTable.innerHTML = htmlTable;
}

function reactivate(id) {
  if (confirm('Do you want to reactivate this vehicle?')) {
    $('#spinnerLoading').show();
    $.post('php/reactivateMasterData.php', {userID: id, type: "Vehicle"}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
            table.ajax.reload();
            $('#spinnerLoading').hide();
            toastr["success"](obj.message, "Success:");
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            toastr["error"](obj.message, "Failed:");
        }
        else{
            $('#spinnerLoading').hide();
            toastr["error"](obj.message, "Failed:");
        }

        $('#spinnerLoading').hide();
    });
  }

  $('#spinnerLoading').hide();
}
</script>
    </body>

    </html>