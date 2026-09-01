<?php if (validation_errors()) : ?>
<div class='alert alert-block alert-alert-danger fade in'>
    <a class='close' data-dismiss='alert'>&times;</a>
    <h4 class='alert-heading'>
        <?php echo lang('order_baju_errors_message'); ?>
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Transaksi Baru</h3>
            </div>
            <?php echo form_open($this->uri->uri_string(), 'class=""'); ?>
            <div class="card-body">
                <p>Masukkan kode order yang sudah dibuat di Content untuk diproses.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> Contoh format: <strong>ORD-YYYYMMDD-NNNN</strong>
                    (contoh: ORD-20260809-0001). Kode bisa disalin di daftar Content.
                </div>
                <div class='form-group<?php echo form_error('kode_order') ? ' error' : ''; ?>'>
                    <?php echo form_label(lang('order_baju_field_kode_order') . lang('bf_form_label_required'), 'kode_order', array('class' => '')); ?>
                    <input id='kode_order' type='text' class='form-control' required='required' name='kode_order' maxlength='50' value='<?php echo set_value('kode_order'); ?>' placeholder="ORD-YYYYMMDD-NNNN" autofocus="autofocus" />
                    <span class='help-inline'><?php echo form_error('kode_order'); ?></span>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" name="lanjut" class="btn btn-primary">Lanjut</button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/transaksi/order_baju', lang('order_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>