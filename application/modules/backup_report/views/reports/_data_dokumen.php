<?php
$show_pdf   = isset($show_pdf)   ? $show_pdf   : true;
$show_excel = isset($show_excel) ? $show_excel : false;

$periode_label = 'Semua Periode';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $periode_label = date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
} elseif (!empty($tgl_mulai)) {
    $periode_label = 'Mulai ' . date('d-m-Y', strtotime($tgl_mulai));
} elseif (!empty($tgl_akhir)) {
    $periode_label = 'Sampai ' . date('d-m-Y', strtotime($tgl_akhir));
}
?>
<div class="card" id="card-data">
    <div class="card-header">
        <h3 class="card-title">Riwayat Backup Dokumen — Periode: <?php echo html_escape($periode_label); ?></h3>
        <span class="float-right">Jumlah: <?php echo count($rows); ?> backup</span>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($rows)) : ?>
            <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Tidak ada riwayat backup dokumen pada periode yang dipilih.</div>
        <?php else : ?>
            <table id="tbl-data" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal/Waktu</th>
                        <th>Nama File</th>
                        <th style="width:80px">Jumlah Dokumen</th>
                        <th style="width:100px">Ukuran</th>
                        <th>Filter</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; foreach ($rows as $r) : $no++; ?>
                    <tr>
                        <td class="text-center"><?php echo $no; ?></td>
                        <td><?php echo html_escape($r->created_on_str); ?></td>
                        <td><?php echo html_escape($r->file_name); ?></td>
                        <td class="text-center"><?php echo (int) $r->jumlah_dokumen; ?></td>
                        <td class="text-right"><?php echo number_format($r->file_size / 1048576, 2) . ' MB'; ?></td>
                        <td><?php echo html_escape($r->filter_used); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="card-footer text-right">
        <?php if ($show_pdf) : ?>
        <a href="<?php echo html_escape($export_url ?? site_url(SITE_AREA . '/laporan-dokumen/cetak-pdf')); ?>" id="btn-pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </a>
        <?php endif; ?>
        <?php if ($show_excel) : ?>
        <a href="<?php echo html_escape($export_url ?? site_url(SITE_AREA . '/laporan-dokumen/cetak-excel')); ?>" id="btn-excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Cetak Excel
        </a>
        <?php endif; ?>
    </div>
</div>
