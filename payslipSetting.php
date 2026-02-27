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

$socso = [];
$eis = [];
$tax = [];
$nonResidentPcbRate = null;
$individualReliefFund = null;
$individualEpfReliefFund = null;
$stmt2 = $link->prepare("SELECT name, value FROM miscellaneous WHERE name IN ('socso', 'eis', 'tax', 'non_res_epf', 'ind_relief', 'ind_epf_relief')");
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    if($row['name'] == 'socso') {
        $socso = $row['value'];
    } else if($row['name'] == 'eis') {
        $eis = $row['value'];
    } else if($row['name'] == 'tax') {
        $tax = $row['value'];
    } else if($row['name'] == 'non_res_epf') {
        $nonResidentPcbRate = $row['value'];
    } else if($row['name'] == 'ind_relief') {
        $individualReliefFund = $row['value'];
    } else if($row['name'] == 'ind_epf_relief') {
        $individualEpfReliefFund = $row['value'];
    }
}//var_dump($socso, $eis, $tax, $nonResidentPcbRate, $individualReliefFund, $individualEpfReliefFund);exit;
$stmt2->close();

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
        
        <title>Payslip Setting | PWS - Weighing System</title>
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
                                    <form action="php/updatePayslipSetting.php" method="post">
                                        <div class="row">
                                            <div class="col-12 mb-3 mt-4">
                                                <h5 class="text-primary">Payroll Settings</h5>
                                                <hr>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="nonResidentPcbRate" class="col-sm-4 col-form-label">Non-Resident PCB Rate (%)</label>
                                                    <div class="col-sm-8">
                                                        <input type="number" step="0.01" class="form-control" id="nonResidentPcbRate" name="nonResidentPcbRate" placeholder="30" value="<?=$nonResidentPcbRate ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="individualReliefFund" class="col-sm-4 col-form-label">Individual Relief Fund (RM)</label>
                                                    <div class="col-sm-8">
                                                        <input type="number" step="0.01" class="form-control" id="individualReliefFund" name="individualReliefFund" placeholder="0" value="<?=$individualReliefFund ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="individualEpfReliefFund" class="col-sm-4 col-form-label">Individual EPF Relief Fund (RM)</label>
                                                    <div class="col-sm-8">
                                                        <input type="number" step="0.01" class="form-control" id="individualEpfReliefFund" name="individualEpfReliefFund" placeholder="0" value="<?=$individualEpfReliefFund ?>" <?= $readonly ?>>
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
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="event.target.tagName !== 'BUTTON' && document.getElementById('pcbContent').classList.toggle('d-none')">
                                                        <h6 class="mb-0 text-white">PCB Rate Table</h6>
                                                        <button type="button" class="btn btn-sm btn-success" id="addPcbRow" <?= $readonly ?>>Add New</button>
                                                    </div>
                                                    <div class="card-body d-none" id="pcbContent">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>P Min (RM) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="Chargeable income range minimum (annual)"></i></th>
                                                                    <th>P Max (RM) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="Chargeable income range maximum (annual)"></i></th>
                                                                    <th>M (RM) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="First chargeable income for the range"></i></th>
                                                                    <th>R (%) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="Tax rate percentage applied on income exceeding M"></i></th>
                                                                    <th>B Cat 1 & 3 (RM) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="Tax on M after rebate — Category 1 (Single) & Category 3 (Married, spouse working / widowed / divorced)"></i></th>
                                                                    <th>B Cat 2 (RM) <i class="fa fa-info-circle text-muted" data-bs-toggle="tooltip" title="Tax on M after rebate — Category 2 (Married, spouse not working)"></i></th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="pcbTable"></tbody>
                                                        </table>
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
                    <input id="socsoEmployer" name="socsoEmployer" type="number" step="0.01" class="form-control socso-employer" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="socsoEmployee" name="socsoEmployee" type="number" step="0.01" class="form-control socso-employee" placeholder="0" <?= $readonly ?>>
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
                    <input id="eisEmployer" name="eisEmployer" type="number" step="0.01" class="form-control eis-employer" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="eisEmployee" name="eisEmployee" type="number" step="0.01" class="form-control eis-employee" placeholder="0" <?= $readonly ?>>
                </td>
                <td class="d-flex" style="text-align:center">
                    <button class="btn btn-sm btn-danger" id="remove" style="background-color: #f06548;">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        </script>

        <script type="text/html" id="pcbDetail">
            <tr class="details">
                <td>
                    <input type="hidden" id="pcbNo" name="pcbNo">
                    <input id="pcbMin" name="pcbMin" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="pcbMax" name="pcbMax" type="number" step="0.01" class="form-control" placeholder="20000" <?= $readonly ?>>
                </td>
                <td>
                    <input id="pcbM" name="pcbM" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="pcbR" name="pcbR" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="pcbB1" name="pcbB1" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
                </td>
                <td>
                    <input id="pcbB2" name="pcbB2" type="number" step="0.01" class="form-control" placeholder="0" <?= $readonly ?>>
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
            var pcbRowCount = 0;
            $(document).ready(function() {
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

                // PCB Rate Table Section
                $("#pcbTable").on('click', 'button[id^="remove"]', function () {
                    $(this).parents("tr").remove();
                    pcbRowCount--;
                });

                $("#addPcbRow").click(function(){
                    addPcbRow();
                });

                <?php if($tax && $tax != ''): ?>
                var pcbData = <?= $tax ?>;
                if(pcbData && pcbData.length > 0) {
                    pcbData.forEach(function(item) {
                        addPcbRow(item.no, item.min, item.max, item.m, item.r, item.b1, item.b2);
                    });
                }
                <?php endif; ?>
                // End of PCB Rate Table Section
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

            function addPcbRow(no = null, min = '', max = '', m = '', r = '', b1 = '', b2 = '') {
                var $addContents = $("#pcbDetail").clone();
                $("#pcbTable").append($addContents.html());

                $("#pcbTable").find('.details:last').attr("id", "detail" + pcbRowCount);
                $("#pcbTable").find('.details:last').attr("data-index", pcbRowCount);
                $("#pcbTable").find('#remove:last').attr("id", "remove" + pcbRowCount);

                $("#pcbTable").find('#pcbNo:last').attr('name', 'pcbNo['+pcbRowCount+']').attr("id", "pcbNo" + pcbRowCount).val(no || (pcbRowCount + 1));
                $("#pcbTable").find('#pcbMin:last').attr('name', 'pcbMin['+pcbRowCount+']').attr("id", "pcbMin" + pcbRowCount).val(min);
                $("#pcbTable").find('#pcbMax:last').attr('name', 'pcbMax['+pcbRowCount+']').attr("id", "pcbMax" + pcbRowCount).val(max);
                $("#pcbTable").find('#pcbM:last').attr('name', 'pcbM['+pcbRowCount+']').attr("id", "pcbM" + pcbRowCount).val(m);
                $("#pcbTable").find('#pcbR:last').attr('name', 'pcbR['+pcbRowCount+']').attr("id", "pcbR" + pcbRowCount).val(r);
                $("#pcbTable").find('#pcbB1:last').attr('name', 'pcbB1['+pcbRowCount+']').attr("id", "pcbB1" + pcbRowCount).val(b1);
                $("#pcbTable").find('#pcbB2:last').attr('name', 'pcbB2['+pcbRowCount+']').attr("id", "pcbB2" + pcbRowCount).val(b2);

                pcbRowCount++;
            }
        </script>
    </body>
</html>