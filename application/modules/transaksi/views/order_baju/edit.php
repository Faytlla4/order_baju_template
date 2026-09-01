<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Transaksi</h3>
            </div>
            <?php echo form_open(current_url(), 'class="" enctype="multipart/form-data"'); ?>
            <div class="card-body">

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
                            <tr><td>Dibuat Pada</td><td><?php echo isset($transaksi->created_on) ? html_escape($transaksi->created_on) : '-'; ?></td></tr>
                        </table>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="status_transaksi">Status Transaksi <span class="required">*</span></label>
                            <?php
                            $options_status = array();
                            foreach ($status_options as $s) {
                                $options_status[$s] = $s;
                            }
                            echo form_dropdown_lte(array('name' => 'status_transaksi', 'id' => 'status_transaksi', 'class' => 'form-control select2', 'required' => 'required'), $options_status, set_value('status_transaksi', $transaksi->status_transaksi), '');
                            ?>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="jumlah">Jumlah <span class="required">*</span></label>
                            <input id='jumlah' type='number' class='form-control' name='jumlah' min='1' value='<?php echo set_value('jumlah', $transaksi->jumlah); ?>' />
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label for="harga">Harga <span class="required">*</span></label>
                            <input id='harga' type='number' class='form-control' name='harga' min='0' step='0.01' value='<?php echo set_value('harga', $transaksi->harga); ?>' />
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label>Total Harga</label>
                            <input id='total_harga_display' type='text' class='form-control' readonly='readonly' value="<?php echo 'Rp ' . number_format((float) $transaksi->jumlah * (float) $transaksi->harga, 0, ',', '.'); ?>" />
                        </div>
                    </div>
                </div>

                <!-- Dokumen / Lampiran -->
                <div class="card mb-3 border">
                    <div class="card-header py-2"><strong>Dokumen / Lampiran</strong> <small class="text-muted">(Opsional)</small></div>
                    <div class="card-body py-2">
                        <div class="form-group">
                            <small class="text-muted d-block mb-2">Format: PDF, PNG, JPG, JPEG, GIF, DOC, DOCX, XLS, XLSX &mdash; Maks 10MB per file.</small>

                            <?php if (!empty($dokumen_files) && is_array($dokumen_files)) : ?>
                            <div id="dokumen-existing" class="mb-2">
                                <strong>Dokumen yang sudah ada:</strong>
                                <?php foreach ($dokumen_files as $idx => $fname) : ?>
                                <div class="dokumen-existing-row mb-1" style="display:flex;align-items:center;gap:8px;background:#f8f9fa;padding:4px 8px;border-radius:4px;">
                                    <input type="checkbox" name="hapus_dokumen[]" value="<?php echo html_escape($fname); ?>" id="hapus_<?php echo $idx; ?>" />
                                    <label for="hapus_<?php echo $idx; ?>" style="margin:0;font-size:13px;">
                                        <i class="fas fa-file"></i> <?php echo html_escape($fname); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <small class="text-muted">Centang untuk menghapus dokumen yang dipilih.</small>
                            </div>
                            <?php else : ?>
                            <p class="text-muted"><em>Belum ada dokumen.</em></p>
                            <?php endif; ?>

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
                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/transaksi/transaksi', lang('order_baju_cancel'), 'class="btn btn-warning"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>