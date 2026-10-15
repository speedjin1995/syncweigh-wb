<?php include 'layouts/session.php'; ?>
<?php
if (!in_array($_SESSION['roles'] ?? '', array('MANAGER', 'ADMIN', 'SADMIN'))) {
    echo '<script type="text/javascript">location.href = "index.php";</script>';
    exit();
}
?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title><?=$languageArray['planned_schedule_code'][$language]?> | Synctronix - Weighing System</title>
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
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header fs-5 text-white" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch" style="background-color: #405189;">
                                        <i class="mdi mdi-chevron-down pull-right"></i>
                                        <?=$languageArray['search_records_code'][$language]?>
                                    </div>
                                    <div id="collapseSearch" class="collapse show" aria-labelledby="collapseSearch">
                                        <div class="card-body">
                                            <?php $vehicles = $db->query("SELECT veh_number FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC"); ?>
                                            <?php $suppliers = $db->query("SELECT supplier_code, name FROM Supplier WHERE status = '0' ORDER BY name ASC"); ?>
                                            <?php $contractors = $db->query("SELECT id, name FROM Contractor WHERE status = '0' ORDER BY name ASC"); ?>
                                            <?php $wastes = $db->query("SELECT raw_mat_code, name FROM Raw_Mat WHERE status = '0' ORDER BY name ASC"); ?>
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
                                                            <label for="vehicleNoSearch" class="form-label"><?=$languageArray['vehicle_no_code'][$language]?></label>
                                                            <select id="vehicleNoSearch" class="form-select select2">
                                                                <option value="" selected>-</option>
                                                                <?php while($rowVehicle=mysqli_fetch_assoc($vehicles)){ ?>
                                                                    <option value="<?=$rowVehicle['veh_number'] ?>"><?=$rowVehicle['veh_number'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="button" class="btn btn-danger" id="clearAllSearch"><i class="bx bx-x"></i> <?=$languageArray['clear_all_code'][$language]?></button>
                                                            <button type="button" class="btn btn-success" id="filterSearch"><i class="bx bx-search-alt"></i> <?=$languageArray['search_code'][$language]?></button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="uploadModal">
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
                                                <button type="button" class="btn btn-success" id="submitScheduleUpload"><?=$languageArray['submit_code'][$language]?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form role="form" id="scheduleForm" autocomplete="off">
                                            <div class="modal-header bg-gray-dark color-palette">
                                                <h4 class="modal-title" id="scheduleModalTitle"><?=$languageArray['add_new_code'][$language]?></h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" id="scheduleId" name="id">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="scheduleVehNumber" class="form-label"><?=$languageArray['vehicle_no_code'][$language]?></label>
                                                        <input type="text" class="form-control" id="scheduleVehNumber" name="vehNumber" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="scheduleSupplier" class="form-label"><?=$languageArray['supplier_code'][$language]?></label>
                                                        <select class="form-select select2" id="scheduleSupplier" name="supplierCode">
                                                            <option value="">-</option>
                                                            <?php while($rowSupplier=mysqli_fetch_assoc($suppliers)){ ?>
                                                                <option value="<?=htmlspecialchars($rowSupplier['supplier_code']) ?>"><?=htmlspecialchars($rowSupplier['supplier_code'].' - '.$rowSupplier['name']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="scheduleContractor" class="form-label"><?=$languageArray['contractor_code'][$language]?></label>
                                                        <select class="form-select select2" id="scheduleContractor" name="contractorId">
                                                            <option value="">-</option>
                                                            <?php while($rowContractor=mysqli_fetch_assoc($contractors)){ ?>
                                                                <option value="<?=$rowContractor['id'] ?>"><?=htmlspecialchars($rowContractor['name']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="scheduleWaste" class="form-label"><?=$languageArray['waste_code'][$language]?></label>
                                                        <select class="form-select select2" id="scheduleWaste" name="rawMatCode">
                                                            <option value="">-</option>
                                                            <?php while($rowWaste=mysqli_fetch_assoc($wastes)){ ?>
                                                                <option value="<?=htmlspecialchars($rowWaste['raw_mat_code']) ?>"><?=htmlspecialchars($rowWaste['name']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="scheduleVehicleType" class="form-label"><?=$languageArray['vehicle_type_code'][$language]?></label>
                                                        <input type="text" class="form-control" id="scheduleVehicleType" name="vehicleType">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="scheduleStartDate" class="form-label"><?=$languageArray['start_date_code'][$language]?></label>
                                                        <input type="text" class="form-control" id="scheduleStartDate" name="startDate" placeholder="dd/mm/yyyy" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="scheduleEndDate" class="form-label"><?=$languageArray['end_date_code'][$language]?></label>
                                                        <input type="text" class="form-control" id="scheduleEndDate" name="endDate" placeholder="dd/mm/yyyy" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="scheduleTrips" class="form-label"><?=$languageArray['trips_per_day_code'][$language]?></label>
                                                        <input type="number" class="form-control" id="scheduleTrips" name="trips" min="0" step="1" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                <button type="button" class="btn btn-success" id="saveSchedule"><?=$languageArray['submit_code'][$language]?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="scheduleLogModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-gray-dark color-palette">
                                            <h4 class="modal-title"><?=$languageArray['view_log_code'][$language]?></h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="scheduleLogBody"></div>
                                        <div class="modal-footer justify-content-end bg-gray-dark color-palette">
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="errorModal" style="display:none">
                                <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header bg-gray-dark color-palette">
                                            <h4 class="modal-title"><?=$languageArray['error_log_code'][$language]?></h4>
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
                                                                <h5 class="card-title mb-0 text-white"><?=$languageArray['previous_records_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="template/Planned_Schedule_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
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
                                                                <button type="button" id="addSchedule" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-add-line align-middle me-1"></i>
                                                                    <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="scheduleTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['start_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['end_date_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['contractor_code'][$language]?></th>
                                                                    <th><?=$languageArray['waste_code'][$language]?></th>
                                                                    <th><?=$languageArray['vehicle_type_code'][$language]?></th>
                                                                    <th><?=$languageArray['trips_per_day_code'][$language]?></th>
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
    <script src="plugins/select2/js/select2.full.min.js"></script>
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

$(function () {
    // Search box, same as index.php
    $('#collapseSearch .select2').select2({
        allowClear: true,
        placeholder: "Please Select"
    });

    $('#scheduleModal .select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#scheduleModal')
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

    $('#fromDateSearch').flatpickr({ 
        dateFormat: "d-m-Y", 
        defaultDate: new Date()
    });

    $('#toDateSearch').flatpickr({ 
        dateFormat: "d-m-Y", 
        defaultDate: new Date()
    });

    renderTable();

    var savedToast = sessionStorage.getItem('scheduleToast');
    if (savedToast) {
        sessionStorage.removeItem('scheduleToast');
        $("#successBtn").attr('data-toast-text', savedToast);
        $("#successBtn").click();
    }

    $('#filterSearch').on('click', function(){
        renderTable();
    });

    $('#clearAllSearch').on('click', function(){
        ['#fromDateSearch', '#toDateSearch'].forEach(function(sel) {
            var picker = $(sel)[0]._flatpickr;
            if (picker) { picker.clear(); } else { $(sel).val(''); }
        });
        $('#vehicleNoSearch').val('').trigger('change');
        renderTable();
    });

    ['#scheduleStartDate', '#scheduleEndDate'].forEach(function(sel) {
        $(sel).flatpickr({ dateFormat: "d/m/Y" });
    });

    $('#scheduleForm').validate({
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); }
    });

    $('#scheduleVehNumber').on('keyup', function(){
        $(this).val($(this).val().toUpperCase());
    });

    $('#selectAllCheckbox').on('change', function(){
        $('#scheduleTable tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
    });

    $('#multiDeactivate').on('click', function(){
        var selectedIds = [];
        $('#scheduleTable tbody input[type="checkbox"]:checked').each(function(){
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert("Please select at least one planned schedule to delete.");
            return;
        }
        if (!confirm('Are you sure you want to delete these planned schedules?')) {
            return;
        }

        $('#spinnerLoading').show();
        $.post('php/modules/plannedSchedule/index.php?action=deactivateMulti', {ids: selectedIds}, function(response){
            var obj = JSON.parse(response);
            $('#spinnerLoading').hide();
            if (obj.status === 'success') {
                $('#selectAllCheckbox').prop('checked', false);
                table.ajax.reload(null, false);
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else {
                $("#failBtn").attr('data-toast-text', obj.message);
                $("#failBtn").click();
            }
        });
    });

    $('#addSchedule').on('click', function(){
        openScheduleModal(null);
    });

    $('#saveSchedule').on('click', function(){
        if (!$('#scheduleForm').valid()) {
            return;
        }

        $('#spinnerLoading').show();
        $.post('php/modules/plannedSchedule/index.php?action=save', $('#scheduleForm').serialize(), function(response){
            var obj = JSON.parse(response);
            $('#spinnerLoading').hide();
            if (obj.status === 'success') {
                // Reload so the vehicle filter (rendered by PHP) includes a new plate; the toast shows after reload
                sessionStorage.setItem('scheduleToast', obj.message);
                location.reload();
            }
            else {
                $("#failBtn").attr('data-toast-text', obj.message);
                $("#failBtn").click();
            }
        });
    });

    $('#uploadExcel').on('click', function(){
        $('#previewTable').html('');
        $('#fileInput').val('');
        $('#uploadModal').modal('show');
    });

    $('#previewButton').on('click', function(){
        var file = document.getElementById('fileInput').files[0];
        if (!file) {
            alert("Please choose an Excel file first.");
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            displayPreview(e.target.result);
        };
        reader.readAsBinaryString(file);
    });

    $('#submitScheduleUpload').on('click', function(){
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

        $.ajax({
            url: 'php/modules/plannedSchedule/index.php?action=upload',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                var obj = JSON.parse(response);
                $('#spinnerLoading').hide();
                if (obj.status === 'success') {
                    $('#uploadModal').modal('hide');
                    table.ajax.reload(null, false);
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if (obj.status === 'error') {
                    $('#uploadModal').modal('hide');
                    table.ajax.reload(null, false);
                    $('#errorList').empty();
                    obj.message.forEach(function(msg) {
                        $('#errorList').append($('<li>').text(msg));
                    });
                    $('#errorModal').modal('show');
                }
                else {
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
            }
        });
    });
});

function openScheduleModal(row){
    var form = $('#scheduleForm');
    form.validate().resetForm();
    form.find('.is-invalid').removeClass('is-invalid');

    $('#scheduleId').val(row ? row.id : '');
    $('#scheduleModalTitle').text(row ? <?=json_encode($languageArray['edit_code'][$language])?> : <?=json_encode($languageArray['add_new_code'][$language])?>);
    $('#scheduleVehNumber').val(row ? row.veh_number : '');
    $('#scheduleSupplier').val(row && row.supplier_code ? row.supplier_code : '').trigger('change');
    $('#scheduleContractor').val(row && row.contractor_id ? row.contractor_id : '').trigger('change');
    $('#scheduleWaste').val(row && row.raw_mat_code ? row.raw_mat_code : '').trigger('change');
    $('#scheduleVehicleType').val(row ? row.vehicle_type : '');
    $('#scheduleTrips').val(row ? row.max_entry : '');
    $('#scheduleStartDate')[0]._flatpickr.setDate(row ? row.start_date : null, false, 'd/m/Y');
    $('#scheduleEndDate')[0]._flatpickr.setDate(row ? row.end_date : null, false, 'd/m/Y');

    $('#scheduleModal').modal('show');
}

function editSchedule(id){
    var rows = table.rows().data();
    for (var i = 0; i < rows.length; i++) {
        if (rows[i].id == id) {
            openScheduleModal(rows[i]);
            return;
        }
    }
}

function deactivate(id){
    if (!confirm('Are you sure you want to delete this planned schedule?')) {
        return;
    }

    $('#spinnerLoading').show();
    $.post('php/modules/plannedSchedule/index.php?action=deactivate', {id: id}, function(response){
        var obj = JSON.parse(response);
        $('#spinnerLoading').hide();
        if (obj.status === 'success') {
            table.ajax.reload(null, false);
            $("#successBtn").attr('data-toast-text', obj.message);
            $("#successBtn").click();
        }
        else {
            $("#failBtn").attr('data-toast-text', obj.message);
            $("#failBtn").click();
        }
    });
}

function showScheduleLog(id){
    $('#spinnerLoading').show();
    $.post('php/modules/plannedSchedule/index.php?action=getLogs', {id: id}, function(response){
        var obj = JSON.parse(response);
        $('#spinnerLoading').hide();
        if (obj.status === 'success') {
            $('#scheduleLogBody').html(formatScheduleLog(obj.message));
            $('#scheduleLogModal').modal('show');
        }
        else {
            $("#failBtn").attr('data-toast-text', obj.message);
            $("#failBtn").click();
        }
    });
}

function formatScheduleLog(logs){
    if (logs.length == 0) {
        return '<div class="p-2 text-muted">' + <?=json_encode($languageArray['no_log_found_code'][$language])?> + '</div>';
    }

    var actionText = {
        1: <?=json_encode($languageArray['create_code'][$language])?>,
        2: <?=json_encode($languageArray['update_code'][$language])?>,
        3: <?=json_encode($languageArray['delete_code'][$language])?>
    };
    var rows = '';

    for (var i = 0; i < logs.length; i++) {
        rows += '<tr><td>' + (actionText[logs[i].action] || logs[i].action) + '</td><td>' + logs[i].start_date + '</td><td>' + logs[i].end_date + '</td><td>' + logs[i].max_entry + '</td><td>' + (logs[i].action_by || '') + '</td><td>' + logs[i].event_date + '</td></tr>';
    }

    return '<table class="table table-sm table-bordered mb-0">' +
        '<thead><tr><th><?=$languageArray['action_code'][$language]?></th><th><?=$languageArray['start_date_code'][$language]?></th><th><?=$languageArray['end_date_code'][$language]?></th>' +
        '<th><?=$languageArray['trips_per_day_code'][$language]?></th><th><?=$languageArray['action_by_code'][$language]?></th><th><?=$languageArray['action_date_code'][$language]?></th></tr></thead>' +
        '<tbody>' + rows + '</tbody></table>';
}

function renderTable(){
    // Destroy old DataTables if exist
    if ($.fn.DataTable.isDataTable('#scheduleTable')) {
        $("#scheduleTable").DataTable().clear().destroy();
    }

    table = $("#scheduleTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/modules/plannedSchedule/index.php',
            'data': function (d) {
                d.action = 'getAll';
                d.fromDate = $('#fromDateSearch').val() || '';
                d.toDate = $('#toDateSearch').val() || '';
                d.vehicleNo = $('#vehicleNoSearch').val() || '';
            }
        },
        'columns': [
            {
                data: 'id',
                className: 'select-checkbox',
                orderable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="' + data + '"/>';
                }
            },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'veh_number' },
            { data: 'contractor_name' },
            { data: 'raw_mat_name' },
            { data: 'vehicle_type' },
            { data: 'max_entry' },
            {
                data: null,
                orderable: false,
                className: 'action-button',
                render: function (data, type, row) {
                    return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                    '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                    '<li><a class="dropdown-item edit-item-btn" id="edit'+row.id+'" onclick="editSchedule('+row.id+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> <?=$languageArray['edit_code'][$language]?></a></li>' +
                    '<li><a class="dropdown-item" id="log'+row.id+'" onclick="showScheduleLog('+row.id+')"><i class="ri-file-list-3-line align-bottom me-2 text-muted"></i> <?=$languageArray['view_log_code'][$language]?></a></li>' +
                    '<li><a class="dropdown-item remove-item-btn" id="deactivate'+row.id+'" onclick="deactivate('+row.id+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> <?=$languageArray['delete_code'][$language]?> </a></li></ul></div>';
                }
            }
        ]
    });
}

// Excel stores dates as serial numbers; show them as dd/mm/yyyy in the preview
function formatExcelDate(value) {
    var d = XLSX.SSF.parse_date_code(value);
    return ('0' + d.d).slice(-2) + '/' + ('0' + d.m).slice(-2) + '/' + d.y;
}

function displayPreview(data) {
    var workbook = XLSX.read(data, { type: 'binary' });
    var sheet = workbook.Sheets[workbook.SheetNames[0]];
    var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
    var headers = jsonData[0] || [];

    var htmlTable = '<table style="width:50%;"><thead><tr>';
    headers.forEach(function(header) {
        htmlTable += '<th>' + $('<div>').text(header).html() + '</th>';
    });
    htmlTable += '</tr></thead><tbody>';

    var rowNo = 0;
    for (var i = 1; i < jsonData.length; i++) {
        var rowData = jsonData[i] || [];

        // Skip blank rows (Excel formatting can leave empty rows in the used range)
        var hasValue = rowData.some(function(v) { return v != null && String(v).trim() !== ''; });
        if (!hasValue) {
            continue;
        }

        htmlTable += '<tr>';
        for (var j = 0; j < headers.length; j++) {
            var name = String(headers[j]).replace(/[^a-zA-Z0-9]/g, '');
            var value = rowData[j] == null ? '' : rowData[j];
            if ((name === 'STARTDATE' || name === 'ENDDATE') && typeof value === 'number') {
                value = formatExcelDate(value);
            }
            var escaped = $('<div>').text(value).html().replace(/"/g, '&quot;');
            // Excel row number, so import errors point at the right row in the sheet
            var excelRow = j === 0 ? '<input type="hidden" name="ExcelRow[' + rowNo + ']" value="' + (i + 1) + '" />' : '';
            htmlTable += '<td>' + excelRow + '<input type="text" name="' + name + '[' + rowNo + ']" value="' + escaped + '" /></td>';
        }
        htmlTable += '</tr>';
        rowNo++;
    }
    htmlTable += '</tbody></table>';

    document.getElementById('previewTable').innerHTML = htmlTable;
}

    </script>
</body>

</html>
