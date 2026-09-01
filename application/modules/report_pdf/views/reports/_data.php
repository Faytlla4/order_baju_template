<?php
$btn_id    = isset($btn_id)    ? $btn_id    : 'btn-pdf';
$btn_class = isset($btn_class) ? $btn_class : 'btn-danger';
$btn_icon  = isset($btn_icon)  ? $btn_icon  : 'fas fa-file-pdf';
$btn_label = isset($btn_label) ? $btn_label : 'Cetak PDF';
?>
<div class="card" id="card-data">
    <div class="card-header">
        <h3 class="card-title">Data Transaksi — Periode: <?php echo html_escape($periode_label); ?> — Status: <?php echo html_escape(isset($status) && $status !== '' ? $status : 'Semua'); ?></h3>
        <span class="float-right">Jumlah: <?php echo count($rows); ?> transaksi</span>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($rows)) : ?>
            <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Tidak ada transaksi pada periode yang dipilih.</div>
        <?php else : ?>
            <table id="tbl-transaksi" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>Warna</th>
                        <th style="width:60px">Jumlah</th>
                        <th style="width:100px">Harga</th>
                        <th style="width:110px">Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; foreach ($rows as $r) : $no++; ?>
                    <tr>
                        <td class="text-center"><?php echo $no; ?></td>
                        <td><?php echo html_escape($r->kode_order); ?></td>
                        <td><?php echo html_escape($r->nama_customer); ?></td>
                        <td><?php echo html_escape($r->produk); ?></td>
                        <td><?php echo html_escape($r->jenis_nama); ?></td>
                        <td><?php echo html_escape($r->ukuran_nama); ?></td>
                        <td><?php echo html_escape($r->warna_nama); ?></td>
                        <td class="text-center"><?php echo (int) $r->jumlah; ?></td>
                        <td class="text-right"><?php echo 'Rp ' . number_format((float) $r->harga, 0, ',', '.'); ?></td>
                        <td class="text-right"><?php echo 'Rp ' . number_format((float) $r->total_harga, 0, ',', '.'); ?></td>
                        <td><?php echo html_escape($r->status_transaksi); ?></td>
                        <td><?php echo html_escape($r->tanggal); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-right">Total</th>
                        <th class="text-center"><?php echo count($rows); ?></th>
                        <th></th>
                        <th class="text-right">Rp <?php echo number_format((float) $grand_total, 0, ',', '.'); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>
    <?php if (!empty($rows)) : ?>
    <div class="card-footer text-right">
        <a href="javascript:void(0)" id="<?php echo html_escape($btn_id); ?>" class="btn <?php echo html_escape($btn_class); ?>">
            <i class="<?php echo html_escape($btn_icon); ?>"></i> <?php echo html_escape($btn_label); ?>
        </a>
    </div>
    <?php endif; ?>
</div>
