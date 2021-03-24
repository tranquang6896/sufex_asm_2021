<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Sufex Admin</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $this->Url->build('/', true); ?>img/favicon.ico">
    <script async defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyDExlzhnegRnzSZRxMNy-7_a56CYBmXssY&libraries=&v=weekly"></script>
    <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Font Awesome 5 Brands';
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.eot");
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.eot?#iefix") format("embedded-opentype"), url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.woff2") format("woff2"), url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.woff") format("woff"), url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.ttf") format("truetype"), url("<?=$this->Url->build('/', true)?>fonts/fa-brands-400.svg#fontawesome") format("svg");
        }

        @font-face {
            font-family: 'Font Awesome 5 Free';
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.eot");
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.eot?#iefix") format("embedded-opentype"), url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.woff2") format("woff2"), url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.woff") format("woff"), url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.ttf") format("truetype"), url("<?=$this->Url->build('/', true)?>fonts/fa-regular-400.svg#fontawesome") format("svg");
        }

        @font-face {
            font-family: 'Font Awesome 5 Free';
            font-style: normal;
            font-weight: 900;
            font-display: block;
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.eot");
            src: url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.eot?#iefix") format("embedded-opentype"), url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.woff2") format("woff2"), url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.woff") format("woff"), url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.ttf") format("truetype"), url("<?=$this->Url->build('/', true)?>fonts/fa-solid-900.svg#fontawesome") format("svg");
        }
    </style>

    <!-- Custom fonts for this template -->
    <?php
        echo $this->Html->script('jquery.min.js') . PHP_EOL;
        echo $this->Html->css('admin/all.min.css') . PHP_EOL;
        echo $this->Html->css('admin/sb-admin-2.min.css?'.date('YmdHis')) . PHP_EOL;
        echo $this->Html->css('admin/dataTables.bootstrap4.min.css') . PHP_EOL;
        echo $this->Html->css('calendar/admin-fullcalendar.css') . PHP_EOL;
        // echo $this->Html->css('calendar/datepicker.css') . PHP_EOL;
        echo $this->Html->css('calendar/jquery-ui.css') . PHP_EOL;
        echo $this->Html->css('admin/common.css') . PHP_EOL;
        if(mb_strtolower($this->request->getParam('action')) == 'gallery'){
            echo $this->Html->css('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') . PHP_EOL;
            echo $this->Html->css('venobox.css') . PHP_EOL;
        }
        echo $this->Html->css('report.css?v='.date('YmdHis')) . PHP_EOL;

    ?>

    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script type="text/javascript">
        let __baseUrl = "<?php echo $this->Url->build('/', true); ?>";
        let __csrfToken = '<?php echo $this->request->getParam('_csrfToken') ?>';
    </script>
    <?php echo $this->fetch('head-end'); ?>
    <!-- assign define to js files -->
    <?= $this->element('assign'); ?>
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex " href="javascript:void(0)">
            <div class="sidebar-brand-icon rotate-n-15">
                <?php echo $this->Html->image('admin/logo-sufex.png', ['alt' => 'logo']); ?>
            </div>
        </a>
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Tables -->
        <?php if(isset($auth) && $auth->Position != 'Area Leader'):?>
        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "Staff", "action" => "index"]) ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Staff Management</span>
            </a>
        </li>
        <?php endif; ?>
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Tables -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "Customer", "action" => "index"]) ?>">
                <i class="far fa-address-card"></i>
                <span>Customer Management</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Tables -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "Schedule", "action" => "index"]) ?>">
                <i class="far fa-calendar-check"></i>
                <span>Check Staff's Schedules</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Tables -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "Visit", "action" => "index"]) ?>">
                <i class="far fa-calendar-alt"></i>
                <span>Visit Log</span>
            </a>
        </li>
        <?php if(isset($auth) && $auth->Position != 'Area Leader'):?>
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Tables -->

        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "FaceImage", "action" => "index"]) ?>">
                <i class="far fa-image"></i>
                <span>Face Image</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Tables -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= $this->Url->build(["controller" => "Calendar", "action" => "index"]) ?>">
                <i class="far fa-calendar-alt" style="color:#b0b0b0"></i>
                <span style="color:#b0b0b0">Working Schedules</span>
            </a>
        </li>
        <?php endif; ?>
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <h2 class="title">ASM System Ver 1.01 for Sufex</h2>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Alerts -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small" style="font-size:14px">
                                <i class="fas fa-user mr-1"></i>
                                <?php if(isset($auth)) echo $auth->StaffID . " - ". $auth->Name; ?>
                            </span>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <?php echo $this->fetch('content'); ?>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                  Produced by ALSOK 
           <span style="float:right; position: absolute;right: 20px;">Copyright © 2020 by Netsurf Vietnam. All Rights Reserved</span> 
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <!-- <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button> -->
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" style="width:100px" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" style="width:100px" href="<?= $this->Url->build(["controller" => "User", "action" => "logout"]) ?>">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<?= $this->Html->script('calendar/jquery.min.js') . PHP_EOL ?>
<?= $this->Html->script('jquery-ui.min.js') . PHP_EOL ?>
<?= $this->Html->script('admin/draggable-modal.js') . PHP_EOL ?>
<?= $this->Html->script('admin/bootstrap.bundle.min.js') . PHP_EOL ?>

<!-- Core plugin JavaScript-->
<?= $this->Html->script('admin/jquery.easing.min.js') . PHP_EOL ?>

<!-- Custom scripts for all pages-->
<?= $this->Html->script('admin/sb-admin-2.min.js?v='.date('YmdHis')) . PHP_EOL ?>

<!-- Page level plugins -->
<?= $this->Html->script('admin/jquery.dataTables.min.js') . PHP_EOL ?>
<?= $this->Html->script('admin/dataTables.bootstrap4.min.js') . PHP_EOL ?>

<!-- Alert plugins -->
<script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<!-- Page level custom scripts -->
<?= $this->Html->script('admin/datatables-demo.js') . PHP_EOL ?>

<?php
    echo $this->Html->script('calendar/moment.min.js', ['block' => 'scriptBottom']);
    echo $this->Html->script('calendar/fullcalendar.js', ['block' => 'scriptBottom']);
    echo $this->Html->script('jquery.ui.monthpicker.js', ['block' => 'scriptBottom']);
    if(mb_strtolower($this->request->getParam('action')) == 'gallery'){
        echo $this->Html->script('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js', ['block' => 'scriptBottom']);
        echo $this->Html->script('venobox.min.js', ['block' => 'scriptBottom']);
    }
?>

<!-- Modal Report -->
<?php echo $this->Html->script('admin/report.js?v='. date('YmdHis'), ['block' => 'scriptBottom']); ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" />

<!--fetch script at bottom at page-->
<?= $this->fetch('scriptBottom'); ?>

</script>
</body>
</html>
