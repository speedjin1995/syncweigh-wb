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
$stmt2 = $link->prepare("SELECT company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, package, fax_no, mpob_no, mpob_expiry_date, mspo_no, mspo_expiry_date, include_price, include_container, include_display_setup, include_grading, epf, socso, eis, tax from Company where id = ?");
mysqli_stmt_bind_param($stmt2, "s", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $company_reg_no, $name, $address_line_1, $address_line_2, $address_line_3, $phone_no, $package, $fax_no, $mpob_no, $mpob_expiry_date, $mspo_no, $mspo_expiry_date, $include_price, $include_container, $include_display_setup, $include_grading, $epf, $socso, $eis, $tax);
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
    $socso = $socso;
    $eis = $eis;
    $tax = $tax;
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
                                            
                                            <div class="col-12 mb-3 mt-4">
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
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="event.target.tagName !== 'BUTTON' && document.getElementById('socsoContent').classList.toggle('d-none')">
                                                        <h6 class="mb-0 text-white">SOCSO Contribution Table</h6>
                                                        <button type="button" class="btn btn-sm btn-success" id="addSocsoRow" <?= $readonly ?>>Add New</button>
                                                    </div>
                                                    <div class="card-body d-none" id="socsoContent">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Min Salary (RM)</th>
                                                                    <th>Max Salary (RM)</th>
                                                                    <th>Employer (%)</th>
                                                                    <th>Employee (%)</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="socsoTable"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="event.target.tagName !== 'BUTTON' && document.getElementById('eisContent').classList.toggle('d-none')">
                                                        <h6 class="mb-0 text-white">EIS Contribution Table</h6>
                                                        <button type="button" class="btn btn-sm btn-success" id="addEisRow" <?= $readonly ?>>Add New</button>
                                                    </div>
                                                    <div class="card-body d-none" id="eisContent">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Min Salary (RM)</th>
                                                                    <th>Max Salary (RM)</th>
                                                                    <th>Employer (RM)</th>
                                                                    <th>Employee (RM)</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="eisTable"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="tax" class="col-sm-4 col-form-label">Tax (%)</label>
                                                    <div class="col-sm-8">
                                                        <input type="number" step="0.01" class="form-control" id="tax" name="tax" placeholder="0.2" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
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

        <script type="text/html" id="socsoDetail">
            <tr class="details">
                <td>
                    <input type="hidden" id="socsoNo" name="socsoNo">
                    <input id="socsoMin" name="socsoMin" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="socsoMax" name="socsoMax" type="number" step="0.01" class="form-control" placeholder="1000" <?= $readonly ?>>
                </td>
                <td>
                    <input id="socsoEmployer" name="socsoEmployer" type="number" step="0.01" class="form-control socso-employer" placeholder="0" min="0" max="100" <?= $readonly ?>>
                </td>
                <td>
                    <input id="socsoEmployee" name="socsoEmployee" type="number" step="0.01" class="form-control socso-employee" placeholder="0" min="0" max="100" <?= $readonly ?>>
                </td>
                <td class="d-flex" style="text-align:center">
                    <button class="btn btn-sm btn-danger" id="remove" style="background-color: #f06548;">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        </script>

        <script type="text/html" id="eisDetail">
            <tr class="details">
                <td>
                    <input type="hidden" id="eisNo" name="eisNo">
                    <input id="eisMin" name="eisMin" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="eisMax" name="eisMax" type="number" step="0.01" class="form-control" placeholder="1000" <?= $readonly ?>>
                </td>
                <td>
                    <input id="eisEmployer" name="eisEmployer" type="number" step="0.01" class="form-control eis-employer" placeholder="0" min="0" max="100" <?= $readonly ?>>
                </td>
                <td>
                    <input id="eisEmployee" name="eisEmployee" type="number" step="0.01" class="form-control eis-employee" placeholder="0" min="0" max="100" <?= $readonly ?>>
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

        <!-- swiper js -->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

        <!-- profile init js -->
        <script src="assets/js/pages/profile.init.js"></script>
        
        <!-- App js -->
        <script src="assets/js/app.js"></script>

        <script type="text/javascript">
            var socsoRowCount = 0;
            var eisRowCount = 0;
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

                // SOCSO Contribution Table Section
                // Remove socso row
                $("#socsoTable").on('click', 'button[id^="remove"]', function () {
                    $(this).parents("tr").remove();

                    socsoRowCount--;
                });

                // Add new socso row
                $("#addSocsoRow").click(function(){
                    addSocsoRow();
                });

                <?php if($socso && $socso != ''): ?>
                var socsoData = <?= $socso ?>;
                if(socsoData && socsoData.length > 0) {
                    socsoData.forEach(function(item) {
                        addSocsoRow(item.no, item.min, item.max, item.employer, item.employee);
                    });
                }
                <?php endif; ?>
                // End of SOCSO Contribution Table Section

                // EIS Contribution Table Section
                // Remove eis row
                $("#eisTable").on('click', 'button[id^="remove"]', function () {
                    $(this).parents("tr").remove();

                    eisRowCount--;
                });

                // Add new eis row
                $("#addEisRow").click(function(){
                    addEisRow();
                });

                <?php if($eis && $eis != ''): ?>
                var eisData = <?= $eis ?>;
                if(eisData && eisData.length > 0) {
                    eisData.forEach(function(item) {
                        addEisRow(item.no, item.min, item.max, item.employer, item.employee);
                    });
                }
                <?php endif; ?>
                // End of EIS Contribution Table Section
            });

            function addSocsoRow(no = null, min = '', max = '', employer = '', employee = '') {
                var $addContents = $("#socsoDetail").clone();
                $("#socsoTable").append($addContents.html());

                $("#socsoTable").find('.details:last').attr("id", "detail" + socsoRowCount);
                $("#socsoTable").find('.details:last').attr("data-index", socsoRowCount);
                $("#socsoTable").find('#remove:last').attr("id", "remove" + socsoRowCount);

                $("#socsoTable").find('#socsoNo:last').attr('name', 'socsoNo['+socsoRowCount+']').attr("id", "socsoNo" + socsoRowCount).val(no || (socsoRowCount + 1));
                $("#socsoTable").find('#socsoMin:last').attr('name', 'socsoMin['+socsoRowCount+']').attr("id", "socsoMin" + socsoRowCount).val(min);
                $("#socsoTable").find('#socsoMax:last').attr('name', 'socsoMax['+socsoRowCount+']').attr("id", "socsoMax" + socsoRowCount).val(max);
                $("#socsoTable").find('#socsoEmployer:last').attr('name', 'socsoEmployer['+socsoRowCount+']').attr("id", "socsoEmployer" + socsoRowCount).val(employer);
                $("#socsoTable").find('#socsoEmployee:last').attr('name', 'socsoEmployee['+socsoRowCount+']').attr("id", "socsoEmployee" + socsoRowCount).val(employee);

                socsoRowCount++;
            }

            function addEisRow(no = null, min = '', max = '', employer = '', employee = '') {
                var $addContents = $("#eisDetail").clone();
                $("#eisTable").append($addContents.html());

                $("#eisTable").find('.details:last').attr("id", "detail" + eisRowCount);
                $("#eisTable").find('.details:last').attr("data-index", eisRowCount);
                $("#eisTable").find('#remove:last').attr("id", "remove" + eisRowCount);

                $("#eisTable").find('#eisNo:last').attr('name', 'eisNo['+eisRowCount+']').attr("id", "eisNo" + eisRowCount).val(no || (eisRowCount + 1));
                $("#eisTable").find('#eisMin:last').attr('name', 'eisMin['+eisRowCount+']').attr("id", "eisMin" + eisRowCount).val(min);
                $("#eisTable").find('#eisMax:last').attr('name', 'eisMax['+eisRowCount+']').attr("id", "eisMax" + eisRowCount).val(max);
                $("#eisTable").find('#eisEmployer:last').attr('name', 'eisEmployer['+eisRowCount+']').attr("id", "eisEmployer" + eisRowCount).val(employer);
                $("#eisTable").find('#eisEmployee:last').attr('name', 'eisEmployee['+eisRowCount+']').attr("id", "eisEmployee" + eisRowCount).val(employee);

                eisRowCount++;
            }
        </script>
    </body>
</html>