<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="<?php $this->Url->build("/", true); ?>img/favicon.ico">
    <!-- App title -->
    <title>ASM Timecard System</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- <link href="<?php echo $this->Url->build("/",true) ;?>css/style.css?v=3" rel="stylesheet" /> -->
    <?php
    echo $this->Html->css("style-staffs.css?v=" . date('ymdhis')) . PHP_EOL;
    echo $this->Html->script('jquery.min.js') . PHP_EOL;
    ?>
    <script type="text/javascript">
        var baseUrl = "<?php echo $this->Url->build('/', true); ?>";
        var csrfToken = '<?php echo $this->request->getParam('_csrfToken') ?>';
    </script>
    <style>
        .video {
            width: 100%;
            /* max-height: 500px; */
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
                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div id="staff">
                    <p><img alt="" src="<?php echo $this->Url->build("/",true) ;?>img/staff.png?v=1" /></p>

                    <p>Staff Name</p>

                    <a href="#"><span></span></a>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="div-right">
                <video class="video" id="video" autoplay muted playsinline></video>

                <div class="headoff">HEAD OFFICE</div>

                <div class="form">
                    <input type="password" class="w-100" placeholder="Enter password" />
                </div>

                <div class="form">
                    <button class="w-49 f-l button1">Check in</button><button class="w-49 f-r" onclick="location.href='index.html'">
                        Check out
                    </button>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="footer">
            Produced by ALSOK
            <span style="position: absolute;right: 20px;/* bottom: -10px; */" class="f-r">Copyright © 2020 by Netsurf Vietnam. All Rights Reserved</span>
        </div>

        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    </div>

    <?php
    echo $this->Html->script("capture.js") . PHP_EOL;
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

    <script>
        const date = document.getElementById("date");

        function updateDate() {
            let newDate = new Date();
            let year = newDate.getFullYear();
            let month = newDate.getMonth() + 1;
            let days = newDate.getDate();
            let hour = newDate.getHours();
            let mins = newDate.getMinutes();
            let sec = newDate.getSeconds();
            let clockJSRead = `${year}/${month}/${days}  ${hour}:${mins}:${sec}`;
            date.textContent = clockJSRead;
        }
        setInterval(updateDate, 1000);
        updateDate();
    </script>
</body>

</html>