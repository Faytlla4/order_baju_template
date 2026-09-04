<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('master_jenis_baju_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif; ?>
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
                            <input id='nama_customer' type='text' class='form-control' required='required' name='nama_customer' maxlength='100' value='<?php echo set_value('nama_customer'); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_customer'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('produk') ? ' error' : ''; ?>'>
                            <?php echo form_label('Produk' . lang('bf_form_label_required'), 'produk', array('class' => '')); ?>
                            <input id='produk' type='text' class='form-control' required='required' name='produk' maxlength='100' value='<?php echo set_value('produk'); ?>' />
                            <span class='help-inline'><?php echo form_error('produk'); ?></span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_jenis') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_jenis_baju_field_nama_jenis') . lang('bf_form_label_required'), 'nama_jenis', array('class' => '')); ?>
                            <input id='nama_jenis' type='text' class='form-control' required='required' name='nama_jenis' maxlength='50' value='<?php echo set_value('nama_jenis'); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_jenis'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <?php
                        $options_status = array(
                            ''  => '-- Pilih Status --',
                            '1' => 'Aktif',
                            '0' => 'Non Aktif',
                        );
                        echo form_dropdown_lte(array('name' => 'status', 'id' => 'status', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status', 1), lang('master_jenis_baju_field_status') . lang('bf_form_label_required'));
                        ?>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        <div class='form-group<?php echo form_error('keterangan') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('master_jenis_baju_field_keterangan'), 'keterangan', array('class' => '')); ?>
                            <?php echo form_textarea(array('name' => 'keterangan', 'id' => 'keterangan', 'class' => 'form-control', 'rows' => '3', 'cols' => '80', 'value' => set_value('keterangan'))); ?>
                            <span class='help-inline'><?php echo form_error('keterangan'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Setelah disimpan, sistem otomatis membuat satu order di Content
                    dengan Customer dan Produk yang diisi di atas, kode order otomatis,
                    tanggal hari ini, dan status Diproses.
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary"><?php echo lang('master_jenis_baju_action_create'); ?></button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/master/jenis_baju', lang('master_jenis_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
