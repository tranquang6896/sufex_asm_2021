<?php
    echo $this->Html->css('calendar/datepicker.css', ['block' => 'head-end']) . PHP_EOL;
    echo $this->Html->css('calendar/jquery-ui.css', ['block' => 'head-end']) . PHP_EOL;
    echo $this->Html->css('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') . PHP_EOL;
?>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Customer Management</h6>
        <button type="button" class="btn btn-success rounded-pill float-right" id="show-modal-customer"><i class="fas fa-plus"></i>Add</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="serverDataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="w-td3 text-center">No.</th>
                    <th class="w-td2">CustomerID</th>
                    <th>Name</th>
                    <th class="w-td2">Area</th>
                    <th class="w-td2">Region</th>
                    <th>Address</th>
                    <th class="w-td2">Longitude</th>
                    <th class="w-td2">Latitude</th>
                    <th class="w-td2">ImplementDate</th>
                    <th class="text-center w-10a"></th>
                </tr>
                </thead>
                <tbody id="tblCustomer">
                <!--datatable-->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- temp values -->
<?php
    $col = 1;
    $dir = 'asc';
    if(isset($sort)){
        if(isset($sort['col']) && isset($sort['dir'])){
            if(strpos($sort['col'], 'ID') !== false){
                $col = 1;
            } else if(strpos($sort['col'], 'Name') !== false){
                $col = 2;
            } else if($sort['col'] == 'Area'){
                $col = 3;
            } else {
                $col = 1;
            }
            $dir = $sort['dir'];
        }

    }
?>
<input type="hidden" id="currIndexSort" value="<?php echo $col; ?>">
<input type="hidden" id="currDirSort" value="<?php echo $dir; ?>">

<!-- popup for customer -->
<?php echo $this->element('Admin/popup_customer'); ?>
<?php echo $this->element('Admin/popup_map'); ?>

<!-- script for customer -->
<?php
    echo $this->Html->script('calendar/moment.min.js', ['block' => 'scriptBottom']);
    echo $this->Html->script('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js', ['block' => 'scriptBottom']);
    echo $this->Html->script('jquery.ui.monthpicker.js', ['block' => 'scriptBottom']);
?>
<?= $this->Html->script('admin/alsok/customer.js?v='. date('YmdHis'), ['block' => 'scriptBottom']) . PHP_EOL ?>
