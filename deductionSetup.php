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
}

// Cleanup
$stmt->close();
$result->free();
?>


    <head>
        <title><?=$languageArray['deduction_setup_code'][$language]?> | Synctronix - Weighing System</title>
        <?php include 'layouts/title-meta.php'; ?>

        <!-- swiper css -->
        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">

        <?php include 'layouts/head-css.php'; ?>

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
                                        <!-- Status -->
                                        <div class="row mb-3">
                                            <div class="col-4 d-flex align-items-center">
                                                <span class="me-2 fw-bold">Disable</span>
                                                <div class="form-check form-switch">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        id="statusSwitch" 
                                                        name="status" 
                                                        value="Enable" 
                                                        <?= ($status === 'Enable' ? 'checked' : '') ?>>
                                                </div>
                                                <span class="ms-2 fw-bold">Enable</span>
                                            </div>
                                        </div>

                                        <!-- Grid for F1-F12 -->
                                        <div class="row">
                                            <div class="col-3">
                                                <label>( - ) kg</label>
                                                <input type="number" class="form-control mb-2" name="F1" placeholder="F1" min="0" value="<?= htmlspecialchars($F1) ?>">
                                                <input type="number" class="form-control mb-2" name="F2" placeholder="F2" min="0" value="<?= htmlspecialchars($F2) ?>">
                                                <input type="number" class="form-control mb-2" name="F3" placeholder="F3" min="0" value="<?= htmlspecialchars($F3) ?>">
                                            </div>
                                            <div class="col-3">
                                                <label>( + ) kg</label>
                                                <input type="number" class="form-control mb-2" name="F4" placeholder="F4" min="0" value="<?= htmlspecialchars($F4) ?>">
                                                <input type="number" class="form-control mb-2" name="F5" placeholder="F5" min="0" value="<?= htmlspecialchars($F5) ?>">
                                                <input type="number" class="form-control mb-2" name="F6" placeholder="F6" min="0" value="<?= htmlspecialchars($F6) ?>">
                                            </div>
                                            <div class="col-3">
                                                <label>( - ) %</label>
                                                <input type="number" class="form-control mb-2" name="F7" placeholder="F7" min="0" max="100" value="<?= htmlspecialchars($F7) ?>">
                                                <input type="number" class="form-control mb-2" name="F8" placeholder="F8" min="0" max="100" value="<?= htmlspecialchars($F8) ?>">
                                                <input type="number" class="form-control mb-2" name="F9" placeholder="F9" min="0" max="100" value="<?= htmlspecialchars($F9) ?>">
                                            </div>
                                            <div class="col-3">
                                                <label>( + ) %</label>
                                                <input type="number" class="form-control mb-2" name="F10" placeholder="F10" min="0" max="100" value="<?= htmlspecialchars($F10) ?>">
                                                <input type="number" class="form-control mb-2" name="F11" placeholder="F11" min="0" max="100" value="<?= htmlspecialchars($F11) ?>">
                                                <input type="number" class="form-control mb-2" name="F12" placeholder="F12" min="0" max="100" value="<?= htmlspecialchars($F12) ?>">
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="row mt-4">
                                            <div class="col-4 text-center">
                                                <input type="text" class="form-control text-center text-danger fw-bold" value="ESC" readonly>
                                            </div>
                                            <div class="col-4">
                                                <button type="reset" class="btn btn-warning w-100">Reset to Zero</button>
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

        <?php include 'layouts/customizer.php'; ?>
        <?php include 'layouts/vendor-scripts.php'; ?>

        <!-- swiper js -->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
        <!-- profile init js -->
        <script src="assets/js/pages/profile.init.js"></script>
        <!-- App js -->
        <script src="assets/js/app.js"></script>
        <!-- Include jQuery library -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Include jQuery Validate plugin -->
        <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
        <script type="text/javascript">
            $(function () {
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

                $("#passwordModal").modal("show");

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

                const switchInput = document.getElementById('statusSwitch');

                // Default: left side = Disable
                switchInput.value = "Disable";

                switchInput.addEventListener('change', function () {
                    this.value = this.checked ? "Enable" : "Disable";
                });

                // ===== VALIDATION & SAVE WITH PASSWORD3 =====
                $.validator.setDefaults({
                    submitHandler: function () {
                        $('#spinnerLoading').show();

                        // Switch modal to ask for password3
                        $('#passwordModal').find('#password2Div').val('').hide();
                        $('#passwordModal').find('#password3Div').val('').show();

                        $("#passwordModal").modal({
                            backdrop: 'static',
                            keyboard: false
                        }).on("shown.bs.modal", function () {
                            $(".page-content").addClass("blur");
                        }).on("hidden.bs.modal", function () {
                            $(".page-content").removeClass("blur");
                        });

                        $("#passwordModal").modal("show");

                        // Handle password3 submit
                        $("#passwordCheckForm").off("submit").on("submit", function (e) {
                            e.preventDefault();
                            var password3 = $('#password3').val();

                            $.post("php/checkPasswords.php", { type: 'save', password3: password3 }, function (data) {
                                let obj = JSON.parse(data);

                                if (obj.status === "success") {
                                    $("#passwordModal").modal("hide");

                                    // Proceed with saving form
                                    $.post('php/updateDeduction.php', $('#profileForm').serialize(), function (data) {
                                        var obj = JSON.parse(data);

                                        if (obj.status === 'success') {
                                            toastr["success"](obj.message, "Success:");
                                            window.location.reload();
                                        } else if (obj.status === 'failed') {
                                            toastr["error"](obj.message, "Failed:");
                                            $('#spinnerLoading').hide();
                                        } else {
                                            toastr["error"]("Failed to update ports", "Failed:");
                                            $('#spinnerLoading').hide();
                                        }
                                    });

                                } else {
                                    alert(obj.message);
                                    window.location.href = "index.php";
                                }
                            });
                        });
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

                $('#resetZero').on('click', function () {
                    // reset all text/number inputs inside the form to 0
                    $('#profileForm').find('input[type="text"], input[type="number"]').each(function () {
                        $(this).val(0);
                    });
                });
            });
        </script>
    </body>
</html>