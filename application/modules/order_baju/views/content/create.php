<?php if (validation_errors()): ?>
<div class='alert alert-block alert-error fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('order_baju_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif;

$id = isset($order_baju->id) ? $order_baju->id : '';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo lang('order_baju_area_title'); ?></h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">

                <div class="alert alert-info">
                    Kode order akan dibuat otomatis oleh sistem setelah disimpan.
                </div>

                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('nama_customer') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('order_baju_field_nama_customer') . lang('bf_form_label_required'), 'nama_customer', array('class' => '')); ?>
                            <input id='nama_customer' type='text' class='form-control' required='required' name='nama_customer' maxlength='100' value='<?php echo set_value('nama_customer', isset($order_baju->nama_customer) ? $order_baju->nama_customer : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('nama_customer'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('produk') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('order_baju_field_produk') . '<span class="required">*</span>', 'produk', array('class' => '')); ?>
                            <input id='produk' type='text' class='form-control' required='required' name='produk' maxlength='100' value='<?php echo set_value('produk', isset($order_baju->produk) ? $order_baju->produk : ''); ?>' />
                            <span class='help-inline'><?php echo form_error('produk'); ?></span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-4'>
                        <div class='form-group<?php echo form_error('jenis_baju_id') ? ' error' : ''; ?>'>
                            <?php echo form_dropdown_lte(array('name' => 'jenis_baju_id', 'id' => 'jenis_baju_id', 'class' => 'form-control select2', 'data-placeholder' => 'Cari jenis baju...', 'required' => 'required'), $jenis_options, set_value('jenis_baju_id'), lang('order_baju_field_jenis_baju') . lang('bf_form_label_required')); ?>
                            <span class='help-inline'><?php echo form_error('jenis_baju_id'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group<?php echo form_error('ukuran_id') ? ' error' : ''; ?>'>
                            <?php echo form_dropdown_lte(array('name' => 'ukuran_id', 'id' => 'ukuran_id', 'class' => 'form-control select2', 'data-placeholder' => 'Cari ukuran...'), $ukuran_options, set_value('ukuran_id'), lang('order_baju_field_ukuran')); ?>
                            <span class='help-inline'><?php echo form_error('ukuran_id'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group<?php echo form_error('warna_id') ? ' error' : ''; ?>'>
                            <?php echo form_dropdown_lte(array('name' => 'warna_id', 'id' => 'warna_id', 'class' => 'form-control select2', 'data-placeholder' => 'Cari warna...'), $warna_options, set_value('warna_id'), lang('order_baju_field_warna')); ?>
                            <span class='help-inline'><?php echo form_error('warna_id'); ?></span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <div class='form-group<?php echo form_error('tanggal_order') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('order_baju_field_tanggal_order') . '<span class="required">*</span>', 'tanggal_order', array('class' => '')); ?>
                            <input id='tanggal_order' type='text' class='form-control datepicker' required='required' name='tanggal_order' value='<?php echo set_value('tanggal_order', isset($order_baju->tanggal_order) ? $order_baju->tanggal_order : date('Y-m-d')); ?>' />
                            <span class='help-inline'><?php echo form_error('tanggal_order'); ?></span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="status_order" value="Diproses" />
            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary"><?php echo lang('order_baju_action_create'); ?></button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/content/order_baju', lang('order_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
