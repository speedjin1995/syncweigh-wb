<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";

// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
$stmt2 = $link->prepare("SELECT username, useremail, languages from Users where id = ?");
mysqli_stmt_bind_param($stmt2, "s", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $name, $email, $languages);

if (mysqli_stmt_fetch($stmt2)) {
    $useremail = $email;
    $username = $name;
    $language = $languages;
}
?>

    <head>

        <title>My Profile | Synctronix - Weighing System</title>
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
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><?=$languageArray['profile_code'][$language]?></h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="profileForm" action="php/modules/settings/api.php" method="post">
                                            <input type="hidden" name="action" value="updateProfile">
                                            <div class="mb-3">
                                                <label for="email" class="form-label"><?=$languageArray['email_code'][$language]?></label>
                                                <input type="email" class="form-control" id="email" name="userEmail" placeholder="<?=$languageArray['email_code'][$language]?>" value="<?=$useremail ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="username" class="form-label"><?=$languageArray['username_code'][$language]?></label>
                                                <input type="text" class="form-control" id="username" name="userName" placeholder="<?=$languageArray['username_code'][$language]?>" value="<?=$username ?>" readonly>
                                            </div>
                                            <div class="mb-4">
                                                <label for="language" class="form-label"><?=$languageArray['language_code'][$language]?></label>
                                                <select class="form-control" style="width: 100%;" id="language" name="language" required>
                                                    <option value="en" <?= ($language == 'en') ? 'selected' : '' ?>>English</option>
                                                    <option value="zh" <?= ($language == 'zh') ? 'selected' : '' ?>>Chinese</option>
                                                    <option value="my" <?= ($language == 'my') ? 'selected' : '' ?>>Bahasa Malaysia</option>
                                                    <option value="ne" <?= ($language == 'ne') ? 'selected' : '' ?>>नेपाली</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-success w-100" type="submit"><?=$languageArray['submit_code'][$language]?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><?=$languageArray['change_password_code'][$language]?></h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="passwordForm" action="php/modules/settings/api.php" method="post">
                                            <input type="hidden" name="action" value="changePassword">
                                            <div class="mb-3">
                                                <label for="oldPassword" class="form-label"><?=$languageArray['old_password_code'][$language]?></label>
                                                <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="<?=$languageArray['old_password_code'][$language]?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="newPassword" class="form-label"><?=$languageArray['new_password_code'][$language]?></label>
                                                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="<?=$languageArray['new_password_code'][$language]?>">
                                            </div>
                                            <div class="mb-4">
                                                <label for="confirmPassword" class="form-label"><?=$languageArray['confirm_password_code'][$language]?></label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="<?=$languageArray['confirm_password_code'][$language]?>">
                                            </div>
                                            <button class="btn btn-success w-100" type="submit"><?=$languageArray['submit_code'][$language]?></button>
                                        </form>
                                    </div>
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

        <script src="plugins/jquery/jquery.min.js"></script>
        <?php include 'layouts/vendor-scripts.php'; ?>

        <!-- swiper js -->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

        <!-- profile init js -->
        <script src="assets/js/pages/profile.init.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js"></script>

        <script type="text/javascript">
            $(function () {
                function submitSettings(form, onSuccess) {
                    $.post('php/modules/settings/api.php', $(form).serialize(), function (data) {
                        var obj = JSON.parse(data);

                        if (obj.status === 'success') {
                            toastr["success"](obj.message, "Success:");
                            onSuccess();
                        }
                        else {
                            toastr["error"](obj.message, "Failed:");
                        }
                    });
                }

                // Reload after a profile save so the chosen language applies to the page
                $('#profileForm').on('submit', function (e) {
                    e.preventDefault();
                    submitSettings(this, function () {
                        setTimeout(function () { window.location.reload(); }, 1000);
                    });
                });

                $('#passwordForm').on('submit', function (e) {
                    e.preventDefault();
                    submitSettings(this, function () {
                        $('#passwordForm')[0].reset();
                    });
                });
            });
        </script>
    </body>
</html>