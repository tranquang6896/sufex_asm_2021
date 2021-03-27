<?php

echo $this->Html->css('calendar/fullcalendar.css', ['block' => 'head-end']) . PHP_EOL;
echo $this->Html->css('calendar/datepicker.css', ['block' => 'head-end']) . PHP_EOL;
echo $this->Html->css('bootstrap-datetimepicker.min.css?v=' . date('ymdhis'), ['block' => 'head-end']) . PHP_EOL;
echo $this->Html->css('calendar/jquery-ui.css', ['block' => 'head-end']) . PHP_EOL;
//echo $this->Html->css('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css', ['block' => 'head-end']) . PHP_EOL;
echo $this->Html->css('admin/style_schedule.css', ['block' => 'head-end']) . PHP_EOL;

?>

<style>
    .Staff-ID {
        position: relative;
    }

    .tooltip-distance {
        display: none;
        position: absolute;
        z-index: 100;
        border: 1px;
        background-color: white;
        border: 1px solid #2c7090;
        padding: 3px;
        color: #2c7090;
        top: 15px;
        left: 65px;
        width: 300px;
        text-align: left;
    }

    .Staff-ID:hover .tooltip-distance {
        display: block;
    }
</style>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A==" crossorigin="anonymous" />
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDExlzhnegRnzSZRxMNy-7_a56CYBmXssY&libraries=&v=weekly"></script>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Check Staff's Schedules</h6>
    </div>
    <div class="card-body">
        <div class="pt-1"></div>

        <div class="row ">

            <div class="col-md-12 col-sm-10 mx-auto text-center form p-8">
                <div>
                    <form class="form-inline" action="#">

                        <span style="font-size:16px; margin-right:5px">Alert Time</span>
                        <input type="text" class="form-control mr-2" name="timepicker" value="08:00" id="timepicker_alert" size="10" placeholder="">

                        <input type="hidden" class="form-control " id="default_timepicker" value="">

                        <input type="text" class="form-control mr-2" size="30" placeholder="Email1">
                        <input type="text" class="form-control mr-2" size="30" placeholder="Email2">
                        <button type="button" id="submitAlert" class="rounded-pill btn btn-primary ">
                            Submit
                        </button>

                    </form>
                    <hr />
                    <form class="form-inline" action="#">
                        <div class="form-group  mb-2">
                            <select id="multiple-select" name="staffIds" class="sStaffID form-control " style="width:260px" place>
                                <option value=""></option>
                                <?php foreach ($staffIds as $staffId => $value) : ?>
                                    <option value="<?= $staffId ?>"><?= $value ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group mx-sm-3 col-xs-2 mb-2">
                            <span style="color:#000;margin-right:3px;font-size:14px">From</span>
                            <input type="text" class="form-control" value="<?= @$params['datepicker'] ?>" name="datepicker" id="datepicker_date" size="10" placeholder="Date">
                            <input type="hidden" class="form-control" id="default_datepicker" value="">
                        </div>
                        <div class="form-group mx-sm-3 col-xs-2 mb-2">
                            <span style="color:#000;margin-right:3px;font-size:14px">To</span>
                            <input type="text" class="form-control" value="<?= @$params['datepicker'] ?>" name="datepicker" id="datepicker_date_to" size="10" placeholder="Date">
                            <input type="hidden" class="form-control" id="default_to_datepicker" value="">
                        </div>
                        <button type="button" id="filterSchedule" class="rounded-pill btn btn-primary mb-2">
                            Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>



    </div>
    <div class="card-body">
        <div class="row ">
            <div class="col-md-8">
                <div class="table ">
                    <table class="table table-bordered" id="serverDataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Customer Name</th>
                                <th>GPS</th>
                                <th>Distance</th>
                                <th>Report</th>
                                <th>Face Image</th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4 p-0">
                <div id="staffScheduleMap" style="min-height: 550px; height: 550px"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="Auth" value="<?php echo $auth->StaffID; ?>">
</div>

<!-- temp values -->
<?php
$col = 3;
$dir = 'desc';
if (isset($sort)) {
    if (isset($sort['col']) && isset($sort['dir'])) {
        if (strpos($sort['col'], 'ID') !== false) {
            $col = 1;
        } else if ($sort['col'] == 'Staff Name') {
            $col = 2;
        } else if ($sort['col'] == 'Customer Name') {
            $col = 5;
        } else {
            $col = 3;
        }
        $dir = $sort['dir'];
    }
}
?>
<input type="hidden" id="currIndexSort" value="<?php echo $col; ?>">
<input type="hidden" id="currDirSort" value="<?php echo $dir; ?>">

<?php echo $this->element('Admin/popup_view_image'); ?>
<?php echo $this->element('Admin/popup_info_staff'); ?>
<?php echo $this->element('Admin/popup_info_customer'); ?>
<!-- popup for gps -->
<?php echo $this->element('Admin/popup_gps'); ?>
<!-- popup for report -->
<?php echo $this->element('Admin/popup_event_admin'); ?>
<!-- popup for face -->
<?php echo $this->element('Admin/popup_face_admin'); ?>

<?php
echo $this->Html->script('calendar/moment.min.js', ['block' => 'scriptBottom']);
echo $this->Html->script('calendar/fullcalendar.js', ['block' => 'scriptBottom']);
echo $this->Html->script('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js', ['block' => 'scriptBottom']);
echo $this->Html->script('bootstrap-datetimepicker.min.js?v=' . date('ymdhis'), ['block' => 'scriptBottom']);
echo $this->Html->script('jquery.ui.monthpicker.js', ['block' => 'scriptBottom']);
echo $this->Html->script('admin/schedule/index.js?v=' . date('YmdHis'), ['block' => 'scriptBottom']);
?>

<script type="text/template" id="tplSecCheck">
    <legend class="legend-report category-report form-jp">__category-jp__</legend>
    <legend class="legend-report category-report form-vn" style="display:none">__category-vn__</legend>
    __checkboxs__
</script>

<script type="text/template" id="tplCheckbox">
    <label class="label-report form-jp"><input class="checkbox-report" type="checkbox" name="Check" value="__id__" __checked__/>__checkcode__ - __detail-jp__</label>
    <label class="label-report form-vn" style="display:none"><input class="checkbox-report" type="checkbox" name="Check" value="__id__" __checked__/>__checkcode__ - __detail-vn__</label>
</script>