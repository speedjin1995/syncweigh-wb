<?php 
include 'layouts/session.php'; 
include 'layouts/head-main.php'; 
require_once 'php/db_connect.php';

// Ensure session is active
if (!isset($_SESSION['id'])) {
    echo '<script type="text/javascript">location.href = "login.html";</script>'; 
    exit();
}

$id = $_SESSION['id'];
$did = '1';

// Prepare query for Deduction (only 1 record)
$stmt = $db->prepare("SELECT * FROM Deduction WHERE id = ? LIMIT 1");
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}
$stmt->bind_param('s', $did);
$stmt->execute();
$result = $stmt->get_result();

// Default values
$status = 'Disable';
$F1 = $F2 = $F3 = $F4 = $F5 = $F6 = $F7 = $F8 = $F9 = $F10 = $F11 = $F12 = 0;
$autoData = null;
if ($row = $result->fetch_assoc()) {
    $status = $row['status'] ?? 'Disable';
    $F1 = $row['F1'] ?? 0;
    $F2 = $row['F2'] ?? 0;
    $F3 = $row['F3'] ?? 0;
    $F4 = $row['F4'] ?? 0;
    $F5 = $row['F5'] ?? 0;
    $F6 = $row['F6'] ?? 0;
    $F7 = $row['F7'] ?? 0;
    $F8 = $row['F8'] ?? 0;
    $F9 = $row['F9'] ?? 0;
    $F10 = $row['F10'] ?? 0;
    $F11 = $row['F11'] ?? 0;
    $F12 = $row['F12'] ?? 0;
    $autoData = $row['auto_data'] ?? null;
    $defaultRangeMin = $row['default_range_min'] ?? 0;
    $defaultRangeMax = $row['default_range_max'] ?? 0;
    $defaultWeight = $row['default_range_weight'] ?? 0;
    $customers = !empty($row['customers']) ? json_decode($row['customers'], true) : [];
    $suppliers = !empty($row['suppliers']) ? json_decode($row['suppliers'], true) : [];
}

// Cleanup
$stmt->close();
$result->free();

$customer = $db->query("SELECT * FROM Customer WHERE status = '0' AND status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' AND status = '0' ORDER BY name ASC");
?>
    <head>
        <title><?=$languageArray['deduction_setup_code'][$language]?> | PWS - Weighing System</title>
        <?php include 'layouts/title-meta.php'; ?>

        <!-- swiper css -->
        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">

        <?php include 'layouts/head-css.php'; ?>

        <style>
            .select2-container--default .select2-selection--multiple .select2-selection__choice,
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: #000 !important;
            }
        </style>

    </head>

    <?php include 'layouts/body.php'; ?>

        <!-- Begin page -->
        <div id="layout-wrapper">

            <?php include 'layouts/menu.php'; ?>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="row col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <form id="profileForm" action="php/updateDeduction.php" method="post">
                                        <div class="d-flex align-items-center mb-3 justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <label for="statusSwitch" class="col-form-label me-2">Mode</label>
                                                <select id="statusSwitch" name="statusSwitch" class="form-select">
                                                    <option value="Manual" <?php if ($status == 'Manual') echo 'selected'; ?>>Manual</option>
                                                    <option value="Auto" <?php if ($status == 'Auto') echo 'selected'; ?>>Auto</option>
                                                    <!--option value="Default" <?php if ($status == 'Default') echo 'selected'; ?>>Default</option-->
                                                    <option value="Customer_Supplier" <?php if ($status == 'Customer_Supplier') echo 'selected'; ?>>Customer/Supplier</option>
                                                    <option value="Disable" <?php if ($status == 'Disable') echo 'selected'; ?>>Disable</option>
                                                </select>
                                            </div>

                                            <button type="button" id="autoNewBtn" class="btn btn-primary d-none">
                                                <i class="ri-add-circle-line align-middle me-1"></i>Add New
                                            </button>
                                        </div>

                                        <!-- Grid for F1-F12 -->
                                        <div id="manualView">
                                            <div class="row">
                                                <div class="col-3">
                                                    <label>F1 - F3 ( - ) kg</label>
                                                    <input type="number" class="form-control mb-2" name="F1" placeholder="F1" min="0" value="<?= htmlspecialchars($F1) ?>">
                                                    <input type="number" class="form-control mb-2" name="F2" placeholder="F2" min="0" value="<?= htmlspecialchars($F2) ?>">
                                                    <input type="number" class="form-control mb-2" name="F3" placeholder="F3" min="0" value="<?= htmlspecialchars($F3) ?>">
                                                </div>
                                                <div class="col-3">
                                                    <label>F4 - F6 ( + ) kg</label>
                                                    <input type="number" class="form-control mb-2" name="F4" placeholder="F4" min="0" value="<?= htmlspecialchars($F4) ?>">
                                                    <input type="number" class="form-control mb-2" name="F5" placeholder="F5" min="0" value="<?= htmlspecialchars($F5) ?>">
                                                    <input type="number" class="form-control mb-2" name="F6" placeholder="F6" min="0" value="<?= htmlspecialchars($F6) ?>">
                                                </div>
                                                <div class="col-3">
                                                    <label>F7 - F9 ( - ) %</label>
                                                    <input type="number" class="form-control mb-2" name="F7" placeholder="F7" min="0" max="100" value="<?= htmlspecialchars($F7) ?>">
                                                    <input type="number" class="form-control mb-2" name="F8" placeholder="F8" min="0" max="100" value="<?= htmlspecialchars($F8) ?>">
                                                    <input type="number" class="form-control mb-2" name="F9" placeholder="F9" min="0" max="100" value="<?= htmlspecialchars($F9) ?>">
                                                </div>
                                                <div class="col-3">
                                                    <label>F10 - F12 ( + ) %</label>
                                                    <input type="number" class="form-control mb-2" name="F10" placeholder="F10" min="0" max="100" value="<?= htmlspecialchars($F10) ?>">
                                                    <input type="number" class="form-control mb-2" name="F11" placeholder="F11" min="0" max="100" value="<?= htmlspecialchars($F11) ?>">
                                                    <input type="number" class="form-control mb-2" name="F12" placeholder="F12" min="0" max="100" value="<?= htmlspecialchars($F12) ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grid for Auto -->
                                        <div id="autoView" style="display: none;">
                                            <div class="row">
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <table class="table table-primary">
                                                        <thead>
                                                            <tr>
                                                                <th width="20%">Ranges</th>
                                                                <th>(-) kg</th>
                                                                <th>(+) kg</th>
                                                                <th>(-%)</th>
                                                                <th>(+%)</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="autoTable"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grid for Default -->
                                        <div id="defaultView" style="display: none;">
                                            <div class="card bg-light p-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold mb-0">Range</label>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <input type="number" id="defaultRangeMin" name="defaultRangeMin" class="form-control me-2" placeholder="Min" min="0" style="width: 45%;" value="<?= htmlspecialchars($defaultRangeMin) ?>">
                                                            <span class="mx-1 fw-bold">–</span>
                                                            <input type="number" id="defaultRangeMax" name="defaultRangeMax" class="form-control ms-2" placeholder="Max" min="0" style="width: 45%;" value="<?= htmlspecialchars($defaultRangeMax) ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="defaultWeight" class="form-label fw-bold mb-0">Enter Weight (kg)</label>
                                                        <input type="number" id="defaultWeight" name="defaultWeight" class="form-control mt-1" placeholder="Enter weight" value="<?= htmlspecialchars($defaultWeight) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grid for Customer/Supplier -->
                                        <div id="customerSupplierView" style="display: none;">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label fw-bold mb-0">Customer</label>
                                                    <select id="customer" name="customer[]" class="form-select select2" multiple>
                                                        <?php while($rowPF = mysqli_fetch_assoc($customer)){ ?>
                                                            <option value="<?=$rowPF['id'] ?>" <?php if(in_array($rowPF['id'], $customers)) echo 'selected'; ?>><?=$rowPF['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-bold mb-0">Supplier</label>
                                                    <select id="supplier" name="supplier[]" class="form-select select2" multiple>
                                                        <?php while($rowPF = mysqli_fetch_assoc($supplier)){ ?>
                                                            <option value="<?=$rowPF['id'] ?>" <?php if(in_array($rowPF['id'], $suppliers)) echo 'selected'; ?>><?=$rowPF['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="row mt-4">
                                            <div class="col-4 text-center">
                                                <input type="text" class="form-control text-center text-danger fw-bold" value="ESC" readonly>
                                            </div>
                                            <div class="col-4">
                                                <button type="reset" class="btn btn-warning w-100" id="resetZero">Reset to Zero</button>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-success w-100">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- container-fluid -->
                </div><!-- End Page-content -->

                <?php include 'layouts/footer.php'; ?>
            </div><!-- end main content-->
        </div>
        <!-- END layout-wrapper -->

        <div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white"><?=$languageArray['additional_passwords_code'][$language]?></h5>
                    </div>
                    <div class="modal-body">
                        <form id="passwordCheckForm">
                            <div class="mb-3" id="password2Div">
                                <label for="password2" class="form-label"><?=$languageArray['password_code'][$language]?> 2</label>
                                <input type="password" class="form-control" id="password2" name="password2">
                            </div>
                            <div class="mb-3" id="password3Div">
                                <label for="password3" class="form-label"><?=$languageArray['password_code'][$language]?> 3</label>
                                <input type="password" class="form-control" id="password3" name="password3">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success"><?=$languageArray['submit_code'][$language]?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include jQuery library FIRST -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Include jQuery Validate plugin -->
        <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
        
        <?php include 'layouts/customizer.php'; ?>
        <?php include 'layouts/vendor-scripts.php'; ?>

        <!-- swiper js -->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
        <!-- profile init js -->
        <script src="assets/js/pages/profile.init.js"></script>
        <!-- App js -->
        <script src="assets/js/app.js"></script>
        <!-- Select2 -->
        <script src="plugins/select2/js/select2.full.min.js"></script> 

        <script type="text/html" id="autoRowTemplate">
            <tr class="details">
                <td>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="number" class="form-control range-from" name="rangeFrom" placeholder="From (kg)" min="0" step="0.01" required>
                        <span class="text-muted">to</span>
                        <input type="number" class="form-control range-to" name="rangeTo" placeholder="To (kg)" min="0" step="0.01" required>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" name="negativeKg" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control" name="positiveKg" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control" name="negativePerc" min="0" max="100" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control" name="positivePerc" min="0" max="100" step="0.01">
                </td>
                <td class="d-flex" style="text-align:center">
                    <button type="button" class="btn btn-danger" id="remove">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        </script>

        <script type="text/javascript">
            const status = "<?= $status ?>";
            const autoData = <?= json_encode($autoData ? json_decode($autoData, true) : []) ?>;

            $(function () {
                var rowCount = 0;

                // Initialize all Select2 elements in the modal
                $('.select2').select2({
                    placeholder: "Please Select",
                    width: '100%'
                });

                $('.select2-container .select2-selection--multiple').css({
                    'padding-top': '4px',
                    'padding-bottom': '4px',
                    'min-height': 'auto'
                });

                $('.select2-container .select2-selection__arrow').css({
                    'padding-top': '33px',
                    'height': 'auto'
                });

                // Check Password Logic when load screen
                $('#passwordModal').find('#password2Div').val('').show(); // show password2 input
                $('#passwordModal').find('#password3Div').val('').hide(); // hide password3 input
                $("#passwordModal").modal({
                    backdrop: 'static', // disable closing by clicking outside
                    keyboard: false // disable ESC close
                }).on('shown.bs.modal', function () {
                    $(".page-content").hide();
                }).on('hidden.bs.modal', function () {
                    $(".page-content").show();
                });

                // $("#passwordModal").modal("show");

                $("#passwordCheckForm").on("submit", function (e) {
                    e.preventDefault();
                    var password2 = $('#passwordModal').find('#password2').val();

                    $.post("php/checkPasswords.php", { type: 'screen', password2: password2 }, function (data) {
                        let obj = JSON.parse(data);

                        if (obj.status === "success") {
                            $("#passwordModal").modal("hide"); // allow access
                        } else {
                            alert(obj.message);
                            window.location.href = "index.php"; // kick out
                        }
                    });
                });

                // ===== VALIDATION & SAVE WITH PASSWORD3 =====
                $.validator.setDefaults({
                    submitHandler: function () {
                        $('#spinnerLoading').show();

                        // Switch modal to ask for password3
                        // $('#passwordModal').find('#password2Div').val('').hide();
                        // $('#passwordModal').find('#password3Div').val('').show();

                        // $("#passwordModal").modal({
                        //     backdrop: 'static',
                        //     keyboard: false
                        // }).on("shown.bs.modal", function () {
                        //     $(".page-content").addClass("blur");
                        // }).on("hidden.bs.modal", function () {
                        //     $(".page-content").removeClass("blur");
                        // });

                        // $("#passwordModal").modal("show");

                        // // Handle password3 submit
                        // $("#passwordCheckForm").off("submit").on("submit", function (e) {
                        //     e.preventDefault();
                        //     var password3 = $('#password3').val();

                        //     $.post("php/checkPasswords.php", { type: 'save', password3: password3 }, function (data) {
                        //         let obj = JSON.parse(data);

                        //         if (obj.status === "success") {
                        //             $("#passwordModal").modal("hide");

                                    // Proceed with saving form
                                    $.post('php/updateDeduction.php', $('#profileForm').serialize(), function (data) {
                                        var obj = JSON.parse(data);

                                        if (obj.status === 'success') {
                                            // toastr["success"](obj.message, "Success:");
                                            window.location.reload();
                                        } else if (obj.status === 'failed') {
                                            // toastr["error"](obj.message, "Failed:");
                                            alert(obj.message);
                                            $('#spinnerLoading').hide();
                                        } else {
                                            // toastr["error"]("Failed to update ports", "Failed:");
                                            alert("Failed to update ports");
                                            $('#spinnerLoading').hide();
                                        }
                                    });

                        //         } else {
                        //             alert(obj.message);
                        //             window.location.href = "index.php";
                        //         }
                        //     });
                        // });
                    }
                });
                
                $('#profileForm').validate({
                    rules: {
                        text: {
                            required: true
                        }
                    },
                    messages: {
                        text: {
                            required: "Please fill in this field"
                        }
                    },
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

                $('#statusSwitch').on('change', function () {
                    var selectedValue = $(this).val();

                    if (selectedValue == 'Manual') {
                        $('#manualView').show();
                        $('#autoView').hide();
                        $('#autoNewBtn').addClass('d-none');
                        $('#buttonRow').show();
                        $('#defaultView').hide();
                        $('#customerSupplierView').hide();
                    } else if (selectedValue == 'Auto') {
                        $('#manualView').hide();
                        $('#autoView').show();
                        $('#autoNewBtn').removeClass('d-none');
                        $('#buttonRow').show();
                        $('#defaultView').hide();
                        $('#customerSupplierView').hide(); 
                    } else if (selectedValue == 'Default') {
                        $('#manualView').hide();
                        $('#autoView').hide();
                        $('#autoNewBtn').addClass('d-none');
                        $('#buttonRow').hide();
                        $('#defaultView').show();
                        $('#customerSupplierView').hide(); 
                    } else if (selectedValue == 'Customer_Supplier') {
                        $('#manualView').hide();
                        $('#autoView').hide();
                        $('#autoNewBtn').addClass('d-none');
                        $('#buttonRow').hide();
                        $('#defaultView').hide();
                        $('#customerSupplierView').show();   
                    }else {
                        $('#manualView').hide();
                        $('#autoView').hide();
                        $('#autoNewBtn').addClass('d-none');
                        $('#buttonRow').hide();
                        $('#defaultView').hide();
                        $('#customerSupplierView').hide();   
                    }
                });

                $('#resetZero').on('click', function(e) {
                    e.preventDefault();
                    var mode = $('#statusSwitch').val();
                    if (mode == 'Manual') {
                        // reset all F1-F12 inputs to 0
                        $('#manualView input[name^="F"]').val(0);
                    } else if (mode == 'Auto') {
                        // Clear the autoTable body
                        $('#autoTable').empty();
                    } else if (mode == 'Default') {
                        // Clear default range and weight inputs
                        $('#defaultRangeMin').val('');
                        $('#defaultRangeMax').val('');
                        $('#defaultWeight').val('');
                    } else if (mode == 'Customer_Supplier') {
                        // Clear selections
                        $('#customer').val(null).trigger('change');
                        $('#supplier').val(null).trigger('change');
                    }
                });

                // Initialize view based on current status
                $('#statusSwitch').val(status).trigger('change');

                // Load auto data if status is Auto
                if (status == 'Auto') {
                    loadAutoData();
                }

                // Find and remove selected table rows
                $("#autoTable").on('click', 'button[id^="remove"]', function () {
                    $(this).parents("tr").remove();
                });

                // Auto New button functionality
                $('#autoNewBtn').on('click', function() {
                    var template = $("#autoRowTemplate").html();
                    var newRow = $(template);
                    
                    // Set unique IDs for the new row
                    newRow.find('.details:last').attr("id", "detail" + rowCount);
                    newRow.find('.details:last').attr("data-index", rowCount);
                    newRow.find('#remove:last').attr("id", "remove" + rowCount);

                    newRow.find('input[name="rangeFrom"]').attr('id', 'rangeFrom' + rowCount);
                    newRow.find('input[name="rangeTo"]').attr('id', 'rangeTo' + rowCount);
                    newRow.find('input[name="negativeKg"]').attr('id', 'negativeKg' + rowCount);
                    newRow.find('input[name="positiveKg"]').attr('id', 'positiveKg' + rowCount);
                    newRow.find('input[name="negativePerc"]').attr('id', 'negativePerc' + rowCount);
                    newRow.find('input[name="positivePerc"]').attr('id', 'positivePerc' + rowCount);
                    
                    // Set array names for form submission
                    newRow.find('input[name="rangeFrom"]').attr('name', 'rangeFrom[' + rowCount + ']');
                    newRow.find('input[name="rangeTo"]').attr('name', 'rangeTo[' + rowCount + ']');
                    newRow.find('input[name="negativeKg"]').attr('name', 'negativeKg[' + rowCount + ']');
                    newRow.find('input[name="positiveKg"]').attr('name', 'positiveKg[' + rowCount + ']');
                    newRow.find('input[name="negativePerc"]').attr('name', 'negativePerc[' + rowCount + ']');
                    newRow.find('input[name="positivePerc"]').attr('name', 'positivePerc[' + rowCount + ']');
                    
                    $("#autoTable").append(newRow);
                    rowCount++;
                });

                // Validate that "To" value is greater than "From" value
                $("#autoTable").on('blur', '.range-from, .range-to', function() {
                    var row = $(this).closest('tr');
                    var fromValue = parseFloat(row.find('.range-from').val()) || 0;
                    var toValue = parseFloat(row.find('.range-to').val()) || 0;

                    if (fromValue > 0 && toValue > 0 && fromValue >= toValue) {
                        alert('The "To" value must be greater than the "From" value.');
                        $(this).focus();
                    }
                });
            });

            // Function to load existing auto data
            function loadAutoData() {
                if (autoData && autoData.length > 0) {
                    $('#autoTable').empty(); // Clear existing rows
                    rowCount = 0;
                    
                    autoData.forEach(function(item, index) {
                        var template = $("#autoRowTemplate").html();
                        var newRow = $(template);
                        
                        // Set unique IDs for the new row
                        newRow.find('.details:last').attr("id", "detail" + rowCount);
                        newRow.find('.details:last').attr("data-index", rowCount);
                        newRow.find('#remove:last').attr("id", "remove" + rowCount);

                        newRow.find('input[name="rangeFrom"]').attr('id', 'rangeFrom' + rowCount);
                        newRow.find('input[name="rangeTo"]').attr('id', 'rangeTo' + rowCount);
                        newRow.find('input[name="negativeKg"]').attr('id', 'negativeKg' + rowCount);
                        newRow.find('input[name="positiveKg"]').attr('id', 'positiveKg' + rowCount);
                        newRow.find('input[name="negativePerc"]').attr('id', 'negativePerc' + rowCount);
                        newRow.find('input[name="positivePerc"]').attr('id', 'positivePerc' + rowCount);
                        
                        // Set array names for form submission
                        newRow.find('input[name="rangeFrom"]').attr('name', 'rangeFrom[' + rowCount + ']');
                        newRow.find('input[name="rangeTo"]').attr('name', 'rangeTo[' + rowCount + ']');
                        newRow.find('input[name="negativeKg"]').attr('name', 'negativeKg[' + rowCount + ']');
                        newRow.find('input[name="positiveKg"]').attr('name', 'positiveKg[' + rowCount + ']');
                        newRow.find('input[name="negativePerc"]').attr('name', 'negativePerc[' + rowCount + ']');
                        newRow.find('input[name="positivePerc"]').attr('name', 'positivePerc[' + rowCount + ']');
                        
                        // Populate with existing data
                        newRow.find('input[name="rangeFrom[' + rowCount + ']"]').val(item.rangeFrom !== undefined ? item.rangeFrom : '');
                        newRow.find('input[name="rangeTo[' + rowCount + ']"]').val(item.rangeTo !== undefined ? item.rangeTo : '');
                        newRow.find('input[name="negativeKg[' + rowCount + ']"]').val(item.negativeKg !== undefined ? item.negativeKg : '');
                        newRow.find('input[name="positiveKg[' + rowCount + ']"]').val(item.positiveKg !== undefined ? item.positiveKg : '');
                        newRow.find('input[name="negativePerc[' + rowCount + ']"]').val(item.negativePerc !== undefined ? item.negativePerc : '');
                        newRow.find('input[name="positivePerc[' + rowCount + ']"]').val(item.positivePerc !== undefined ? item.positivePerc : '');
                        
                        $("#autoTable").append(newRow);
                        rowCount++;
                    });
                }
            }
        </script>
    </body>
</html>