<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('master_warna_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('master_warna_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <div class="alert alert-info">
                    Masukkan kode order dari Content. Customer terisi otomatis.
                    Tidak membuat kode order baru.
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('kode_order') ? ' error' : ''; ?>'>
                            <label for="kode_order_warna">Kode Order <span class="required">*</span></label>
                            <input id='kode_order_warna' type='text' class='form-control' required='required' name='kode_order' maxlength='50' value='<?php echo set_value('kode_order'); ?>' placeholder="ORD-YYYYMMDD-NNNN" autofocus="autofocus" />
                            <span class='help-inline'><?php echo form_error('kode_order'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group'>
                            <label>Customer</label>
                            <input id='customer_warna' type='text' class='form-control' readonly='readonly' placeholder="Terisi otomatis" value="<?php echo html_escape(isset($customer_value) ? $customer_value : ''); ?>" />
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_warna') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_warna_field_nama_warna') . lang('bf_form_label_required'), 'nama_warna', array('class' => '')); ?>
                            <input id='nama_warna' type='text' class='form-control' required='required' name='nama_warna' maxlength='30' value='<?php echo set_value('nama_warna'); ?>' placeholder="Contoh: Hitam, Putih, Merah" />
                            <span class='help-inline'><?php echo form_error('nama_warna'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <?php
                        $options_status = array(
                            ''  => '-- Pilih Status --',
                            '1' => 'Aktif',
                            '0' => 'Non Aktif',
                        );
                        echo form_dropdown_lte(array('name' => 'status', 'id' => 'status', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status', 1), lang('master_warna_field_status') . lang('bf_form_label_required'));
                        ?>
                    </div>
                    </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary"><?php echo lang('master_warna_action_create'); ?></button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/master/master_warna', lang('master_warna_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>