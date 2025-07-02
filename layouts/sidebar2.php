<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.php" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.jpg" alt="" height="70">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-lg.png" alt="" height="60">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.php" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.jpg" alt="" height="70">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-lg.png" alt="" height="60">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span><?=$lang['t-menu']?></span></li>
                <!--li class="nav-item">
                    <a href="dashboard.php" class="nav-link"><i class="mdi mdi-billboard"></i><?=$lang['t-billboard']?></a>
                </li-->
                <li class="nav-item">
                    <a href="simple.php" class="nav-link"><i class="mdi mdi-weight"></i><?=$lang['t-weighing']?></a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAuth">
                        <i class="ri-account-circle-line"></i> <span><?=$lang['t-setting']?></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAuth">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <?php
                                    if($_SESSION["roles"] == 'SADMIN'){
                                        echo '<li class="nav-item">
                                            <a href="companyProfile.php" class="nav-link">'.$lang['t-companyProfile'].'</a>
                                        </li> ';
                                    }
                                ?>
                                
                                <li class="nav-item">
                                    <a href="portSetup2.php" class="nav-link"><?=$lang['t-portSetup']?></a>
                                </li> 
                                <li class="nav-item">
                                    <a href="myProfile2.php" class="nav-link"><?=$lang['t-myProfile']?></a>
                                </li> 
                                <li class="nav-item">
                                    <a href="ChangePassword2.php" class="nav-link"><?=$lang['t-changePassword']?></a>
                                </li>                                 
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="php/logout.php" class="nav-link"><i class="mdi mdi-logout-variant"></i> <span><?=$lang['t-logout']?></span></a>
                </li>                 
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
