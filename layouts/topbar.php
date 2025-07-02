<?php
require_once "php/db_connect.php";

## Fetch records
// Lorry SQL
$lorryWeighingSQL = "(select * from Weight where status = '0' AND is_complete = 'N' AND is_cancel='N') UNION ALL (select * from Weight_Container where status = '0' AND is_complete = 'N' AND is_cancel='N')";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $normalWeighingSQL = "(select * from Weight where status = '0' AND is_complete = 'N' AND is_cancel='N' AND plant_code IN ('$username')) UNION ALL (select * from Weight_Container where status = '0' AND is_complete = 'N' AND is_cancel='N' AND plant_code IN ('$username'))";
}
$normalWeighing = $db->query($lorryWeighingSQL);

// Container SQL
$containerWeighingSQL = "select * from Weight_Container where status = '0' AND is_complete = 'Y' AND is_cancel='N'";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $normalWeighingSQL = "select * from Weight_Container where status = '0' AND is_complete = 'Y' AND is_cancel='N' AND plant_code IN ('$username'))";
}
$containerWeighing = $db->query($containerWeighingSQL);

$weighing2 = $db->query("SELECT * FROM Weight WHERE is_approved = 'N'");
# Lorry
$salesList = array();
$purchaseList = array();
$localList = array();
$miscList = array();
$count = 0;
# Container
$salesContainerList = array();
$purchaseContainerList = array();
$localContainerList = array();
$miscContainerList = array();
$containerCount = 0;

$salesList2 = array();
$purchaseList2 = array();
$localList2 = array();
$miscList2 = array();
$count2 = 0;

while($row=mysqli_fetch_assoc($normalWeighing)){
    $weightType = '';
    if ($row['weight_type'] == 'Empty Container') {
        $weightType = 'Primer Mover + Container';
    } elseif ($row['weight_type'] == 'Container') {
        $weightType = 'Primer Mover';
    } else {
        $weightType = $row['weight_type'];
    }

    if($row['transaction_status'] == 'Sales'){
        $salesList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else if($row['transaction_status'] == 'Purchase'){
        $purchaseList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else if($row['transaction_status'] == 'Local'){
        $localList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else{
        $miscList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
}

while($row=mysqli_fetch_assoc($containerWeighing)){
    $weightType = '';
    if ($row['weight_type'] == 'Empty Container') {
        $weightType = 'Primer Mover + Container';
    } elseif ($row['weight_type'] == 'Container') {
        $weightType = 'Primer Mover';
    } else {
        $weightType = $row['weight_type'];
    }

    if($row['transaction_status'] == 'Sales'){
        $salesContainerList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else if($row['transaction_status'] == 'Purchase'){
        $purchaseContainerList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else if($row['transaction_status'] == 'Local'){
        $localContainerList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
    else{
        $miscContainerList[] = array(
            "id" => $row['id'],
            "transaction_id" => $row['transaction_id'],
            "weight_type" => $weightType
        );
    }
}

while($row2=mysqli_fetch_assoc($weighing2)){
    if($row2['transaction_status'] == 'Sales'){
        $salesList2[] = array(
            "id" => $row2['id'],
            "transaction_id" => $row2['transaction_id'],
            "weight_type" => $row2['weight_type']
        );
    }
    else if($row2['transaction_status'] == 'Purchase'){
        $purchaseList2[] = array(
            "id" => $row2['id'],
            "transaction_id" => $row2['transaction_id'],
            "weight_type" => $row2['weight_type']
        );
    }
    else if($row2['transaction_status'] == 'Local'){
        $localList2[] = array(
            "id" => $row2['id'],
            "transaction_id" => $row2['transaction_id'],
            "weight_type" => $row2['weight_type']
        );
    }
    else{
        $miscList2[] = array(
            "id" => $row2['id'],
            "transaction_id" => $row2['transaction_id'],
            "weight_type" => $row2['weight_type']
        );
    }
}

$compids = '1';
$stmtComp = $db->prepare("SELECT * FROM Company WHERE id=?");
$stmtComp->bind_param('s', $compids);
$stmtComp->execute();
$resultC = $stmtComp->get_result();
$compname = '';
        
if ($rowc = $resultC->fetch_assoc()) {
    $compname = $rowc['name'];
}

$count = count($salesList) + count($purchaseList) + count($localList) + count($miscList);
$containerCount = count($salesContainerList) + count($purchaseContainerList) + count($localContainerList) + count($miscContainerList);
$count2 = count($salesList2) + count($purchaseList2) + count($localList2) + count($miscList2);
?>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.php" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm2.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-lg.png" alt="" height="17">
                        </span>
                    </a>

                    <a href="index.php" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm2.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-lg.png" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <h3><?=$compname ?></h3>
            </div>

            <div class="d-flex align-items-center">
                <!--div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..."
                                        aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div-->

                <!--div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="assets/images/flags/us.svg" alt="Header Language" height="20"
                            class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <!-- item->
                        <a href="?lang=en" class="dropdown-item notify-item language py-2" data-lang="en"
                            title="English">
                            <img src="assets/images/flags/us.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">English</span>
                        </a>

                        <!-- item->
                        <a href="?lang=sp" class="dropdown-item notify-item language py-2 language" data-lang="sp"
                            title="Spanish">
                            <img src="assets/images/flags/spain.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Española</span>
                        </a>

                        <!-- item->
                        <a href="?lang=gr" class="dropdown-item notify-item language py-2 language" data-lang="gr"
                            title="German">
                            <img src="assets/images/flags/germany.svg" alt="user-image" class="me-2 rounded"
                                height="18"> <span class="align-middle">Deutsche</span>
                        </a>

                        <!-- item->
                        <a href="?lang=it" class="dropdown-item notify-item language py-2 language" data-lang="it"
                            title="Italian">
                            <img src="assets/images/flags/italy.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Italiana</span>
                        </a>

                        <!-- item->
                        <a href="?lang=ru" class="dropdown-item notify-item language py-2 language" data-lang="ru"
                            title="Russian">
                            <img src="assets/images/flags/russia.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">русский</span>
                        </a>

                        <!-- item->
                        <a href="?lang=ch" class="dropdown-item notify-item language py-2 language" data-lang="ch"
                            title="Chinese">
                            <img src="assets/images/flags/china.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">中国人</span>
                        </a>

                        <!-- item->
                        <a href="?lang=fr" class="dropdown-item notify-item language py-2 language" data-lang="fr"
                            title="French">
                            <img src="assets/images/flags/french.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">français</span>
                        </a>

                        <!-- item->
                        <a href="?lang=ae" class="dropdown-item notify-item language" data-lang="ae" title="Arabic">
                            <img src="assets/images/flags/ae.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Arabic</span>
                        </a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-category-alt fs-22'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fw-semibold fs-15"> Web Apps </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-soft-info"> View All Apps
                                        <i class="ri-arrow-right-s-line align-middle"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="p-2">
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/github.png" alt="Github">
                                        <span>GitHub</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/bitbucket.png" alt="bitbucket">
                                        <span>Bitbucket</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/dribbble.png" alt="dribbble">
                                        <span>Dribbble</span>
                                    </a>
                                </div>
                            </div>

                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/dropbox.png" alt="dropbox">
                                        <span>Dropbox</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/mail_chimp.png" alt="mail_chimp">
                                        <span>Mail Chimp</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#!">
                                        <img src="assets/images/brands/slack.png" alt="slack">
                                        <span>Slack</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-cart-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-shopping-bag fs-22'></i>
                        <span
                            class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info">5</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0 dropdown-menu-cart"
                        aria-labelledby="page-header-cart-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> My Cart</h6>
                                </div>
                                <div class="col-auto">
                                    <span class="badge badge-soft-warning fs-13"><span class="cartitem-badge">7</span>
                                        items</span>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 300px;">
                            <div class="p-2">
                                <div class="text-center empty-cart" id="empty-cart">
                                    <div class="avatar-md mx-auto my-3">
                                        <div class="avatar-title bg-soft-info text-info fs-36 rounded-circle">
                                            <i class='bx bx-cart'></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-3">Your Cart is Empty!</h5>
                                    <a href="#" class="btn btn-success w-md mb-3">Shop Now</a>
                                </div>
                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/img-1.png"
                                            class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="mt-0 mb-1 fs-14">
                                                <a href="apps-ecommerce-product-details.php" class="text-reset">Branded
                                                    T-Shirts</a>
                                            </h6>
                                            <p class="mb-0 fs-12 text-muted">
                                                Quantity: <span>10 x $32</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">320</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-ghost-secondary remove-item-btn"><i
                                                    class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/img-2.png"
                                            class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="mt-0 mb-1 fs-14">
                                                <a href="apps-ecommerce-product-details.php" class="text-reset">Bentwood
                                                    Chair</a>
                                            </h6>
                                            <p class="mb-0 fs-12 text-muted">
                                                Quantity: <span>5 x $18</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">89</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-ghost-secondary remove-item-btn"><i
                                                    class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/img-3.png"
                                            class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="mt-0 mb-1 fs-14">
                                                <a href="apps-ecommerce-product-details.php" class="text-reset">
                                                    Borosil Paper Cup</a>
                                            </h6>
                                            <p class="mb-0 fs-12 text-muted">
                                                Quantity: <span>3 x $250</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">750</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-ghost-secondary remove-item-btn"><i
                                                    class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/img-6.png"
                                            class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="mt-0 mb-1 fs-14">
                                                <a href="apps-ecommerce-product-details.php" class="text-reset">Gray
                                                    Styled T-Shirt</a>
                                            </h6>
                                            <p class="mb-0 fs-12 text-muted">
                                                Quantity: <span>1 x $1250</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$ <span class="cart-item-price">1250</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-ghost-secondary remove-item-btn"><i
                                                    class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block dropdown-item dropdown-item-cart text-wrap px-3 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/products/img-5.png"
                                            class="me-3 rounded-circle avatar-sm p-2 bg-light" alt="user-pic">
                                        <div class="flex-1">
                                            <h6 class="mt-0 mb-1 fs-14">
                                                <a href="apps-ecommerce-product-details.php"
                                                    class="text-reset">Stillbird Helmet</a>
                                            </h6>
                                            <p class="mb-0 fs-12 text-muted">
                                                Quantity: <span>2 x $495</span>
                                            </p>
                                        </div>
                                        <div class="px-2">
                                            <h5 class="m-0 fw-normal">$<span class="cart-item-price">990</span></h5>
                                        </div>
                                        <div class="ps-2">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-ghost-secondary remove-item-btn"><i
                                                    class="ri-close-fill fs-16"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-bottom-0 border-start-0 border-end-0 border-dashed border"
                            id="checkout-elem">
                            <div class="d-flex justify-content-between align-items-center pb-3">
                                <h5 class="m-0 text-muted">Total:</h5>
                                <div class="px-2">
                                    <h5 class="m-0" id="cart-item-total">$1258.58</h5>
                                </div>
                            </div>

                            <a href="apps-ecommerce-checkout.php" class="btn btn-success text-center w-100">
                                Checkout
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div-->

                <!--div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationsDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bookmarks fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?=$count2 ?>
                        <span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Pending Approval </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge badge-soft-light fs-13"> <?=$count2 ?> New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti2-tab" role="tab"
                                            aria-selected="true">
                                            Sales <?php echo (count($salesList2) == 0 ? '' : '('.count($salesList2).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#messages2-tab" role="tab"
                                            aria-selected="false">
                                            Purchase <?php echo (count($purchaseList2) == 0 ? '' : '('.count($purchaseList2).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#alerts2-tab" role="tab"
                                            aria-selected="false">
                                            Local <?php echo (count($localList2) == 0 ? '' : '('.count($localList2).')'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti2-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($salesList2); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?approve=<?=$salesList2[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$salesList2[$i]['weight_type'] ?> weighing with <b><?=$salesList2[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending Approval</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="messages2-tab" role="tabpanel" aria-labelledby="messages-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($purchaseList2); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?approve=<?=$purchaseList2[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$purchaseList2[$i]['weight_type'] ?> weighing with <b><?=$purchaseList2[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending Approval</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade p-4" id="alerts2-tab" role="tabpanel" aria-labelledby="alerts-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($localList2); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?approve=<?=$localList2[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$localList2[$i]['weight_type'] ?> weighing with <b><?=$localList2[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending Approval</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center">
                                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result
                                    <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                        data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div-->

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <span class="fw-bold">LW</span>
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?=$count ?>
                        <span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown" style="width: 580px;">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Pending Lorry Weighing </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge badge-soft-light fs-13"> <?=$count ?> New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab"
                                            aria-selected="true">
                                            Dispatch <?php echo (count($salesList) == 0 ? '' : '('.count($salesList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                                            aria-selected="false">
                                            Receiving <?php echo (count($purchaseList) == 0 ? '' : '('.count($purchaseList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                                            aria-selected="false">
                                            Internal Transfer <?php echo (count($localList) == 0 ? '' : '('.count($localList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#misc-tab" role="tab"
                                            aria-selected="false">
                                            Miscellaneous <?php echo (count($miscList) == 0 ? '' : '('.count($miscList).')'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($salesList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$salesList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$salesList[$i]['weight_type'] ?> weighing with <b><?=$salesList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel" aria-labelledby="messages-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($purchaseList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$purchaseList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$purchaseList[$i]['weight_type'] ?> weighing with <b><?=$purchaseList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel" aria-labelledby="alerts-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($localList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$localList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$localList[$i]['weight_type'] ?> weighing with <b><?=$localList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade p-4" id="misc-tab" role="tabpanel" aria-labelledby="misc-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($miscList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$miscList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$miscList[$i]['weight_type'] ?> weighing with <b><?=$miscList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center">
                                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result
                                    <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                        data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <span class="fw-bold">CW</span>
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?=$containerCount ?>
                        <span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown" style="width: 580px;">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Pending Container Weighing </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge badge-soft-light fs-13"> <?=$containerCount ?> New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab"
                                            aria-selected="true">
                                            Dispatch <?php echo (count($salesContainerList) == 0 ? '' : '('.count($salesContainerList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                                            aria-selected="false">
                                            Receiving <?php echo (count($purchaseContainerList) == 0 ? '' : '('.count($purchaseContainerList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                                            aria-selected="false">
                                            Internal Transfer <?php echo (count($localContainerList) == 0 ? '' : '('.count($localContainerList).')'); ?>
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#misc-tab" role="tab"
                                            aria-selected="false">
                                            Miscellaneous <?php echo (count($miscContainerList) == 0 ? '' : '('.count($miscContainerList).')'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($salesContainerList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$salesContainerList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$salesContainerList[$i]['weight_type'] ?> weighing with <b><?=$salesContainerList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel" aria-labelledby="messages-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($purchaseContainerList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$purchaseContainerList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$purchaseContainerList[$i]['weight_type'] ?> weighing with <b><?=$purchaseContainerList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel" aria-labelledby="alerts-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($localContainerList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$localContainerList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$localContainerList[$i]['weight_type'] ?> weighing with <b><?=$localContainerList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="tab-pane fade p-4" id="misc-tab" role="tabpanel" aria-labelledby="misc-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php for($i=0; $i<count($miscContainerList); $i++){ ?>
                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="flex-1">
                                                    <a href="index.php?weight=<?=$miscContainerList[$i]['id'] ?>" class="stretched-link">
                                                        <h6 class="mt-0 mb-2 lh-base">There is a <?=$miscContainerList[$i]['weight_type'] ?> weighing with <b><?=$miscContainerList[$i]['transaction_id'] ?></b>
                                                            is <span class="text-secondary">Pending</span>
                                                        </h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center">
                                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result
                                    <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                        data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <!--img class="rounded-circle header-profile-user" src="assets/images/users/avatar-1.jpg"
                                alt="Header Avatar"-->
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?=$_SESSION["username"] ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text"><?=$_SESSION["roles"] ?></span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome <?=$_SESSION["username"] ?>!</h6>
                        <a class="dropdown-item" href="myProfile.php"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profile</span></a>
                        <!--a class="dropdown-item" href="apps-chat.php"><i
                                class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Messages</span></a>
                        <a class="dropdown-item" href="apps-tasks-kanban.php"><i
                                class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Taskboard</span></a>
                        <a class="dropdown-item" href="pages-faqs.php"><i
                                class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="pages-profile.php"><i
                                class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Balance : <b>$5971.67</b></span></a>
                        <a class="dropdown-item" href="pages-profile-settings.php"><span
                                class="badge bg-soft-success text-success mt-1 float-end">New</span><i
                                class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Settings</span></a>
                        <a class="dropdown-item" href="auth-lockscreen-basic.php"><i
                                class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Lock screen</span></a-->
                        <a class="dropdown-item" href="php/logout.php"><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key=t-logout><?=$lang['t-logout']?></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->