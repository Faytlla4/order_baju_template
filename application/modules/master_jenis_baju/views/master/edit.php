<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('master_jenis_baju_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif;

$id = isset($master_jenis_baju->id) ? $master_jenis_baju->id : '';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('master_jenis_baju_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_customer') ? ' error' : ''; ?>'>
                            <?php echo form_label('Customer' . lang('bf_form_label_required'), 'nama_customer', array('class' => '')); ?>
                            <input id='nama_customer' type='text' class='form-control' required='required' name='nama_customer' maxlength='100' value='<?php echo set_value('nama_customer', isset($order_terkait->nama_customer) ? $order_terkait->nama_customer : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_customer'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('produk') ? ' error' : ''; ?>'>
                            <?php echo form_label('Produk' . lang('bf_form_label_required'), 'produk', array('class' => '')); ?>
                            <input id='produk' type='text' class='form-control' required='required' name='produk' maxlength='100' value='<?php echo set_value('produk', isset($order_terkait->produk) ? $order_terkait->produk : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('produk'); ?></span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_jenis') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_jenis_baju_field_nama_jenis') . lang('bf_form_label_required'), 'nama_jenis', array('class' => '')); ?>
                            <input id='nama_jenis' type='text' class='form-control' required='required' name='nama_jenis' maxlength='50' value='<?php echo set_value('nama_jenis', isset($master_jenis_baju->nama_jenis) ? $master_jenis_baju->nama_jenis : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_jenis'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('keterangan') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_jenis_baju_field_keterangan'), 'keterangan', array('class' => '')); ?>
                            <?php echo form_textarea(array('name' => 'keterangan', 'id' => 'keterangan', 'class' => 'form-control', 'rows' => '3', 'cols' => '80', 'value' => set_value('keterangan', isset($master_jenis_baju->keterangan) ? $master_jenis_baju->keterangan : ''))); ?>
                            <span class='help-inline'><?php echo form_error('keterangan'); ?></span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <?php
                        $options_status = array(
                            '1' => 'Aktif',
                            '0' => 'Non Aktif',
                        );
                        echo form_dropdown_lte(array('name' => 'status', 'id' => 'status', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status', isset($master_jenis_baju->status) ? $master_jenis_baju->status : 1), lang('master_jenis_baju_field_status') . lang('bf_form_label_required'));
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type='submit' name='save' class='btn btn-primary' value="<?php echo lang('master_jenis_baju_action_edit'); ?>" />
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/master/jenis_baju', lang('master_jenis_baju_cancel'), 'class="btn btn-warning"'); ?>

                <?php if ($this->auth->has_permission('Master_jenis_baju.Master.Delete')) : ?>
                    <?php echo lang('bf_or'); ?>
                    <button type='submit' name='delete' formnovalidate class='btn btn-danger' id='delete-me' onclick="return confirm('<?php e(js_escape(lang('master_jenis_baju_delete_confirm'))); ?>');">
                        <span class='icon-trash icon-white'></span>&nbsp;<?php echo lang('master_jenis_baju_delete_record'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
