<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="<?php echo $this->Url->build("/", true); ?>img/favicon.ico">
    <!-- App title -->
    <title>ASM Timecard System</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Poppins:400,500&amp;display=swap"/>

    <?php
    // echo $this->Html->css('bootstrap.min.css') . PHP_EOL;
    echo $this->Html->css("style-staffs.css?v=" . date('ymdhis')) . PHP_EOL;
    echo $this->Html->css("icons.css") . PHP_EOL;
    echo $this->Html->script('jquery.min.js') . PHP_EOL;
    ?>
    <script type="text/javascript">
        var baseUrl = "<?php echo $this->Url->build('/', true); ?>";
        var csrfToken = '<?php echo $this->request->getParam('_csrfToken') ?>';
    </script>
    <style>
        .video {
            width: 100%;
            max-height: 620px;
            margin-top: 5px
        }
    </style>
</head>

<body>
    <div id="page-container">
        <div class="top-menu">
            <div href="index.html" class="navbar-brand">
                <img src="<?php echo $this->Url->build("/",true) ;?>img/logo.png" class="media-object" alt="" />
                <div class="title">ASM System Ver 1.01 for Sufex</div>
            </div>

            <div id="date" class="time"></div>
        </div>

        <div id="content" class="content">
            <div class="div-left">

                <?php foreach($staffs as $staff):?>
                    <div class="staff-avatar" data-active="false" data-staffid="<?php echo $staff->StaffID; ?>">
                        <?php if($staff->Image != ""):?> 
                            <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>files/StaffImage/<?php echo $staff->Image?>" /></p>
                        <?php endif; ?>

                        <p><?php echo $staff->Name; ?></p>
                    </div>
                <?php endforeach;?>

                <div class="clearfix"></div>
            </div>

            <div class="div-right">
                <video class="video" id="video" autoplay muted playsinline></video>

                <div class="headoff">HEAD OFFICE</div>

                <div class="form form-password">
                    <input type="password" name="Password" class="w-100 input-password" placeholder="Enter password" />
                    <span class="show-password" data-type="show"><i class="fas fa-eye"></i></span>
                </div>

                <div class="form">
                    <button class="w-49 f-l button1" id="btnSubmitCheckin">Check in</button>
                    <button class="w-49 f-r" id="btnSubmitCheckout">Check out</button>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="footer">
            Produced by ALSOK
            <span style="position: absolute;right: 20px;/* bottom: -10px; */" class="f-r">Copyright © 2020 by Netsurf Vietnam. All Rights Reserved</span>
        </div>

        <!-- <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a> -->
    </div>

    <!-- START__hidden vars -->
    <input type="hidden" id="currentCoord" value="none">
    <p style="display: none;" id="btnInsertCheckin"></p>
    <p style="display: none;" id="btnInsertCheckout"></p>
    <input type="hidden" id="staffID" value="">
    <input type="hidden" id="pwStaff" value="">
    <canvas id="canvas" style="display: none;"></canvas>
    <!-- END__hidden_vars -->

    <!-- START__elements -->
    <?php echo $this->element('Mypage/popup_sign_homepage'); ?>
    <?php echo $this->element('Mypage/popup_look_camera_homepage'); ?>
    <!-- END__elements -->

    <?php
    echo $this->Html->script('bootstrap.bundle.min.js') . PHP_EOL;
    echo $this->Html->script("bootstrap-datepicker.min.js") . PHP_EOL;
    echo $this->Html->script("bootstrap-timepicker.js") . PHP_EOL;
    echo $this->Html->script("plugins/bootstrap-daterangepicker/daterangepicker.js") . PHP_EOL;
    echo $this->Html->script("bootstrap-datetimepicker.js") . PHP_EOL;
    echo $this->Html->script('detect.js') . PHP_EOL;
    echo $this->Html->script('fastclick.js') . PHP_EOL;
    echo $this->Html->script('jquery.blockUI.js') . PHP_EOL;
    echo $this->Html->script('waves.js') . PHP_EOL;
    echo $this->Html->script('jquery.slimscroll.js') . PHP_EOL;
    echo $this->Html->script('jquery.scrollTo.min.js') . PHP_EOL;
    echo $this->Html->script('plugins/moment/moment.js') . PHP_EOL;
    echo $this->Html->script('jquery.core.js') . PHP_EOL;
    echo $this->Html->script('jquery.app.js') . PHP_EOL;
    echo $this->Html->script('sweetalert.min.js') . PHP_EOL;
    echo $this->Html->script('venobox.min.js') . PHP_EOL;
    ?>

    <!-- APP -->
    <?php
    echo $this->Html->script("user-common.js") . PHP_EOL;
    echo $this->Html->script("user-home.js") . PHP_EOL;
    ?>

    <script>
        const date = document.getElementById("date");

        function updateDate() {
           
            let clockJSRead = moment().format("YYYY/MM/DD HH:mm:ss");
            date.textContent = clockJSRead;
        }
        setInterval(updateDate, 1000);
        updateDate();
    </script>
</body>

</html>