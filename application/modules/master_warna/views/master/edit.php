<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('master_warna_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif;

$id = isset($master_warna->id) ? $master_warna->id : '';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('master_warna_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_warna') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_warna_field_nama_warna') . lang('bf_form_label_required'), 'nama_warna', array('class' => '')); ?>
                            <input id='nama_warna' type='text' class='form-control' required='required' name='nama_warna' maxlength='30' value='<?php echo set_value('nama_warna', isset($master_warna->nama_warna) ? $master_warna->nama_warna : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_warna'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <?php
                        $options_status = array(
                            '1' => 'Aktif',
                            '0' => 'Non Aktif',
                        );
                        echo form_dropdown_lte(array('name' => 'status', 'id' => 'status', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status', isset($master_warna->status) ? $master_warna->status : 1), lang('master_warna_field_status') . lang('bf_form_label_required'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type='submit' name='save' class='btn btn-primary' value="<?php echo lang('master_warna_action_edit'); ?>" />
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/master/warna', lang('master_warna_cancel'), 'class="btn btn-warning"'); ?>

                <?php if ($this->auth->has_permission('Master_warna.Master.Delete')) : ?>
                    <?php echo lang('bf_or'); ?>
                    <button type='submit' name='delete' formnovalidate class='btn btn-danger' id='delete-me' onclick="return confirm('<?php e(js_escape(lang('master_warna_delete_confirm'))); ?>');">
                        <span class='icon-trash icon-white'></span>&nbsp;<?php echo lang('master_warna_delete_record'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
