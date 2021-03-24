<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">

<head>
    <meta name="google" content="notranslate" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>ALSOK - ASM System Ver 1.0</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $this->Url->build('/', true); ?>img/favicon.ico">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDExlzhnegRnzSZRxMNy-7_a56CYBmXssY&libraries=&v=weekly"></script>
    <!-- CSS -->
    <?php
    echo $this->Html->css('bootstrap.min.css') . PHP_EOL;
    echo $this->Html->css('icons.css?' . date('YmdHis')) . PHP_EOL;
    echo $this->Html->css('style-user.css?' . date('YmdHis')) . PHP_EOL;
    echo $this->Html->css('report.css?v=7' . date('YmdHis')) . PHP_EOL;
    echo $this->Html->css('venobox.css') . PHP_EOL;
    // JS
    echo $this->Html->script('jquery.min.js') . PHP_EOL;
    ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Comfortaa:300:400:500:600:700|Prompt:300:400:500:600:700|Poppins:400,500&amp;display=swap" />

    <script type="text/javascript">
        var __baseUrl = "<?php echo $this->Url->build('/', true); ?>";
        var csrfToken = '<?php echo $this->request->getParam('_csrfToken') ?>';
    </script>
    <style>
    .img-flag{
        width: 30px !important;
        height: 15px;
        float: right;
        margin-left: 10px;
    }
    .flag-en {
        height: 14px !important;
    }
    </style>

    <?php echo $this->fetch('head-end'); ?>

</head>

<body id="toppage-body">
    <div class="bg-img1">
        <div class="header" style="height:8vh">
            <!-- language -->
            <?php echo $this->Html->image('lang/en.png', ['alt' => 'English','class'=>'img-flag flag-en', 'url' => ['controller' => 'App', 'action' => 'changeLanguage', 'en_US']]); ?>
            <?php echo $this->Html->image('lang/jp.png', ['alt' => 'Japan','class'=>'img-flag', 'url' => ['controller' => 'App', 'action' => 'changeLanguage', 'jp_JP']]); ?>
            <?php echo $this->Html->image('lang/vn.png', ['alt' => 'Vietnam','class'=>'img-flag', 'url' => ['controller' => 'App', 'action' => 'changeLanguage', 'vn_VN']]); ?>
            <!-- text logo -->
            <a href="<?php echo $this->Url->build('/', true); ?>"><img src="<?php echo $this->Url->build('/', true); ?>img/text.png?v=123" alt="logo" /></a>
        </div>
        <div class="main-highlight" style="height:46vh">
            <?php echo $this->fetch('content'); ?>
            <div class="copy" style="height:3vh">© NetSurf Co., Ltd.</div>
            <canvas id="canvas" style="display: none;"></canvas>
        </div>
    </div>

    <!-- elements -->
    <?php echo $this->element('Mypage/popup_sign'); ?>
    <?php echo $this->element('Mypage/popup_report'); ?>
    <?php echo $this->element('Mypage/popup_view_image'); ?>
    <?php echo $this->element('Mypage/popup_look_camera'); ?>
    <?php echo $this->element('Mypage/div_dictionary'); ?>

    <!-- jQuery  -->
    <?php
    echo $this->Html->script('bootstrap.bundle.min.js') . PHP_EOL;
    echo $this->Html->script('bootstrap.bundle.min.js') . PHP_EOL;
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
    <!-- App js -->
    <?php
    echo $this->Html->script('attached-picture.js?v='. date('YmdHis')) . PHP_EOL;
    echo $this->Html->script('mypage.js?v='. date('YmdHis')) . PHP_EOL;

    if(mb_strtolower($this->request->getParam('action')) == 'index'):
        echo $this->Html->script('capture.js?v='.date('YmdHis')) . PHP_EOL;
    endif;
    ?>

    <?php echo $this->fetch('body-end'); ?>

    <script>
        // Digital Clock
        setInterval(() => {
            let time = new Date();
            let hours = time.getHours();
            let minutes = time.getMinutes();
            let seconds = time.getSeconds();

            // Prepending 0 if less than 10
            hours = hours >= 10 ? hours : "0" + hours;
            minutes = minutes >= 10 ? minutes : "0" + minutes;
            seconds = seconds >= 10 ? seconds : "0" + seconds;

            // Adding the time in the DOM
            document.getElementById(
                "digital-clock"
            ).innerHTML = `${hours}:${minutes}:${seconds}`;

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '/' + mm + '/' + dd;
            $('#today').html(today)
        }, 1000);

        $(document).ready(function() {
            // $('#CurrentLocation').on('click', function(e){
            //     e.preventDefault()
            //     // $('#exampleModal').modal('show');
            //     console.log('ok')
            // })
        })
    </script>



</body>

</html>
