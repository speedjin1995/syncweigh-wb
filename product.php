<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
if (!hasModulePermission('Master Data', 'Products', ['view'])){
    header('Location: no-permission.php');
    exit;
}
?>
<?php
// Pricing modal options, loaded once for the page
$defaultCurrency = $db->query("SELECT id FROM Currency WHERE currency_code = 'MYR' AND status = '0' LIMIT 1")->fetch_assoc();
$defaultCurrencyId = $defaultCurrency ? (int) $defaultCurrency['id'] : '';
$currencyOptions = $db->query("SELECT id, currency_code, name FROM Currency WHERE status = '0' ORDER BY currency_code");
$customerOptions = $db->query("SELECT id, customer_code, name FROM Customer WHERE status = '0' ORDER BY name");
?>

<head>
    <title><?=$languageArray['product_code'][$language]?> | Synctronix - Weighing System</title>
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
                                                    <form role="form" id="productForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productCode" class="col-sm-4 col-form-label"><?=$languageArray['product_code_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="productCode" name="productCode" placeholder="Product Code" required>
                                                                                        <div class="invalid-feedback">
                                                                                            <?=$languageArray['please_fill_in_the_field_code'][$language]?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productName" class="col-sm-4 col-form-label"><?=$languageArray['product_name_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Product Name" required>
                                                                                        <div class="invalid-feedback">
                                                                                            <?=$languageArray['please_fill_in_the_field_code'][$language]?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label"><?=$languageArray['description_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="description" class="col-sm-4 col-form-label"><?=$languageArray['variance_type_code'][$language]?></label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-control select2" style="width: 100%;" id="varianceType" name="varianceType">
                                                                                            <option value="" selected disabled hidden>Please Select</option>
                                                                                            <option value="W">kg</option>
                                                                                            <option value="P">%</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="high" class="col-sm-4 col-form-label"><?=$languageArray['high_code'][$language]?> (+)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="high" name="high" placeholder="High" value="0">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="low" class="col-sm-4 col-form-label"><?=$languageArray['low_code'][$language]?> (-)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="low" name="low" placeholder="Low" value="0">
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
                                                                <button type="button" class="btn btn-success" id="submitProduct"><?=$languageArray['submit_code'][$language]?></button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->                                    

                                    <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?=$languageArray['pricing_code'][$language]?> <span id="pricingTitle" class="fs-14 text-muted ms-1"></span></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="pricingForm" autocomplete="off">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-body">
                                                                <h6 class="mb-3"><?=$languageArray['standard_price_code'][$language]?></h6>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-2">
                                                                        <label for="standardCurrency" class="form-label"><?=$languageArray['currency_code'][$language]?></label>
                                                                        <select class="form-control" id="standardCurrency" style="width: 100%;" data-default-id="<?=$defaultCurrencyId?>">
                                                                            <?php $currencyOptions->data_seek(0); while ($currencyRow = $currencyOptions->fetch_assoc()): ?>
                                                                            <option value="<?= (int) $currencyRow['id'] ?>"><?= htmlspecialchars($currencyRow['currency_code'] . ' - ' . $currencyRow['name']) ?></option>
                                                                            <?php endwhile; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 mb-2">
                                                                        <label for="standardPrice" class="form-label"><?=$languageArray['price_code'][$language]?></label>
                                                                        <input type="number" step="0.01" min="0" class="form-control" id="standardPrice" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="mb-0"><?=$languageArray['customer_prices_code'][$language]?></h6>
                                                            <button type="button" class="btn btn-success btn-sm" id="addPriceRow">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                <?=$languageArray['add_customer_code'][$language]?>
                                                            </button>
                                                        </div>
                                                        <div id="priceRows"></div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=$languageArray['close_code'][$language]?></button>
                                                    <button type="button" class="btn btn-success" id="submitPricing"><?=$languageArray['submit_code'][$language]?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                                        <button type="button" class="btn btn-success" id="submitProducts"><?=$languageArray['submit_code'][$language]?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="errorModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title"><?=$languageArray['error_log_code'][$language]?></h4>
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
                                                                <?php if(hasModulePermission('Master Data', 'Products', ['download_template'])): ?>
                                                                <a href="template/Product_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        <?=$languageArray['download_template_code'][$language]?>
                                                                    </button>
                                                                </a>
                                                                <?php endif; ?>
                                                                <?php if(hasModulePermission('Master Data', 'Products', ['upload_excel'])): ?>
                                                                <button type="button" id="uploadExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    <?=$languageArray['upload_excel_code'][$language]?>
                                                                </button>
                                                                <?php endif; ?>
                                                                <?php if(hasModulePermission('Master Data', 'Products', ['cancelled'])): ?>
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-delete-bin-fill align-middle me-1"></i>
                                                                    <?=$languageArray['delete_code'][$language]?>
                                                                </button>
                                                                <?php endif; ?>
                                                                <?php if(hasModulePermission('Master Data', 'Products', ['create'])): ?>
                                                                <button type="button" id="addProduct" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    <?=$languageArray['add_new_code'][$language]?>
                                                                </button>
                                                                <?php endif; ?>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="productTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th><?=$languageArray['product_code_code'][$language]?></th>
                                                                    <th><?=$languageArray['product_name_code'][$language]?></th>
                                                                    <th><?=$languageArray['description_code'][$language]?></th>
                                                                    <th><?=$languageArray['standard_price_code'][$language]?></th>
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

    <script type="text/html" id="priceRowTemplate">
        <div class="card bg-light price-row mb-2" data-index="{index}">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="partyRow{index}"><?=$languageArray['customer_code'][$language]?></label>
                        <select class="form-control party-select" id="partyRow{index}" style="width: 100%;">
                            <option value=""></option>
                            <?php $customerOptions->data_seek(0); while ($customerRow = $customerOptions->fetch_assoc()): ?>
                            <option value="<?= (int) $customerRow['id'] ?>"><?= htmlspecialchars($customerRow['customer_code'] . ' - ' . $customerRow['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="pricingTypeRow{index}"><?=$languageArray['pricing_type_code'][$language]?></label>
                        <select class="form-control pricing-type" id="pricingTypeRow{index}">
                            <option value="STANDARD"><?=$languageArray['pricing_standard_code'][$language]?></option>
                            <option value="FLOAT"><?=$languageArray['pricing_float_code'][$language]?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="currencyRow{index}"><?=$languageArray['currency_code'][$language]?></label>
                        <select class="form-control currency-select" id="currencyRow{index}" style="width: 100%;">
                            <?php $currencyOptions->data_seek(0); while ($currencyRow = $currencyOptions->fetch_assoc()): ?>
                            <option value="<?= (int) $currencyRow['id'] ?>"><?= $currencyRow['currency_code'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="priceRow{index}"><?=$languageArray['price_code'][$language]?></label>
                        <input type="number" step="0.01" min="0" class="form-control price-input" id="priceRow{index}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-soft-danger btn-sm remove-price-row" data-index="{index}" title="<?=$languageArray['remove_code'][$language]?>"><i class="ri-close-line"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/javascript">
        var table;
        var permissions = <?= json_encode($_SESSION['permissions'] ?? []) ?>;
        var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;
        var pricingLabels = {
            party: <?=json_encode($languageArray['customer_code'][$language])?>,
            pricingType: <?=json_encode($languageArray['pricing_type_code'][$language])?>,
            standard: <?=json_encode($languageArray['pricing_standard_code'][$language])?>,
            special: <?=json_encode($languageArray['pricing_special_code'][$language])?>,
            currency: <?=json_encode($languageArray['currency_code'][$language])?>,
            price: <?=json_encode($languageArray['price_code'][$language])?>,
            placeholder: <?=json_encode($languageArray['please_select_code'][$language])?>,
            remove: <?=json_encode($languageArray['remove_code'][$language])?>,
            currencyRequired: <?=json_encode($languageArray['pricing_currency_required_code'][$language])?>,
            standardPriceInvalid: <?=json_encode($languageArray['pricing_standard_price_invalid_code'][$language])?>,
            partyRequired: <?=json_encode($languageArray['pricing_customer_required_code'][$language])?>,
            partyDuplicate: <?=json_encode($languageArray['pricing_customer_duplicate_code'][$language])?>
        };
        var priceRowCount = 0;

        $(function () {
            $('#selectAllCheckbox').on('change', function() {
                var checkboxes = $('#productTable tbody input[type="checkbox"]');
                checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
            });

            // Initialize all Select2 elements in the modal
            $('#addModal .select2').select2({
                allowClear: true,
                placeholder: "Please Select",
                dropdownParent: $('#addModal') // Ensures dropdown is not cut off
            });

            $('#standardCurrency').select2({ 
                dropdownParent: $('#pricingModal') 
            });

            // Apply custom styling to Select2 elements in addModal
            $('.select2-container .select2-selection--single').css({
                'padding-top': '4px',
                'padding-bottom': '4px',
                'height': 'auto'
            });

            $('.select2-container .select2-selection__arrow').css({
                'padding-top': '33px',
                'height': 'auto'
            });

            table = $("#productTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/modules/product/index.php',
                    'data': { action: 'getAll' }
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
                    { data: 'product_code' },
                    { data: 'name' },
                    { data: 'description' },
                    { 
                        data: 'standard_price', 
                        orderable: false, 
                        render: function (data, type, row) { 
                            return (row.standard_currency_code ? row.standard_currency_code + ' ' : '') + parseFloat(row.standard_price || 0).toFixed(2); 
                        } 
                    },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            if (row.status == '1'){
                                if (!canDo('reactivate')) { return ''; }
                                return '<button title="Reactivate" type="button" id="reactivate'+data+'" onclick="reactivate('+data+')" class="btn btn-warning btn-sm">Reactivate</button>';
                            }else{
                                return 'Active';
                            }
                        }
                    },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            if (row.status == '1'){ return ''; }
                            return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                            (canDo('edit') ? '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> <?=$languageArray['edit_code'][$language] ?></a></li>' : '') +
                            (canDo('manage_pricing') ? '<li><a class="dropdown-item" id="pricing'+data+'" onclick="pricing('+data+')"><i class="ri-price-tag-3-line align-bottom me-2 text-muted"></i> <?=$languageArray['pricing_code'][$language] ?></a></li>' : '') +
                            (canDo('cancelled') ? '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> <?=$languageArray['delete_code'][$language] ?> </a></li>' : '') + '</ul></div>';
                        }
                    }
                ]       
            });
            
            $('#submitProduct').on('click', function(){
                // custom validation for select2
                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                    var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                    // Check if the value is empty
                    if (select2Field.val() === "" || select2Field.val() === null) {
                        select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                        // Add error message if not already present
                        if (select2Container.next('.select2-error').length === 0) {
                            select2Container.after(errorMsg);
                        }

                        isValid = false;
                    } else {
                        select2Container.find('.select2-selection').css('border', ''); // Remove red border
                        select2Container.next('.select2-error').remove(); // Remove error message
                    }
                });

                if($('#productForm').valid()){
                    $('#spinnerLoading').show();
                    $.post('php/modules/product/index.php', $('#productForm').serialize() + '&action=' + ($('#addModal').find('#id').val() ? 'update' : 'create'), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success')
                        {
                            table.ajax.reload();
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            toastr["success"](obj.message, "Success:");
                        }
                        else if(obj.status === 'failed')
                        {
                            $('#spinnerLoading').hide();
                            toastr["error"](obj.message, "Failed:");
                        }
                        else
                        {

                        }
                    });
                }
                // }
            });

            $('#submitProducts').on('click', function(){
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
                    url: 'php/modules/product/index.php?action=upload',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        var obj = JSON.parse(response);
                        if (obj.status === 'success') {
                            $('#spinnerLoading').hide();
                            $('#uploadModal').modal('hide');
                            toastr["success"](obj.message, "Success:");
                            $('#productTable').DataTable().ajax.reload(null, false);
                        } 
                        else if (obj.status === 'failed') {
                            $('#spinnerLoading').hide();
                            toastr["error"](obj.message, "Failed:");
                        } 
                        else if (obj.status === 'error') {
                            $('#spinnerLoading').hide();
                            $('#uploadModal').modal('hide');
                            $('#productTable').DataTable().ajax.reload(null, false);
                            $('#errorModal').find('#errorList').empty();
                            var errorMessage = obj.message;
                            for (var i = 0; i < errorMessage.length; i++) {
                                $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);                            
                            }
                            $('#errorModal').modal('show');
                        } 
                        else {
                            $('#spinnerLoading').hide();
                            toastr["error"]("Failed to save", "Failed:");
                        }
                    }
                });
            });

            $('#addProduct').on('click', function(){
                $('#addModal').find('#id').val("");
                $('#addModal').find('#productCode').val("");
                $('#addModal').find('#productName').val("");
                $('#addModal').find('#description').val("");
                $('#addModal').find('#varianceType').val("");
                $('#addModal').find('#high').val("0");
                $('#addModal').find('#low').val("0");

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

                $('#addModal').modal('show');
                
                $('#productForm').validate({
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
                $('#previewTable').html('');
                $('#fileInput').val('');
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

                $("#productTable tbody input[type='checkbox']").each(function () {
                    if (this.checked) {
                        selectedIds.push($(this).val());
                    }
                });

                if (selectedIds.length > 0) {
                    if (confirm('Are you sure you want to delete these products?')) {
                        $.post('php/modules/product/index.php', {action: 'deleteMulti', userID: selectedIds}, function(data){
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
                    alert("Please select at least one product to delete.");
                    $('#spinnerLoading').hide();
                }     
            });

            // Pricing Code
            $(document).on('change', '.pricing-type', function(){
                applyPricingType($(this).closest('.price-row').data('index'));
            });
            $('#standardPrice, #standardCurrency').on('change input', function(){
                $('#priceRows .price-row').each(function(){
                    applyPricingType($(this).data('index'));
                });
            });

            $('#addPriceRow').on('click', function(){
                addPriceRow({});
            });

            $(document).on('click', '.remove-price-row', function(){
                $(this).closest('.price-row').remove();
            });

            $('#submitPricing').on('click', function(){
                var error = '';
                var seen = {};
                var prices = [];

                if (!$('#standardCurrency').val()) {
                    error = pricingLabels.currencyRequired;
                }
                if (!error && ($('#standardPrice').val() === '' || parseFloat($('#standardPrice').val()) < 0)) {
                    error = pricingLabels.standardPriceInvalid;
                }

                $('#priceRows .price-row').each(function(){
                    var partyId = $(this).find('.party-select').val();
                    if (!partyId) {
                        error = pricingLabels.partyRequired;
                        return false;
                    }
                    if (seen[partyId]) {
                        error = pricingLabels.partyDuplicate;
                        return false;
                    }
                    seen[partyId] = true;
                    prices.push({
                        customerId: partyId,
                        pricingType: $(this).find('.pricing-type').val(),
                        currencyId: $(this).find('.currency-select').val(),
                        price: $(this).find('.price-input').val()
                    });
                });

                if (error) {
                    toastr["error"](error, "Failed:");
                    return;
                }

                $('#spinnerLoading').show();
                $.post('php/modules/product/index.php', {
                    action: 'savePricing',
                    productId: $('#pricingForm').data('recordId'),
                    standardCurrencyId: $('#standardCurrency').val(),
                    standardPrice: $('#standardPrice').val(),
                    prices: JSON.stringify(prices)
                }, function(data){
                    var obj = JSON.parse(data);
                    $('#spinnerLoading').hide();
                    if (obj.status === 'success') {
                        table.ajax.reload(null, false);
                        $('#pricingModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                    } else {
                        toastr["error"](obj.message, "Failed:");
                    }
                });
            });
        });

        function canDo(permission) {
            return isSADMIN || (permissions['Master Data'] && permissions['Master Data']['Products'] && permissions['Master Data']['Products'].includes(permission));
        }

        function edit(id){
            $('#spinnerLoading').show();
            $.post('php/modules/product/index.php', {action: 'get', id: id}, function(data)
            {
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    $('#addModal').find('#id').val(obj.message.id);
                    $('#addModal').find('#productCode').val(obj.message.product_code);
                    $('#addModal').find('#productName').val(obj.message.name);
                    $('#addModal').find('#description').val(obj.message.description);
                    $('#addModal').find('#varianceType').val(obj.message.variance).trigger('change');
                    $('#addModal').find('#high').val(obj.message.high || 0);
                    $('#addModal').find('#low').val(obj.message.low || 0);
                    
                    // Remove Validation Error Message
                    $('#addModal .is-invalid').removeClass('is-invalid');

                    $('#addModal .select2[required]').each(function () {
                        var select2Field = $(this);
                        var select2Container = select2Field.next('.select2-container');
                        
                        select2Container.find('.select2-selection').css('border', ''); // Remove red border
                        select2Container.next('.select2-error').remove(); // Remove error message
                    });
                    
                    $('#addModal').modal('show');

                    $('#productForm').validate({
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
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    toastr["error"](obj.message, "Failed:");
                }
                else{
                    $('#spinnerLoading').hide();
                    toastr["error"](obj.message, "Failed:");
                }
                $('#spinnerLoading').hide();
            });
        }

        function deactivate(id){
            $('#spinnerLoading').show();
            if (confirm('Are you sure you want to delete this product?')) {
                $.post('php/modules/product/index.php', {action: 'delete', id: id}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        toastr["success"](obj.message, "Success:");
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        toastr["error"](obj.message, "Failed:");
                    }
                    else{
                        $('#spinnerLoading').hide();
                        toastr["error"](obj.message, "Failed:");
                    }
                });
            }
            $('#spinnerLoading').hide();
        }

        function pricing(id) {
            $('#spinnerLoading').show();
            $.post('php/modules/product/index.php', {action: 'getPricing', id: id}, function(data){
                var obj = JSON.parse(data);
                var record = obj.message;
                $('#spinnerLoading').hide();
                if (obj.status !== 'success') {
                    toastr["error"](obj.message, "Failed:");
                    return;
                }

                $('#pricingForm').data('recordId', id);
                $('#pricingTitle').text(record.product_code + ' - ' + record.name);
                $('#standardCurrency').val(record.standard_currency_id || $('#standardCurrency').data('defaultId')).trigger('change');
                $('#standardPrice').val(parseFloat(record.standard_price || 0).toFixed(2));

                $('#priceRows').empty();
                $.each(record.rows, function(i, row){
                    addPriceRow(row);
                });

                $('#pricingModal').modal('show');
            });
        }

        function renderPriceRow(index) {
            return $('#priceRowTemplate').html().replace(/\{index\}/g, index);
        }

        function addPriceRow(row) {
            row = row || {};
            var index = priceRowCount++;
            var price = row.price !== undefined ? row.price : $('#standardPrice').val();
            $('#priceRows').append(renderPriceRow(index));

            $('#partyRow' + index).val(row.customer_id || '');
            $('#pricingTypeRow' + index).val(row.pricing_type === 'STANDARD' ? 'STANDARD' : 'FLOAT');
            $('#currencyRow' + index).val(row.currency_id || $('#standardCurrency').val());
            $('#priceRow' + index).val(parseFloat(price || 0).toFixed(2));

            $('#partyRow' + index).select2({ 
                allowClear: true, 
                placeholder: pricingLabels.placeholder, 
                dropdownParent: $('#pricingModal') 
            });
            $('#currencyRow' + index).select2({ 
                dropdownParent: $('#pricingModal') 
            });

            // Apply custom styling to Select2 elements in addModal
            $('.select2-container .select2-selection--single').css({
                'padding-top': '4px',
                'padding-bottom': '4px',
                'height': 'auto'
            });

            $('.select2-container .select2-selection__arrow').css({
                'padding-top': '33px',
                'height': 'auto'
            });

            applyPricingType(index);
        }

        // Standard rows take the product's standard price and currency and are read-only; Float rows are editable
        function applyPricingType(index) {
            var isStandard = $('#pricingTypeRow' + index).val() === 'STANDARD';
            if (isStandard) {
                $('#priceRow' + index).val(parseFloat($('#standardPrice').val() || 0).toFixed(2));
                $('#currencyRow' + index).val($('#standardCurrency').val());
            }
            $('#priceRow' + index).prop('readonly', isStandard);
            $('#currencyRow' + index).prop('disabled', isStandard).trigger('change.select2');
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

            // Ensure we handle cases where there may be less than 3 columns
            while (headers.length < 3) {
                headers.push(''); // Adding empty headers to reach 3 columns
            }

            // Create HTML table headers
            var htmlTable = '<table style="width:30%;"><thead><tr>';
            $.each(headers, function(index, header) {
                htmlTable += '<th>' + header + '</th>';
            });
            htmlTable += '</tr></thead><tbody>';

            // Iterate over the data and create table rows
            for (var i = 1; i < jsonData.length; i++) {
                htmlTable += '<tr>';
                var rowData = jsonData[i];

                // Ensure we handle cases where there may be less than 3 cells in a row
                while (rowData.length < 3) {
                    rowData.push(''); // Adding empty cells to reach 3 columns
                }

                for (var j = 0; j < 3; j++) {
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

            $('#previewTable').html(htmlTable);
        }

        function reactivate(id) {
            if (confirm('Do you want to reactivate this item?')) {
                $('#spinnerLoading').show();
                $.post('php/reactivateMasterData.php', {userID: id, type: "Product"}, function(data){
                    var obj = JSON.parse(data);

                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        toastr["success"](obj.message, "Success:");
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        toastr["error"](obj.message, "Failed:");
                    }
                    else{
                        $('#spinnerLoading').hide();
                        toastr["error"](obj.message, "Failed:");
                    }

                    $('#spinnerLoading').hide();
                });
            }

            $('#spinnerLoading').hide();
        }
    </script>
    </body>

    </html>