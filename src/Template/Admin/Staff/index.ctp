<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Staff Management</h6>
        <button type="button" class="btn btn-success rounded-pill float-right" id="show-modal-staff"><i class="fas fa-plus"></i>Add</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="serverDataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="w-td3 text-center">No.</th>
                    <th class="w-td2">StaffID</th>
                    <th>Name</th>
                    <th class="w-td2">Password</th>
                    <th class="w-td4">Position</th>
                    <th class="w-td4">Face Image</th>
                    <th class="w-td2">Region</th>
                    <th class="w-td1">Created At</th>
                    <th class="text-center w-button"></th>
                </tr>
                </thead>
                <tbody id="tblStaff">
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
                $col = 5;
            } else {
                $col = 1;
            }
            $dir = $sort['dir'];
        }

    }
?>
<input type="hidden" id="currIndexSort" value="<?php echo $col; ?>">
<input type="hidden" id="currDirSort" value="<?php echo $dir; ?>">

<!-- popups -->
<?php echo $this->element('Admin/popup_staff'); ?>
<?php echo $this->element('Admin/popup_qrcode'); ?>
<?php echo $this->element('Admin/popup_view_image'); ?>

<!-- script for staff -->
<?= $this->Html->script('admin/alsok/staff.js?v='. date('YmdHis'), ['block' => 'scriptBottom']) . PHP_EOL ?>
