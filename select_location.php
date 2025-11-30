<?php
session_start();
require_once 'layouts/config.php';

// User must login first
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Fetch locations
$sql = "SELECT id, location_code, location_name FROM Location WHERE status = 0";
$result = mysqli_query($link, $sql);

$locations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}
?>
<!DOCTYPE html>
<html>
<?php include 'layouts/head-main.php'; ?>
<head>
    <title>Select Location | Synctronix Weighing System</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
</head>
<?php include 'layouts/body.php'; ?>

<div class="auth-page-wrapper pt-5" style="height: 100vh;">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg"  id="auth-particles">
        <div class="bg-overlay"></div>
        
        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>
    <div class="auth-page-content" style="padding-bottom: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mt-sm-5 mb-4 text-white-50" style="margin-bottom: 5px !important;">
                        <div>
                            <a href="index.php" class="d-inline-block auth-logo">
                                <img src="assets/images/logo-lg.png" alt="" height="20" style="width: 50%; height: 100%;">
                            </a>
                        </div>
                        <p class="mt-3 fs-15 fw-medium" style="display: none;"> </p>
                        <p class="mt-3 fs-15 fw-medium" style="display: none;">Synctronix Weighing System</p>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow border-2">
                        <div class="card-body p-4">

                            <h4 class="text-center text-primary mb-3">
                                Select Location
                            </h4>

                            <form method="POST" action="php/select_location_process.php">

                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <select name="location_id" class="form-control" required>
                                        <option value="">-- Choose Location --</option>
                                        <?php foreach($locations as $loc): ?>
                                            <option value="<?= $loc['id']; ?>">
                                                <?= $loc['location_name']; ?> (<?= $loc['location_code']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-success flex-grow-1">
                                        Continue
                                    </button>
                                    <a href="php/logout.php" class="btn btn-outline-danger flex-grow-1">
                                        Cancel
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>

                    <div class="text-center mt-3 text-muted">
                        © <?= date('Y') ?> Synctronix Weighing System
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<?php include 'layouts/vendor-scripts.php'; ?>
</body>
</html>
