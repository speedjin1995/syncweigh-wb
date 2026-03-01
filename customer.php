<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
$role = $_SESSION["roles"];
$allowDeduct = $_SESSION["allowDeduct"];
?>

<head>
    <title>Customer | PWS - Weighing System</title>
    <?php include 'layouts/title-meta.php'; ?>

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Include jQuery library -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    
    <?php include 'layouts/head-css.php'; ?>

</head>

<?php include 'layouts/body.php'; ?>

<div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="h-100">
                            <div class="row mb-3 pb-1">
                                <div class="col-12">
                                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                        <div class="flex-grow-1">
                                            <!--h4 class="fs-16 mb-1">Good Morning, Anna!</h4>
                                            <p class="text-muted mb-0">Here's what's happening with your store
                                                today.</p-->
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <!-- <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="javascript:void(0);">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="mb-3">
                                                        <label for="customerCode" class="form-label">Customer Code</label>
                                                        <input type="text" class="form-control" placeholder="Customer Code" id="customerCode">
                                                    </div>
                                                </div>
                                                <div class="col-3">

                                                </div>
                                                <div class="col-3">
  
                                                </div>
                                                <div class="col-3">
                                                    <div class="text-end mt-4">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bx bx-search-alt"></i>
                                                            <?=$languageArray['search_code'][$language]?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>                                                                        
                                    </div>
                                </div>
                            </div> -->
                            
                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <div class="row">
                                <div class="col-xl-3 col-md-6 add-new-weight">

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle"><?=$languageArray['add_new_code'][$language]?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="customerForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="customerCode" class="col-sm-4 col-form-label"><?=$languageArray['customer_code_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="customerCode" name="customerCode" placeholder="<?=$languageArray['customer_code_code'][$language]?>" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="companyRegNo" class="col-sm-4 col-form-label"><?=$languageArray['reg_no_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4">
                                                                                                <input type="text" class="form-control" id="companyRegNo" name="companyRegNo">
                                                                                            </div>
                                                                                            <div class="col-sm-8">
                                                                                                <div class="row">
                                                                                                    <label for="newRegNo" class="col-sm-4 col-form-label"><?=$languageArray['new_reg_no_code'][$language]?></label>
                                                                                                    <div class="col-sm-8">
                                                                                                        <input type="text" class="form-control" id="newRegNo" name="newRegNo" required>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="companyName" class="col-sm-4 col-form-label"><?=$languageArray['customer_name_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="<?=$languageArray['customer_name_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine1" class="col-sm-4 col-form-label"><?=$languageArray['address_code'][$language]?> 1</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine1" name="addressLine1" placeholder="<?=$languageArray['address_code'][$language]?> 1">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine2" class="col-sm-4 col-form-label"><?=$languageArray['address_code'][$language]?> 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine2" name="addressLine2" placeholder="<?=$languageArray['address_code'][$language]?> 2">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine3" class="col-sm-4 col-form-label"><?=$languageArray['address_code'][$language]?> 3</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine3" name="addressLine3" placeholder="<?=$languageArray['address_code'][$language]?> 3">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine4" class="col-sm-4 col-form-label">Address Line 4</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine4" name="addressLine4" placeholder="Address Line 4">
                                                                                    </div>
                                                                                </div>
                                                                            </div> -->
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="phoneNo" class="col-sm-4 col-form-label"><?=$languageArray['phone_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="<?=$languageArray['phone_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="faxNo" class="col-sm-4 col-form-label"><?=$languageArray['fax_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="faxNo" name="faxNo" placeholder="<?=$languageArray['fax_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="contactName" class="col-sm-4 col-form-label"><?=$languageArray['pic_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="contactName" name="contactName" placeholder="<?=$languageArray['pic_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="icNo" class="col-sm-4 col-form-label"><?=$languageArray['ic_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="icNo" name="icNo" placeholder="<?=$languageArray['ic_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="tinNo" class="col-sm-4 col-form-label"><?=$languageArray['tin_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="tinNo" name="tinNo" placeholder="<?=$languageArray['tin_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="mpob" class="col-sm-4 col-form-label"><?=$languageArray['mpob_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="mpob" name="mpob" placeholder="<?=$languageArray['mpob_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="mspoNo" class="col-sm-4 col-form-label"><?=$languageArray['mspo_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="mspoNo" name="mspoNo" placeholder="<?=$languageArray['mspo_code'][$language]?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="paymentTerm" class="col-sm-4 col-form-label"><?=$languageArray['payment_term_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="paymentTerm" name="paymentTerm" class="form-select select2">
                                                                                            <option value="Term"><?=$languageArray['term_code'][$language]?></option>
                                                                                            <option value="Cash"><?=$languageArray['cash_code'][$language]?></option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" id="id" name="id">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                                <button type="button" class="btn btn-success" id="submitCustomer"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="uploadModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title"><?=$languageArray['upload_excel_code'][$language]?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton"><?=$languageArray['preview_data_code'][$language]?></button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                        <button type="button" class="btn btn-success" id="submitWeights"><?=$languageArray['submit_code'][$language]?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="errorModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title">Error Log</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <ol id="errorList" class="text-danger mt-2" style="padding-left: 20px;"></ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Deduction Modal -->
                                    <div class="modal fade" id="deductionModal" tabindex="-1" aria-hidden="true" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title">Deduction Setup</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="deductionForm" role="form">
                                                        <input type="hidden" id="type" name="type" value="Customer">
                                                        <input type="hidden" id="custSuppId" name="custSuppId">
                                                        <input type="hidden" id="deductionId" name="deductionId">
                                                        <div class="d-flex align-items-center mb-3 justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <label for="statusSwitch" class="col-form-label me-2">Mode</label>
                                                                <select id="statusSwitch" name="statusSwitch" class="form-select select2" style="width: 150px;">
                                                                    <!--option value="Manual" selected>Manual</option-->
                                                                    <option value="Auto" selected>Auto</option>
                                                                    <option value="Disable">Disable</option>
                                                                </select>
                                                            </div>
                                                            <button type="button" id="autoNewBtn" class="btn btn-primary d-none">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>Add New
                                                            </button>
                                                        </div>

                                                        <!-- Manual Mode View -->
                                                        <div id="manualView">
                                                            <div class="row">
                                                                <div class="col-3">
                                                                    <label>F1 - F3 ( - ) kg</label>
                                                                    <input type="number" class="form-control mb-2" id="F1" name="F1" placeholder="F1" min="0">
                                                                    <input type="number" class="form-control mb-2" id="F2" name="F2" placeholder="F2" min="0">
                                                                    <input type="number" class="form-control mb-2" id="F3" name="F3" placeholder="F3" min="0">
                                                                </div>
                                                                <div class="col-3">
                                                                    <label>F4 - F6 ( + ) kg</label>
                                                                    <input type="number" class="form-control mb-2" id="F4" name="F4" placeholder="F4" min="0">
                                                                    <input type="number" class="form-control mb-2" id="F5" name="F5" placeholder="F5" min="0">
                                                                    <input type="number" class="form-control mb-2" id="F6" name="F6" placeholder="F6" min="0">
                                                                </div>
                                                                <div class="col-3">
                                                                    <label>F7 - F9 ( - ) %</label>
                                                                    <input type="number" class="form-control mb-2" id="F7" name="F7" placeholder="F7" min="0" max="100">
                                                                    <input type="number" class="form-control mb-2" id="F8" name="F8" placeholder="F8" min="0" max="100">
                                                                    <input type="number" class="form-control mb-2" id="F9" name="F9" placeholder="F9" min="0" max="100">
                                                                </div>
                                                                <div class="col-3">
                                                                    <label>F10 - F12 ( + ) %</label>
                                                                    <input type="number" class="form-control mb-2" id="F10" name="F10" placeholder="F10" min="0" max="100">
                                                                    <input type="number" class="form-control mb-2" id="F11" name="F11" placeholder="F11" min="0" max="100">
                                                                    <input type="number" class="form-control mb-2" id="F12" name="F12" placeholder="F12" min="0" max="100">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Auto Mode View -->
                                                        <div id="autoView" style="display: none;">
                                                            <div class="row">
                                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                                    <table class="table table-primary">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="30%">Ranges</th>
                                                                                <th>(-) kg</th>
                                                                                <th>(+) kg</th>
                                                                                <th>(-%)</th>
                                                                                <th>(+%)</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="autoTable"></tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="row mt-4">
                                                            <div class="col-4 text-center">
                                                                <button type="button" class="btn btn-danger w-100" id="escButton">ESC</button>
                                                            </div>
                                                            <div class="col-4">
                                                                <button type="button" class="btn btn-warning w-100" id="resetZero">Reset to Zero</button>
                                                            </div>
                                                            <div class="col-4">
                                                                <button type="button" class="btn btn-success w-100" id="submitDeduction"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Restriction Modal -->
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
                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0"><?=$languageArray['previous_records_code'][$language]?></h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="template/Customer_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        <?=$languageArray['download_template_code'][$language]?>
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    <?=$languageArray['upload_excel_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <button type="button" id="addCustomers" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="customerTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['customer_code_code'][$language]?></th>
                                                                    <th><?=$languageArray['reg_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['new_reg_no_code'][$language]?></th>
                                                                    <th><?=$languageArray['customer_name_code'][$language]?></th>
                                                                    <th><?=$languageArray['address_code'][$language]?> 1</th>
                                                                    <th><?=$languageArray['address_code'][$language]?> 2</th>
                                                                    <th><?=$languageArray['address_code'][$language]?> 3</th>
                                                                    <th><?=$languageArray['phone_code'][$language]?></th>
                                                                    <th><?=$languageArray['fax_code'][$language]?></th>
                                                                    <th><?=$languageArray['pic_code'][$language]?></th>
                                                                    <th><?=$languageArray['ic_code'][$language]?></th>
                                                                    <th><?=$languageArray['tin_code'][$language]?></th>
                                                                    <th><?=$languageArray['status_code'][$language]?></th>
                                                                    <th><?=$languageArray['action_code'][$language]?></th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end row-->
                                    </div> <!-- end .h-100-->
                                </div> <!-- end col -->
                            </div><!-- container-fluid -->

                        </div> <!-- end .h-100-->
                    </div> <!-- end col -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->




    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <script src="assets/js/pages/form-validation.init.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>

    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>

    <script type="text/html" id="autoRowTemplate">
        <tr class="details">
            <td>
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" class="form-control range-from" name="rangeFrom" placeholder="From (kg)" min="0" step="0.01" required>
                    <span class="text-muted">to</span>
                    <input type="number" class="form-control range-to" name="rangeTo" placeholder="To (kg)" min="0" step="0.01" required>
                </div>
            </td>
            <td>
                <input type="number" class="form-control" name="negativeKg" min="0" step="0.01">
            </td>
            <td>
                <input type="number" class="form-control" name="positiveKg" min="0" step="0.01">
            </td>
            <td>
                <input type="number" class="form-control" name="negativePerc" min="0" max="100" step="0.01">
            </td>
            <td>
                <input type="number" class="form-control" name="positivePerc" min="0" max="100" step="0.01">
            </td>
            <td class="d-flex" style="text-align:center">
                <button type="button" class="btn btn-danger" id="remove">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

<script type="text/javascript">

var table;
var role = '<?=$role?>';
var allowDeduct = '<?=$allowDeduct?>';
var rowCount = 0;

$(function () {
    // Initialize all Select2 elements in the modal
    $('#addModal .select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addModal') // Ensures dropdown is not cut off
    });

    // Apply custom styling to Select2 elements in addModal
    $('#addModal .select2-container .select2-selection--single').css({
        'padding-top': '4px',
        'padding-bottom': '4px',
        'height': 'auto'
    });

    $('#addModal .select2-container .select2-selection__arrow').css({
        'padding-top': '33px',
        'height': 'auto'
    });

    table = $("#customerTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadCustomers.php'
        },
        'columns': [
            {
                // Add a checkbox with a unique ID for each row
                data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                className: 'select-checkbox',
                orderable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                }
            },
            { data: 'customer_code' },
            { data: 'company_reg_no' },
            { data: 'new_reg_no' },
            { data: 'name' },
            { data: 'address_line_1' },
            { data: 'address_line_2' },
            { data: 'address_line_3' },
            { data: 'phone_no' },
            { data: 'fax_no' },
            { data: 'contact_name' },
            { data: 'ic_no' },
            { data: 'tin_no' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    if (row.status == '1'){
                        return '<button title="Reactivate" type="button" id="reactivate'+data+'" onclick="reactivate('+data+')" class="btn btn-warning btn-sm">Reactivate</button>';
                    }else{
                        return 'Active';
                    }
                }
            },
            { 
                data: 'id',
                render: function (data, type, row) {
                    let dropdownHtml = '';

                    dropdownHtml += '<div class="dropdown d-inline-block">';
                    dropdownHtml += '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    dropdownHtml += '<i class="ri-more-fill align-middle"></i></button>';
                    dropdownHtml += '<ul class="dropdown-menu dropdown-menu-end">';

                    // Edit button
                    dropdownHtml += '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')">';
                    dropdownHtml += '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> <?=$languageArray['edit_code'][$language] ?></a></li>';

                    // Conditionally add Deduction Setup button
                    if (role === 'ADMIN' || role === 'SADMIN' || allowDeduct === 'Y') {
                        // button marked deduction-field so WS listener can hide it
                        dropdownHtml += '<li class="deduction-field"><a class="dropdown-item" id="deduction' + data + '" onclick="deduction(' + data + ', ' + row.customer_deduction_id + ')">';
                        dropdownHtml += '<i class="ri-subtract-fill align-bottom me-2 text-muted"></i> <?=$languageArray['deduction_setup_code'][$language] ?></a></li>';
                    }

                    // Delete button
                    dropdownHtml += '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')">';
                    dropdownHtml += '<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> <?=$languageArray['delete_code'][$language] ?> </a></li>';

                    dropdownHtml += '</ul></div>';

                    return dropdownHtml;
                }
            }
        ]       
    });

    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#customerTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });
    
    // $.validator.setDefaults({
    //     submitHandler: function() {
    $('#submitCustomer').on('click', function(){
        if($('#customerForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/customers.php', $('#customerForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success')
                {
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed')
                {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else
                {

                }
            });
        }
        // }
    });

    $('#submitWeights').on('click', function(){
        $('#spinnerLoading').show();
        var formData = $('#uploadForm').serializeArray();
        var data = [];
        var rowIndex = -1;
        formData.forEach(function(field) {
        var match = field.name.match(/([a-zA-Z0-9]+)\[(\d+)\]/);
        if (match) {
            var fieldName = match[1];
            var index = parseInt(match[2], 10);
            if (index !== rowIndex) {
            rowIndex = index;
            data.push({});
            }
            data[index][fieldName] = field.value;
        }
        });

        // Send the JSON array to the server
        $.ajax({
            url: 'php/uploadCustomers.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                var obj = JSON.parse(response);
                if (obj.status === 'success') {
                    $('#spinnerLoading').hide();
                    $('#uploadModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                    $('#customerTable').DataTable().ajax.reload(null, false);
                } 
                else if (obj.status === 'failed') {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                } 
                else if (obj.status === 'error') {
                    $('#spinnerLoading').hide();
                    $('#uploadModal').modal('hide');
                    // alert(obj.message);
                    // $("#failBtn").attr('data-toast-text', obj.message );
                    // $("#failBtn").click();
                    $('#customerTable').DataTable().ajax.reload(null, false);
                    $('#errorModal').find('#errorList').empty();
                    var errorMessage = obj.message;
                    for (var i = 0; i < errorMessage.length; i++) {
                        $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);                            
                    }
                    $('#errorModal').modal('show');
                } 
                else {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', 'Failed to save');
                    $("#failBtn").click();
                }
            }
        });
    });

    $('#addCustomers').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#customerCode').val("");
        $('#addModal').find('#companyName').val("");
        $('#addModal').find('#companyRegNo').val("");
        $('#addModal').find('#newRegNo').val("");
        $('#addModal').find('#addressLine1').val("");
        $('#addModal').find('#addressLine2').val("");
        $('#addModal').find('#addressLine3').val("");
        $('#addModal').find('#addressLine4').val("");
        $('#addModal').find('#phoneNo').val("");
        $('#addModal').find('#faxNo').val("");
        $('#addModal').find('#contactName').val("");
        $('#addModal').find('#icNo').val("");
        $('#addModal').find('#tinNo').val("");
        $('#addModal').find('#mpob').val("");
        $('#addModal').find('#mpsoNo').val("");
        $('#addModal').find('#paymentTerm').val("Term").trigger('change');

        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal').modal('show');
        
        $('#customerForm').validate({
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
    });

    $('#uploadExcel').on('click', function(){
        $('#uploadModal').modal('show');

        $('#uploadForm').validate({
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
    });

    $('#uploadModal').find('#previewButton').on('click', function(){
        var fileInput = document.getElementById('fileInput');
        var file = fileInput.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var data = e.target.result;
            // Process data and display preview
            displayPreview(data);
        };

        reader.readAsBinaryString(file);
    });

    $('#multiDeactivate').on('click', function () {
        $('#spinnerLoading').show();
        var selectedIds = []; // An array to store the selected 'id' values

        $("#customerTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteCustomer.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        toastr["success"](obj.message, "Success:");
                        $('#spinnerLoading').hide();
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }

            $('#spinnerLoading').hide();
        } 
        else {
            // Optionally, you can display a message or take another action if no IDs are selected
            alert("Please select at least one customer to delete.");
            $('#spinnerLoading').hide();
        }     
    });

    // Find and remove selected table rows
    $("#autoTable").on('click', 'button[id^="remove"]', function () {
        $(this).parents("tr").remove();
    });

    // Auto New button functionality
    $('#autoNewBtn').on('click', function() {
        var template = $("#autoRowTemplate").html();
        var newRow = $(template);
        
        // Set unique IDs for the new row
        newRow.find('.details:last').attr("id", "detail" + rowCount);
        newRow.find('.details:last').attr("data-index", rowCount);
        newRow.find('#remove:last').attr("id", "remove" + rowCount);

        newRow.find('input[name="rangeFrom"]').attr('id', 'rangeFrom' + rowCount);
        newRow.find('input[name="rangeTo"]').attr('id', 'rangeTo' + rowCount);
        newRow.find('input[name="negativeKg"]').attr('id', 'negativeKg' + rowCount);
        newRow.find('input[name="positiveKg"]').attr('id', 'positiveKg' + rowCount);
        newRow.find('input[name="negativePerc"]').attr('id', 'negativePerc' + rowCount);
        newRow.find('input[name="positivePerc"]').attr('id', 'positivePerc' + rowCount);
        
        // Set array names for form submission
        newRow.find('input[name="rangeFrom"]').attr('name', 'rangeFrom[' + rowCount + ']');
        newRow.find('input[name="rangeTo"]').attr('name', 'rangeTo[' + rowCount + ']');
        newRow.find('input[name="negativeKg"]').attr('name', 'negativeKg[' + rowCount + ']');
        newRow.find('input[name="positiveKg"]').attr('name', 'positiveKg[' + rowCount + ']');
        newRow.find('input[name="negativePerc"]').attr('name', 'negativePerc[' + rowCount + ']');
        newRow.find('input[name="positivePerc"]').attr('name', 'positivePerc[' + rowCount + ']');
        
        $("#autoTable").append(newRow);
        rowCount++;
    });

    // Validate that "To" value is greater than "From" value
    $("#autoTable").on('blur', '.range-from, .range-to', function() {
        var row = $(this).closest('tr');
        var fromValue = parseFloat(row.find('.range-from').val()) || 0;
        var toValue = parseFloat(row.find('.range-to').val()) || 0;

        if (fromValue > 0 && toValue > 0 && fromValue >= toValue) {
            alert('The "To" value must be greater than the "From" value.');
            $(this).focus();
        }
    });

    $("#passwordCheckForm").on("submit", function (e) {
        e.preventDefault();
        var custSuppId = $('#deductionModal').find('#custSuppId').val();
        var password2 = $('#passwordModal').find('#password2').val();

        $.post("php/checkPasswords.php", { type: 'screen', password2: password2 }, function (data) {
            let obj = JSON.parse(data);

            if (obj.status === "success") {
                $("#passwordModal").modal("hide");

                $.post("php/getDeduction.php", { userID: custSuppId, type: 'Customer' }, function (data) {
                    let obj = JSON.parse(data);

                    if (obj.status == "success") {
                        // Check if message is not empty
                        if (obj.message && Object.keys(obj.message).length > 0) {
                            $('#deductionModal').find('#deductionId').val(obj.message.id);
                            $('#deductionModal').find('#custSuppId').val(obj.message.customer_supplier_id);
                            $('#deductionModal').find('#statusSwitch').val(obj.message.status).trigger('change');
                            $('#deductionModal').find('#F1').val(obj.message.F1);
                            $('#deductionModal').find('#F2').val(obj.message.F2);
                            $('#deductionModal').find('#F3').val(obj.message.F3);
                            $('#deductionModal').find('#F4').val(obj.message.F4);
                            $('#deductionModal').find('#F5').val(obj.message.F5);
                            $('#deductionModal').find('#F6').val(obj.message.F6);
                            $('#deductionModal').find('#F7').val(obj.message.F7);
                            $('#deductionModal').find('#F8').val(obj.message.F8);
                            $('#deductionModal').find('#F9').val(obj.message.F9);
                            $('#deductionModal').find('#F10').val(obj.message.F10);
                            $('#deductionModal').find('#F11').val(obj.message.F11);
                            $('#deductionModal').find('#F12').val(obj.message.F12);
                            loadAutoDeductionData(obj.message.auto_data);
                            $('#deductionModal').modal('show');
                        } else {
                            $('#deductionModal').find('#manualView input[name^="F"]').val('');
                            $('#deductionModal').find('#autoTable').empty();
                            $('#deductionModal').find('#statusSwitch').val('Disable').trigger('change');
                            $('#deductionModal').modal('show');
                        }
                        
                        $('#deductionModal').modal('show');
                    } else {
                        alert(obj.message);
                    }
                });
            } else {
                alert(obj.message);
                $("#passwordModal").modal("hide"); // hide modal
            }
        });
    });

    $('#deductionModal').find('#statusSwitch').on('change', function () {
        var selectedValue = $(this).val();

        if (selectedValue == 'Manual') {
            $('#deductionModal').find('#manualView').show();
            $('#deductionModal').find('#autoView').hide();
            $('#deductionModal').find('#autoNewBtn').addClass('d-none');
            $('#deductionModal').find('#buttonRow').show();
        } else if (selectedValue == 'Auto') {
            $('#deductionModal').find('#manualView').hide();
            $('#deductionModal').find('#autoView').show();
            $('#deductionModal').find('#autoNewBtn').removeClass('d-none');
            $('#deductionModal').find('#buttonRow').show();
        } else {
            $('#deductionModal').find('#manualView').hide();
            $('#deductionModal').find('#autoView').hide();
            $('#deductionModal').find('#autoNewBtn').addClass('d-none');
            $('#deductionModal').find('#buttonRow').hide();
        }
    });

    $('#deductionModal').find('#resetZero').on('click', function(e) {
        e.preventDefault();
        var mode = $('#deductionModal').find('#statusSwitch').val();
        if (mode == 'Manual') {
            // reset all F1-F12 inputs to 0
            $('#deductionModal').find('#manualView input[name^="F"]').val(0);
        } else if (mode == 'Auto') {
            // Clear the autoTable body
            $('#deductionModal').find('#autoTable').empty();
            rowCount = 0; // Reset rowCount so "Add New" starts from 0
        }
    });

    $('#submitDeduction').on('click', function(){
        if($('#deductionForm').valid()){
            $('#deductionModal').modal('hide');
            // // Switch modal to ask for password3
            // $('#passwordModal').find('#password2Div').hide();
            // $('#passwordModal').find('#password3Div').show();
            // $("#passwordModal").modal({
            //     backdrop: 'static', // disable closing by clicking outside
            //     keyboard: false // disable ESC close
            // }).modal("show");


            // Handle password3 submit
            // $("#passwordCheckForm").off("submit").on("submit", function (e) {
            //     e.preventDefault();
            //     var password3 = $('#password3').val();

            //     $.post("php/checkPasswords.php", { type: 'save', password3: password3 }, function (data) {
            //         let obj = JSON.parse(data);

            //         if (obj.status === "success") {
            //             $("#passwordModal").modal("hide");

                        // Proceed with saving form
                        $.post('php/deductions.php', $('#deductionForm').serialize(), function (data) {
                            var obj = JSON.parse(data);

                            if (obj.status === 'success') {
                                window.location.reload();
                            } else if (obj.status === 'failed') {
                                alert(obj.message);
                            } else {
                                alert("Failed to update deduction");
                            }
                        });
            //         } else {
            //             alert(obj.message);
            //         }
            //     });
            // });
        }
    });

    $('#escButton').on('click', function(){
        $('#deductionModal').modal('hide');
    });
});

function loadAutoDeductionData(dataArray) {
    $("#autoTable").empty(); // clear existing rows first
    rowCount = 0;

    if (!dataArray || dataArray.length === 0) {
        // if no data, start with one blank row
        $('#autoNewBtn').trigger('click');
        return;
    }

    dataArray.forEach((item, index) => {
        var template = $("#autoRowTemplate").html();
        var newRow = $(template);
        
        // Assign unique IDs
        newRow.find('.details:last').attr("id", "detail" + rowCount);
        newRow.find('.details:last').attr("data-index", rowCount);
        newRow.find('#remove:last').attr("id", "remove" + rowCount);

        newRow.find('input[name="rangeFrom"]').attr({
            'id': 'rangeFrom' + rowCount,
            'name': 'rangeFrom[' + rowCount + ']',
            'value': item.rangeFrom ?? ''
        });

        newRow.find('input[name="rangeTo"]').attr({
            'id': 'rangeTo' + rowCount,
            'name': 'rangeTo[' + rowCount + ']',
            'value': item.rangeTo ?? ''
        });

        newRow.find('input[name="negativeKg"]').attr({
            'id': 'negativeKg' + rowCount,
            'name': 'negativeKg[' + rowCount + ']',
            'value': item.negativeKg ?? ''
        });

        newRow.find('input[name="positiveKg"]').attr({
            'id': 'positiveKg' + rowCount,
            'name': 'positiveKg[' + rowCount + ']',
            'value': item.positiveKg ?? ''
        });

        newRow.find('input[name="negativePerc"]').attr({
            'id': 'negativePerc' + rowCount,
            'name': 'negativePerc[' + rowCount + ']',
            'value': item.negativePerc ?? ''
        });

        newRow.find('input[name="positivePerc"]').attr({
            'id': 'positivePerc' + rowCount,
            'name': 'positivePerc[' + rowCount + ']',
            'value': item.positivePerc ?? ''
        });

        $("#autoTable").append(newRow);
        rowCount++;
    });
}

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getCustomer.php', {userID: id}, function(data)
    {
        var obj = JSON.parse(data);
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#customerCode').val(obj.message.customer_code);
            $('#addModal').find('#companyName').val(obj.message.name);
            $('#addModal').find('#companyRegNo').val(obj.message.company_reg_no);
            $('#addModal').find('#newRegNo').val(obj.message.new_reg_no);
            $('#addModal').find('#addressLine1').val(obj.message.address_line_1);
            $('#addModal').find('#addressLine2').val(obj.message.address_line_2);
            $('#addModal').find('#addressLine3').val(obj.message.address_line_3);
            $('#addModal').find('#phoneNo').val(obj.message.phone_no);
            $('#addModal').find('#faxNo').val(obj.message.fax_no);
            $('#addModal').find('#contactName').val(obj.message.contact_name);
            $('#addModal').find('#icNo').val(obj.message.ic_no);
            $('#addModal').find('#tinNo').val(obj.message.tin_no);
            $('#addModal').find('#mpob').val(obj.message.mpob);
            $('#addModal').find('#mspoNo').val(obj.message.mspo_no);
            $('#addModal').find('#paymentTerm').val(obj.message.payment_term).trigger('change');

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal').modal('show');
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    $('#spinnerLoading').show();
    if (confirm('Are you sure you want to cancel this item?')) {
        $.post('php/deleteCustomer.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                table.ajax.reload();
                $('#spinnerLoading').hide();
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }
    $('#spinnerLoading').hide();
}

function displayPreview(data) {
    // Parse the Excel data
    var workbook = XLSX.read(data, { type: 'binary' });

    // Get the first sheet
    var sheetName = workbook.SheetNames[0];
    var sheet = workbook.Sheets[sheetName];

    // Convert the sheet to an array of objects
    var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    // Get the headers
    var headers = jsonData[0];

    // Ensure we handle cases where there may be less than 15 columns
    while (headers.length < 13) {
        headers.push(''); // Adding empty headers to reach 15 columns
    }

    // Create HTML table headers
    var htmlTable = '<table style="width:100%;"><thead><tr>';
    headers.forEach(function(header) {
        htmlTable += '<th>' + header + '</th>';
    });
    htmlTable += '</tr></thead><tbody>';

    // Iterate over the data and create table rows
    for (var i = 1; i < jsonData.length; i++) {
        htmlTable += '<tr>';
        var rowData = jsonData[i];

        // Ensure we handle cases where there may be less than 15 cells in a row
        while (rowData.length < 13) {
            rowData.push(''); // Adding empty cells to reach 15 columns
        }

        for (var j = 0; j < 13; j++) {
            var cellData = rowData[j];
            var formattedData = cellData;

            // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
            if (typeof cellData === 'number' && cellData > 0) {
                var excelDate = XLSX.SSF.parse_date_code(cellData);
            }

            htmlTable += '<td><input type="text" id="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+(i-1)+'" name="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
        }
        htmlTable += '</tr>';
    }

    htmlTable += '</tbody></table>';

    var previewTable = document.getElementById('previewTable');
    previewTable.innerHTML = htmlTable;
}

function reactivate(id) {
  if (confirm('Do you want to reactivate this item?')) {
    $('#spinnerLoading').show();
    $.post('php/reactivateMasterData.php', {userID: id, type: "Customer"}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
            table.ajax.reload();
            $('#spinnerLoading').hide();
            $("#successBtn").attr('data-toast-text', obj.message);
            $("#successBtn").click();
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }

        $('#spinnerLoading').hide();
    });
  }

  $('#spinnerLoading').hide();
}

function deduction(id, deductionId = '') {
    $('#spinnerLoading').show();
    $.post('php/getDeduction.php', {userID: deductionId, type: "Customer"}, function(data)
    {
        var obj = JSON.parse(data);
        if(obj.status === 'success'){
            $('#deductionModal').find('#custSuppId').val(id);
            $('#deductionModal').find('#deductionId').val(obj.message.id || deductionId);
            $('#deductionModal').find('#statusSwitch').val(obj.message.status).trigger('change');
            $('#deductionModal').find('#F1').val(obj.message.F1);
            $('#deductionModal').find('#F2').val(obj.message.F2);
            $('#deductionModal').find('#F3').val(obj.message.F3);
            $('#deductionModal').find('#F4').val(obj.message.F4);
            $('#deductionModal').find('#F5').val(obj.message.F5);
            $('#deductionModal').find('#F6').val(obj.message.F6);
            $('#deductionModal').find('#F7').val(obj.message.F7);
            $('#deductionModal').find('#F8').val(obj.message.F8);
            $('#deductionModal').find('#F9').val(obj.message.F9);
            $('#deductionModal').find('#F10').val(obj.message.F10);
            $('#deductionModal').find('#F11').val(obj.message.F11);
            $('#deductionModal').find('#F12').val(obj.message.F12);
            loadAutoDeductionData(obj.message.auto_data);
            $('#deductionModal').modal('show');
            // $('#passwordModal').find('#password2').val('');
            // $('#passwordModal').find('#password3').val('');
            // $('#passwordModal').find('#password2Div').show(); // show password2 input
            // $('#passwordModal').find('#password3Div').hide(); // hide password3 input
            // $("#passwordModal").modal({
            //     backdrop: 'static', // disable closing by clicking outside
            //     keyboard: false // disable ESC close
            // }).modal("show");
        }
        else if(obj.status === 'failed'){
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        else{
            $('#spinnerLoading').hide();
            $("#failBtn").attr('data-toast-text', obj.message );
            $("#failBtn").click();
        }
        $('#spinnerLoading').hide();
    });
}

$('#customerForm').validate({
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
</script>
    </body>

    </html>