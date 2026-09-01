<?php $selected = isset($order) && $order !== null; ?>
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Proses Transaksi</h3>
            </div>

            <!-- Pilih kode order dari Content -->
            <?php echo form_open(current_url(), 'class="" enctype="multipart/form-data"'); ?>
            <div class="card-body">
                <p>Masukkan kode order yang sudah tersedia di Content untuk diproses.</p>
                <div class="row">
                    <div class="col-md-8">
                        <div class='form-group<?php echo form_error('kode_order') ? ' error' : ''; ?>'>
                            <label for="kode_order">Kode Order <span class="required">*</span></label>
                            <input id='kode_order' type='text' class='form-control' name='kode_order' maxlength='50'
                                   value='<?php echo set_value('kode_order', $kode_selected); ?>'
                                   placeholder="ORD-YYYYMMDD-NNNN" autofocus="autofocus" />
                            <span class='help-inline'><?php echo form_error('kode_order'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="submit" name="cari" value="1" class="btn btn-info btn-block">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!$selected) : ?>
            <?php echo form_close(); ?>
            <?php endif; ?>

            <!-- Bila order ditemukan: detail + form transaksi -->
            <?php if ($selected) : ?>

            <div class="card-body border-top">

                <input type="hidden" name="kode_order" value="<?php echo html_escape($kode_selected); ?>" />

                <div class="card mb-3 border">
                    <div class="card-header py-2"><strong>Detail Pesanan</strong></div>
                    <div class="card-body py-2">
                        <table class="table table-sm mb-0">
                            <tr><td width="20%">Kode Order</td><td><strong><?php echo html_escape($detail->kode); ?></strong></td></tr>
                            <tr><td>Customer</td><td><?php echo html_escape($detail->nama_customer); ?></td></tr>
                            <tr><td>Produk</td><td><?php echo html_escape($detail->produk); ?></td></tr>
                            <tr><td>Jenis Baju</td><td><?php echo html_escape($detail->jenis_nama); ?></td></tr>
                            <tr><td>Ukuran</td><td><?php echo html_escape($detail->ukuran_nama); ?></td></tr>
                            <tr><td>Warna</td><td><?php echo html_escape($detail->warna_nama); ?></td></tr>
                            <tr><td>Tanggal Order</td><td><?php echo html_escape($detail->tanggal_order); ?></td></tr>
                            <tr><td>Status Order</td><td><?php echo isset($order->status_order) ? html_escape($order->status_order) : '-'; ?></td></tr>
                        </table>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="status_transaksi">Status <span class="required">*</span></label>
                            <?php
                            $options_status = array();
                            foreach ($status_options as $s) {
                                $options_status[$s] = $s;
                            }
                            echo form_dropdown_lte(array('name' => 'status_transaksi', 'id' => 'status_transaksi', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status_transaksi', 'Diproses'), '');
                            ?>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="jumlah">Jumlah <span class="required">*</span></label>
                            <input id='jumlah' type='number' class='form-control' name='jumlah' min='1' value='<?php echo set_value('jumlah', $jumlah_val); ?>' />
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="harga">Harga <span class="required">*</span></label>
                            <input id='harga' type='number' class='form-control' name='harga' min='0' step='0.01' value='<?php echo set_value('harga', $harga_val); ?>' />
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label>Total Harga</label>
                            <input id='total_harga_display' type='text' class='form-control' readonly='readonly' value="<?php echo 'Rp ' . number_format((float) $jumlah_val * (float) $harga_val, 0, ',', '.'); ?>" />
                        </div>
                    </div>
                </div>

                <!-- Dokumen / Lampiran -->
                <div class="card mb-3 border">
                    <div class="card-header py-2"><strong>Dokumen / Lampiran</strong> <small class="text-muted">(Opsional)</small></div>
                    <div class="card-body py-2">
                        <div class="form-group">
                            <small class="text-muted d-block mb-2">Format: PDF, PNG, JPG, JPEG, GIF, DOC, DOCX, XLS, XLSX &mdash; Maks 10MB per file.</small>
                            <div id="dokumen-list">
                                <div class="dokumen-row mb-2" style="display:flex;gap:8px;align-items:center;">
                                    <input type="file" name="dokumen[]" class="form-control-file dokumen-input" style="flex:1;" accept=".pdf,.png,.jpg,.jpeg,.jfif,.gif,.doc,.docx,.xls,.xlsx" />
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-dokumen" title="Hapus"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-1" id="btn-tambah-dokumen">
                                <i class="fas fa-plus"></i> Tambah Dokumen
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" name="save" value="1" class="btn btn-primary">Simpan / Proses</button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/transaksi/transaksi', lang('order_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>

            <?php endif; ?>

        </div>
    </div>
</div>