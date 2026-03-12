<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";

// Check if the user is already logged in, if yes then redirect him to index page
$user = $_SESSION['id'];
$id = '1';
$stmt2 = $link->prepare("SELECT company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, package, fax_no, mpob_no, mpob_expiry_date, mspo_no, mspo_expiry_date, include_price, include_container, include_display_setup, include_grading, epf from Company where id = ?");
mysqli_stmt_bind_param($stmt2, "s", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $company_reg_no, $name, $address_line_1, $address_line_2, $address_line_3, $phone_no, $package, $fax_no, $mpob_no, $mpob_expiry_date, $mspo_no, $mspo_expiry_date, $include_price, $include_container, $include_display_setup, $include_grading, $epf);
if (mysqli_stmt_fetch($stmt2)) {
    $usercompany_reg_no = $company_reg_no;
    $username = $name;
    $useraddress_line_1 = $address_line_1;
    $useraddress_line_2 = $address_line_2;
    $useraddress_line_3 = $address_line_3;
    $userphone_no = $phone_no;
    $userpackage = $package;
    $userfax_no = $fax_no;
    $mpobNo = $mpob_no;
    $mpobExpiry = $mpob_expiry_date;
    $mspoNo = $mspo_no;
    $mspoExpiry = $mspo_expiry_date;
    $includePrice = $include_price;
    $includeContainer = $include_container;
    $includeDisplaySetup = $include_display_setup;
    $includeGrading = $include_grading;
    $epf = $epf;
}

$role = 'NORMAL';
if ($user != null && $user != ''){
    $stmt3 = $link->prepare("SELECT * from Users WHERE id = ?");
    $stmt3->bind_param('s', $user);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
        
    if(($row3 = $result3->fetch_assoc()) !== null){
        $role = $row3['role'];
    }
}

$readonly = '';
$hidden = false;
if ($role != 'SADMIN' && $role != 'AUTHORITY'){
    $readonly = 'readonly';
    $hidden = true;
}
?>

    <head>
        
        <title>Company Profile | PWS - Weighing System</title>
        <?php include 'layouts/title-meta.php'; ?>

        <!-- swiper css -->
        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">

        <?php include 'layouts/head-css.php'; ?>
        
        <!-- Include jQuery library -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Include jQuery Validate plugin -->
        <script src="plugins/jquery-validation/jquery.validate.min.js"></script>

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
                            <div class="card">
                                <div class="card-body">
                                    <form action="php/updateCompany.php" method="post">
                                        <div class="row">
                                            <div class="col-12 mb-3 mt-4">
                                                <h5 class="text-primary">Company Settings</h5>
                                                <hr>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label for="companyRegNo" class="col-form-label">Company Reg No. *</label>
                                                        <input type="text" class="form-control input-readonly" id="companyRegNo" name="companyRegNo" placeholder="Company Reg No" value="<?=$usercompany_reg_no ?>" required <?= $readonly ?>>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="companyName" class="col-form-label">Company Name *</label>
                                                        <input type="text" class="form-control input-readonly" id="companyName" name="companyName" placeholder="Company Name" value="<?=$username ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label for="companyPhone" class="col-form-label">Company Phone</label>
                                                        <input type="text" class="form-control input-readonly" id="companyPhone" name="companyPhone" placeholder="Company Phone" value="<?=$userphone_no ?>" required <?= $readonly ?>>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="companyFax" class="col-form-label">Fax No.</label>
                                                        <input type="text" class="form-control input-readonly" id="companyFax" name="companyFax" placeholder="Company Fax" value="<?=$userfax_no ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="companyAddress" class="col-sm-4 col-form-label">Company Address 1 *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress" name="companyAddress" placeholder="Company Address 1" value="<?=$useraddress_line_1 ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="companyAddress2" class="col-sm-4 col-form-label">Company Address 2</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress2" name="companyAddress2" placeholder="Company Address 2" value="<?=$useraddress_line_2 ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="companyAddress3" class="col-sm-4 col-form-label">Company Address 3</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress3" name="companyAddress3" placeholder="Company Address 3" value="<?=$useraddress_line_3 ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="mpobNo" class="col-sm-4 col-form-label">MPOB License No</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control input-readonly" id="mpobNo" name="mpobNo" placeholder="MPOB License No" value="<?=$mpob_no ?>" <?= $readonly ?>>
                                                    </div>
                                                    <label for="mpobExpiry" class="col-sm-2 col-form-label">Expiry Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="date" class="form-control" data-provider="flatpickr" id="mpobExpiry" name="mpobExpiry" value="" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="mspoNo" class="col-sm-4 col-form-label">MSPO Cert No</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control input-readonly" id="mspoNo" name="mspoNo" placeholder="MSPO Cert No" value="<?=$mspo_no ?>" <?= $readonly ?>>
                                                    </div>
                                                    <label for="mspoExpiry" class="col-sm-2 col-form-label">Expiry Date</label>
                                                    <div class="col-sm-2">
                                                        <input type="date" class="form-control" data-provider="flatpickr" id="mspoExpiry" name="mspoExpiry" value="" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="companyPackage" class="col-sm-4 col-form-label">Package</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-select input-readonly" id="companyPackage" name="companyPackage" <?= $readonly ?>>
                                                            <option value="">-- Select Package --</option>
                                                            <option value="Lite" <?= $userpackage == 'Lite' ? 'selected' : '' ?>>Lite</option>
                                                            <option value="Standard" <?= $userpackage == 'Standard' ? 'selected' : '' ?>>Standard</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3" <?= $hidden ? 'style="display:none;"' : '' ?>>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="col-form-label">Include Price</label>
                                                        <div>
                                                            <input type="radio" class="form-check-input" id="includePriceYes" name="includePrice" value="Y" <?= $includePrice == 'Y' ? 'checked' : '' ?> <?= $readonly ?>> Yes
                                                            <input type="radio" class="form-check-input ms-3" id="includePriceNo" name="includePrice" value="N" <?= $includePrice == 'N' ? 'checked' : '' ?> <?= $readonly ?>> No
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="col-form-label">Include Container</label>
                                                        <div>
                                                            <input type="radio" class="form-check-input" id="includeContainerYes" name="includeContainer" value="Y" <?= $includeContainer == 'Y' ? 'checked' : '' ?> <?= $readonly ?>> Yes
                                                            <input type="radio" class="form-check-input ms-3" id="includeContainerNo" name="includeContainer" value="N" <?= $includeContainer == 'N' ? 'checked' : '' ?> <?= $readonly ?>> No
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="col-form-label">Include Display Setup</label>
                                                        <div>
                                                            <input type="radio" class="form-check-input" id="includeDisplaySetupYes" name="includeDisplaySetup" value="Y" <?= $includeDisplaySetup == 'Y' ? 'checked' : '' ?> <?= $readonly ?>> Yes
                                                            <input type="radio" class="form-check-input ms-3" id="includeDisplaySetupNo" name="includeDisplaySetup" value="N" <?= $includeDisplaySetup == 'N' ? 'checked' : '' ?> <?= $readonly ?>> No
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="col-form-label">Include Grading</label>
                                                        <div>
                                                            <input type="radio" class="form-check-input" id="includeGradingYes" name="includeGrading" value="Y" <?= $includeGrading == 'Y' ? 'checked' : '' ?> <?= $readonly ?>> Yes
                                                            <input type="radio" class="form-check-input ms-3" id="includeGradingNo" name="includeGrading" value="N" <?= $includeGrading == 'N' ? 'checked' : '' ?> <?= $readonly ?>> No
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!--div class="col-12 mb-3 mt-4">
                                                <h5 class="text-primary">Payroll Settings</h5>
                                                <hr>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="employeeEpf" class="col-sm-4 col-form-label">Employee EPF (%)</label>
                                                    <div class="col-sm-8">
                                                        <input type="number" step="0.01" class="form-control" id="employeeEpf" name="employeeEpf" placeholder="11" value="<?=$epf ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div-->
                                            <div class="mt-4" <?= $hidden ? 'style="display:none;"' : '' ?>>
                                                <button class="btn btn-success w-100" type="submit">Update</button>
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

        

        <?php include 'layouts/customizer.php'; ?>

        <?php include 'layouts/vendor-scripts.php'; ?>

        <!-- swiper js -->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

        <!-- profile init js -->
        <script src="assets/js/pages/profile.init.js"></script>
        
        <!-- App js -->
        <script src="assets/js/app.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                //Date picker
                $('#mpobExpiry').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: "<?= $mpobExpiry ? date('d-m-Y', strtotime($mpobExpiry)) : '' ?>"
                });

                $('#mspoExpiry').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: "<?= $mspoExpiry ? date('d-m-Y', strtotime($mspoExpiry)) : '' ?>"
                });
            });
        </script>
    </body>
</html>