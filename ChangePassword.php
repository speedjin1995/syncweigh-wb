<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

    <head>
        
        <title>Change Password | Synctronix - Weighing System</title>
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
                            <form action="php/changepassword.php" method="post">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white font-16">
                                        <?=$languageArray['password_code'][$language]?>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="oldPassword" class="col-sm-4 col-form-label"><?=$languageArray['old_password_code'][$language]?></label>
                                                    <div class="col-sm-8 ">
                                                        <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="<?=$languageArray['old_password_code'][$language]?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="newPassword" class="col-sm-4 col-form-label"><?=$languageArray['new_password_code'][$language]?></label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="<?=$languageArray['new_password_code'][$language]?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="confirmPassword" class="col-sm-4 col-form-label"><?=$languageArray['confirm_password_code'][$language]?></label>
                                                    <div class="col-sm-8">
                                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="<?=$languageArray['confirm_password_code'][$language]?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    if ($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN' || $_SESSION["allowDeduct"] == 'Y') {
                                        echo '
                                            <div class="card bg-light">
                                                <div class="card-header bg-primary text-white font-16">'
                                                    . $languageArray['password_code'][$language] . ' 2
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="oldPassword2" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['old_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="oldPassword2" name="oldPassword2" placeholder="' . $languageArray['old_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="newPassword2" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['new_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="newPassword2" name="newPassword2" placeholder="' . $languageArray['new_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="confirmPassword2" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['confirm_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="confirmPassword2" name="confirmPassword2" placeholder="' . $languageArray['confirm_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card bg-light">
                                                <div class="card-header bg-primary text-white font-16">'
                                                    . $languageArray['password_code'][$language] . ' 3
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="oldPassword3" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['old_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="oldPassword3" name="oldPassword3" placeholder="' . $languageArray['old_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="newPassword3" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['new_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="newPassword3" name="newPassword3" placeholder="' . $languageArray['new_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <label for="confirmPassword3" class="col-sm-4 col-form-label">'
                                                                    . $languageArray['confirm_password_code'][$language] . '
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <input type="password" class="form-control" id="confirmPassword3" name="confirmPassword3" placeholder="' . $languageArray['confirm_password_code'][$language] . '">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ';
                                    }
                                ?>
                                    
                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit"><?=$languageArray['submit_code'][$language]?></button>
                                </div>
                            </form>
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
    </body>
</html>