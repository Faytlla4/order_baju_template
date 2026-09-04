<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('master_ukuran_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif;

$id = isset($master_ukuran->id) ? $master_ukuran->id : '';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('master_ukuran_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_ukuran') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_ukuran_field_nama_ukuran') . lang('bf_form_label_required'), 'nama_ukuran', array('class' => '')); ?>
                            <input id='nama_ukuran' type='text' class='form-control' required='required' name='nama_ukuran' maxlength='20' value='<?php echo set_value('nama_ukuran', isset($master_ukuran->nama_ukuran) ? $master_ukuran->nama_ukuran : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_ukuran'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <?php
                        $options_status = array(
                            '1' => 'Aktif',
                            '0' => 'Non Aktif',
                        );
                        echo form_dropdown_lte(array('name' => 'status', 'id' => 'status', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status', isset($master_ukuran->status) ? $master_ukuran->status : 1), lang('master_ukuran_field_status') . lang('bf_form_label_required'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type='submit' name='save' class='btn btn-primary' value="<?php echo lang('master_ukuran_action_edit'); ?>" />
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/master/ukuran', lang('master_ukuran_cancel'), 'class="btn btn-warning"'); ?>

                <?php if ($this->auth->has_permission('Master_ukuran.Master.Delete')) : ?>
                    <?php echo lang('bf_or'); ?>
                    <button type='submit' name='delete' formnovalidate class='btn btn-danger' id='delete-me' onclick="return confirm('<?php e(js_escape(lang('master_ukuran_delete_confirm'))); ?>');">
                        <span class='icon-trash icon-white'></span>&nbsp;<?php echo lang('master_ukuran_delete_record'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
