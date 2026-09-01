<?php if (validation_errors()) : ?>
<div class="alert alert-block alert-danger fade in">
    <a class="close" data-dismiss="alert">&times;</a>
    <h4 class="alert-heading">
        Periksa kembali data berikut:
    </h4>
    <?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Proses Transaksi</h3>
            </div>
            <?php echo form_open(SITE_AREA . '/transaksi/order_baju/save', 'class=""'); ?>
            <div class="card-body">

                <input type="hidden" name="id" value="<?php echo $order_id; ?>" />
                <input type="hidden" name="kode_order" value="<?php echo html_escape($kode); ?>" />

                <div class="card mb-3 border">
                    <div class="card-header py-2"><strong>Data Order</strong></div>
                    <div class="card-body py-2">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td width="12%">Kode Order</td>
                                <td><strong><?php echo html_escape($detail->kode); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td><?php echo html_escape($detail->nama_customer); ?></td>
                            </tr>
                            <tr>
                                <td>Produk</td>
                                <td><?php echo html_escape($detail->produk); ?></td>
                            </tr>
                            <tr>
                                <td>Jenis Baju</td>
                                <td><?php echo html_escape($detail->jenis_nama); ?></td>
                            </tr>
                            <tr>
                                <td>Ukuran</td>
                                <td><?php echo html_escape($detail->ukuran_nama); ?></td>
                            </tr>
                            <tr>
                                <td>Warna</td>
                                <td><?php echo html_escape($detail->warna_nama); ?></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td><?php echo html_escape($detail->tanggal_order); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="status_order">Status Order</label>
                            <?php
                            $options_status = array(
                                'Draft'             => 'Draft',
                                'Menunggu'          => 'Menunggu',
                                'Diproses'          => 'Diproses',
                                'Menunggu Selesai'  => 'Menunggu Selesai',
                                'Selesai'           => 'Selesai',
                                'Diambil'           => 'Diambil',
                            );
                            echo form_dropdown_lte(array('name' => 'status_order', 'id' => 'status_order', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status_order', isset($order_status) ? $order_status : 'Menunggu'), 'Status Order');
                            ?>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group<?php echo form_error('jumlah') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('order_baju_field_jumlah') . lang('bf_form_label_required'), 'jumlah', array('class' => '')); ?>
                            <input id='jumlah' type='number' class='form-control' required='required' name='jumlah' min='1' value='<?php echo set_value('jumlah', isset($jumlah_val) ? $jumlah_val : 1); ?>' />
                            <span class='help-inline'><?php echo form_error('jumlah'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group<?php echo form_error('harga') ? ' error' : ''; ?>'>
                            <?php echo form_label(lang('order_baju_field_harga') . lang('bf_form_label_required'), 'harga', array('class' => '')); ?>
                            <input id='sel_harga' type='number' class='form-control' required='required' name='harga' min='0' step='0.01' value='<?php echo set_value('harga', isset($harga_val) ? $harga_val : ''); ?>' placeholder="0" />
                            <span class='help-inline'><?php echo form_error('harga'); ?></span>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Total Harga</label>
                            <input id='total_harga_display' type='text' class='form-control' readonly='readonly' placeholder="Rp 0" value="Rp 0" />
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" name="save" class="btn btn-primary">Simpan Transaksi</button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/transaksi/order_baju', lang('order_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>