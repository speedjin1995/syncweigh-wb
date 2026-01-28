<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

    <head>
        
        <title>Purge Data | SPMT - Weighing System</title>
        <?php include 'layouts/title-meta.php'; ?>

        <!-- swiper css -->
        <link rel="stylesheet" href="assets/libs/swiper/swiper-bundle.min.css">
        <!-- Include jQuery library -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Include jQuery Validate plugin -->
        <script src="plugins/jquery-validation/jquery.validate.min.js"></script>

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
                            <div class="card bg-light">
                                <div class="card-body">
                                    <form role="form" id="prePrintForm">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label for="fromDateSearch" class="form-label">From Date</label>
                                                    <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch" name="fromDateSearch" required>
                                                </div>
                                            </div><!--end col-->
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label for="toDateSearch" class="form-label">To Date</label>
                                                    <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch" name="toDateSearch" required>
                                                </div>
                                            </div><!--end col-->
                                        </div>
                                        <div class="row">
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="button" id="submitPrePrint">Purge</button>
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
        <!-- END layout-wrapper -->

        

        <?php include 'layouts/customizer.php'; ?>
        <?php include 'layouts/vendor-scripts.php'; ?>
        <!-- apexcharts -->
        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <!-- Vector map-->
        <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
        <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>
        <!--Swiper slider js-->
        <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
        <!-- Dashboard init -->
        <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
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
            $(function () {
                const today = new Date();
                const tomorrow = new Date(today);
                const yesterday = new Date(today);
                const last30 = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                yesterday.setDate(yesterday.getDate() - 1);
                last30.setDate(today.getDate() - 30);

                $('#fromDateSearch').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: ''
                });

                $('#toDateSearch').flatpickr({
                    dateFormat: "d-m-Y",
                    defaultDate: ''
                });

                $('#submitPrePrint').on('click', function(){
                    if($('#prePrintForm').valid()){
                        $('#spinnerLoading').show();

                        $.post('php/purgeData.php', $('#prePrintForm').serialize(), function(data){
                            var obj = JSON.parse(data);
                            debugger;

                            if(obj.status === 'success'){
                                window.location.reload();

                                $('#spinnerLoading').hide();
                            }
                            else if(obj.status === 'failed'){
                                $("#failBtn").attr('data-toast-text', obj.message );
                                $("#failBtn").click();
                                window.location.reload();
                            }
                            else{
                                $("#failBtn").attr('data-toast-text', "Something wrong when print");
                                $("#failBtn").click();
                                window.location.reload();
                            }
                        });
                    }
                });

                $.validator.setDefaults({
                    submitHandler: function () {
                        $('#spinnerLoading').show();
                        $.post('php/purgeData.php', $('#profileForm').serialize(), function(data){
                            var obj = JSON.parse(data); 
                            
                            if(obj.status === 'success'){
                                toastr["success"](obj.message, "Success:");
                                window.location.reload();
                            }
                            else if(obj.status === 'failed'){
                                toastr["error"](obj.message, "Failed:");
                                $('#spinnerLoading').hide();
                            }
                            else{
                                toastr["error"]("Failed to update ports", "Failed:");
                                $('#spinnerLoading').hide();
                            }
                        });
                    }
                });
                
                $('#profileForm').validate({
                    rules: {
                        text: {
                            required: true
                        }
                    },
                    messages: {
                        text: {
                            required: "Please fill in this field"
                        }
                    },
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
        </script>
    </body>
</html>