<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Weighing | Synctronix - Weighing System</title>
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
                                                            Search</button>
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
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Customer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="supplierForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class=" row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="supplierCode" class="col-sm-4 col-form-label">Supplier Code</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="supplierCode" name="supplierCode" placeholder="Supplier Code" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="companyRegNo" class="col-sm-4 col-form-label">Company Reg No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4">
                                                                                                <input type="text" class="form-control" id="companyRegNo" name="companyRegNo">
                                                                                            </div>
                                                                                            <div class="col-sm-8">
                                                                                                <div class="row">
                                                                                                    <label for="newRegNo" class="col-sm-4 col-form-label">New Reg No</label>
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
                                                                                    <label for="companyName" class="col-sm-4 col-form-label">Company Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Customer Code">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine1" class="col-sm-4 col-form-label">Address Line 1</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine1" name="addressLine1" placeholder="Address Line 1">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine2" class="col-sm-4 col-form-label">Address Line 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine2" name="addressLine2" placeholder="Address Line 2">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="addressLine3" class="col-sm-4 col-form-label">Address Line 3</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="addressLine3" name="addressLine3" placeholder="Address Line 3">
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
                                                                                    <label for="phoneNo" class="col-sm-4 col-form-label">Phone No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="faxNo" class="col-sm-4 col-form-label">Fax No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="faxNo" name="faxNo" placeholder="Fax No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="contactName" class="col-sm-4 col-form-label">Contact Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="contactName" name="contactName" placeholder="Contact Name">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="icNo" class="col-sm-4 col-form-label">I/C No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="icNo" name="icNo" placeholder="I/C No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="tinNo" class="col-sm-4 col-form-label">Tin No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="tinNo" name="tinNo" placeholder="Tin No">
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
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-success" id="submitCustomer">Submit</button>
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
                                                        <h4 class="modal-title">Upload Excel File</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" id="fileInput">
                                                        <button type="button" id="previewButton">Preview Data</button>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-success" id="submitWeights">Save changes</button>
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
                                                                <h5 class="card-title mb-0">Previous Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="template/Supplier_Template.xlsx" download>
                                                                    <button type="button" class="btn btn-info waves-effect waves-light">
                                                                        <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                        Download Template 
                                                                    </button>
                                                                </a>
                                                                <button type="button" id="uploadExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Upload Excel
                                                                </button>
                                                                <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="fa-solid fa-ban align-middle me-1"></i>
                                                                    Delete Supplier
                                                                </button>
                                                                <button type="button" id="addSupplier" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New Supplier
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="supplierTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Supplier Code</th>
                                                                    <th>Company Reg No</th>
                                                                    <th>New Reg No</th>
                                                                    <th>Company Name</th>
                                                                    <th>Address Line 1</th>
                                                                    <th>Address Line 2</th>
                                                                    <th>Address Line 3</th>
                                                                    <th>Phone No</th>
                                                                    <th>Fax No</th>
                                                                    <th>Contact Name</th>
                                                                    <th>I/C No</th>
                                                                    <th>Tin No</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
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

<script type="text/javascript">

var table;

$(function () {

    table = $("#supplierTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadSupplier.php'
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
            { data: 'supplier_code' },
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
                render: function ( data, type, row ) {
                    // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-success btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                    return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                    '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                    '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                    '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete </a></li></ul></div>';
                }
            }
        ]       
    });
    
    // $.validator.setDefaults({
    //     submitHandler: function () {
        $('#submitCustomer').on('click', function(){
            if($('#supplierForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/supplier.php', $('#supplierForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success'){
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
            url: 'php/uploadSuppliers.php',
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
                    $('#supplierTable').DataTable().ajax.reload(null, false);
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
                    $('#supplierTable').DataTable().ajax.reload(null, false);
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

    $('#addSupplier').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#supplierCode').val("");
        $('#addModal').find('#companyName').val("");
        $('#addModal').find('#companyRegNo').val("");
        $('#addModal').find('#newRegNo').val("");
        $('#addModal').find('#addressLine1').val("");
        $('#addModal').find('#addressLine2').val("");
        $('#addModal').find('#addressLine3').val("");
        $('#addModal').find('#phoneNo').val("");
        $('#addModal').find('#faxNo').val("");
        $('#addModal').find('#contactName').val("");
        $('#addModal').find('#icNo').val("");
        $('#addModal').find('#tinNo').val("");

        // Remove Validation Error Message
        $('#addModal .is-invalid').removeClass('is-invalid');

        $('#addModal').modal('show');
        
        $('#supplierForm').validate({
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

        $("#supplierTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteSupplier.php', {userID: selectedIds, type: 'MULTI'}, function(data){
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
            alert("Please select at least one supplier to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

$('#supplierForm').validate({
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
    while (headers.length < 12) {
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
        while (rowData.length < 12) {
            rowData.push(''); // Adding empty cells to reach 15 columns
        }

        for (var j = 0; j < 12; j++) {
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

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getSupplier.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
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

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal').modal('show');
            
            // $('#customerForm').validate({
            //     errorElement: 'span',
            //     errorPlacement: function (error, element) {
            //         error.addClass('invalid-feedback');
            //         element.closest('.form-group').append(error);
            //     },
            //     highlight: function (element, errorClass, validClass) {
            //         $(element).addClass('is-invalid');
            //     },
            //     unhighlight: function (element, errorClass, validClass) {
            //         $(element).removeClass('is-invalid');
            //     }
            // });
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
        $.post('php/deleteSupplier.php', {userID: id}, function(data){
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

function reactivate(id) {
  if (confirm('Do you want to reactivate this item?')) {
    $('#spinnerLoading').show();
    $.post('php/reactivateMasterData.php', {userID: id, type: "Supplier"}, function(data){
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
</script>
</body>

</html>