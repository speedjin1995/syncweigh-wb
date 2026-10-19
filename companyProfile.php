<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
if (!hasModulePermission('Settings', 'Company Profile', ['view'])){
    header('Location: no-permission.php');
    exit;
}
?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";

// Check if the user is already logged in, if yes then redirect him to index page
$user = $_SESSION['id'];
$id = '1';
$stmt2 = $link->prepare("SELECT company_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no from Company where id = ?");
mysqli_stmt_bind_param($stmt2, "s", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $company_reg_no, $name, $address_line_1, $address_line_2, $address_line_3, $phone_no, $fax_no);

if (mysqli_stmt_fetch($stmt2)) {
    $usercompany_reg_no = $company_reg_no;
    $username = $name;
    $useraddress_line_1 = $address_line_1;
    $useraddress_line_2 = $address_line_2;
    $useraddress_line_3 = $address_line_3;
    $userphone_no = $phone_no;
    $userfax_no = $fax_no;
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
if ($role != 'SADMIN'){
    $readonly = 'readonly';
    $hidden = true;
}

$features = [];
if ($role == 'SADMIN'){
    require_once "php/classes/BaseController.php";
    $baseController = new BaseController($db);
    $features = $baseController->getCompanyFeatures();
}

?>

    <head>
        
        <title>Company Profile | Synctronix - Weighing System</title>
        <?php include 'layouts/title-meta.php'; ?>

        <!-- swiper css -->
        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">

        <!-- Include jQuery library -->
        <script src="plugins/jquery/jquery.min.js"></script>

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
                        <div class="row col-12 px-4">
                            <h5 class="mb-3 mt-2 px-0 text-decoration-underline">Company Information</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <form action="php/updateCompany.php" method="post">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyRegNo" class="col-sm-4 col-form-label"><?=$languageArray['company_reg_no_code'][$language]?> *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyRegNo" name="companyRegNo" placeholder="<?=$languageArray['search_code'][$language]?>Company Reg No" value="<?=$usercompany_reg_no ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyName" class="col-sm-4 col-form-label"><?=$languageArray['company_name_code'][$language]?> *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyName" name="companyName" placeholder="Company Name" value="<?=$username ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyAddress" class="col-sm-4 col-form-label"><?=$languageArray['company_address_code'][$language]?> 1 *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress" name="companyAddress" placeholder="<?=$languageArray['company_address_code'][$language]?> 1" value="<?=$useraddress_line_1 ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyAddress2" class="col-sm-4 col-form-label"><?=$languageArray['company_address_code'][$language]?> 2</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress2" name="companyAddress2" placeholder="<?=$languageArray['company_address_code'][$language]?> 2" value="<?=$useraddress_line_2 ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyAddress3" class="col-sm-4 col-form-label"><?=$languageArray['company_address_code'][$language]?> 3</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyAddress3" name="companyAddress3" placeholder="<?=$languageArray['company_address_code'][$language]?> 3" value="<?=$useraddress_line_3 ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyPhone" class="col-sm-4 col-form-label"><?=$languageArray['company_phone_code'][$language]?></label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyPhone" name="companyPhone" placeholder="<?=$languageArray['company_phone_code'][$language]?>" value="<?=$userphone_no ?>" required <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="companyFax" class="col-sm-4 col-form-label"><?=$languageArray['company_fax_code'][$language]?></label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control input-readonly" id="companyFax" name="companyFax" placeholder="<?=$languageArray['company_fax_code'][$language]?>" value="<?=$userfax_no ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4" <?= $hidden ? 'style="display:none;"' : '' ?>>
                                                <button class="btn btn-success w-100" type="submit"><?=$languageArray['update_code'][$language]?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if ($role == 'SADMIN'){ ?>
                        <div class="row col-12 px-4">
                            <h5 class="mb-3 mt-2 px-0 text-decoration-underline">Features</h5>
                            <div class="card bg-white">
                                <div class="card-body">
                                    <?php if (empty($features)){ ?>
                                        <p class="text-muted mb-0">No features found.</p>
                                    <?php } ?>
                                    <div class="row">
                                    <?php foreach ($features as $feature){ ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="row">
                                            <label for="feature_<?=htmlspecialchars($feature['feature_code']) ?>" class="col-sm-8 col-form-label">
                                                <?=htmlspecialchars($feature['feature_name']) ?>
                                                <small class="d-block text-muted"><?=htmlspecialchars($feature['description'] ?? '') ?></small>
                                            </label>
                                            <div class="col-sm-4 d-flex align-items-center">
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input feature-toggle" type="checkbox" role="switch" id="feature_<?=htmlspecialchars($feature['feature_code']) ?>" data-code="<?=htmlspecialchars($feature['feature_code']) ?>" <?= $feature['enabled'] ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                        <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                        <?php } ?>
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

        <script>
        $(function () {
            $('.feature-toggle').on('change', function () {
                var toggle = $(this);
                var enabled = toggle.is(':checked');

                $('#spinnerLoading').show();
                $.post('php/modules/features/index.php', {action: 'save', featureCode: toggle.data('code'), enabled: enabled ? '1' : '0'}, function (data) {
                    var obj = JSON.parse(data);

                    if (obj.status === 'success') {
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else {
                        toggle.prop('checked', !enabled);
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                }).fail(function () {
                    toggle.prop('checked', !enabled);
                    $("#failBtn").attr('data-toast-text', 'Something wrong when saving!');
                    $("#failBtn").click();
                }).always(function () {
                    $('#spinnerLoading').hide();
                });
            });
        });
        </script>
    </body>
</html>