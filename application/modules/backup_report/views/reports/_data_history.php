<?php
$periode_label = 'Semua Periode';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $periode_label = date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
} elseif (!empty($tgl_mulai)) {
    $periode_label = 'Mulai ' . date('d-m-Y', strtotime($tgl_mulai));
} elseif (!empty($tgl_akhir)) {
    $periode_label = 'Sampai ' . date('d-m-Y', strtotime($tgl_akhir));
}

function _format_laporan_size($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}
?>
<div class="card" id="card-data">
    <div class="card-header">
        <h3 class="card-title">Riwayat Cetak Laporan — Periode: <?php echo html_escape($periode_label); ?></h3>
        <span class="float-right">Jumlah: <?php echo count($rows); ?> cetak</span>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($rows)) : ?>
            <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Tidak ada riwayat cetak laporan pada periode yang dipilih.</div>
        <?php else : ?>
            <table id="tbl-data" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Waktu Cetak</th>
                        <th>Jenis Laporan</th>
                        <th>Tipe Export</th>
                        <th>Filter Periode</th>
                        <th style="width:80px">Jumlah Data</th>
                        <th style="width:100px">Ukuran File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; foreach ($rows as $r) : $no++; ?>
                    <tr>
                        <td class="text-center"><?php echo $no; ?></td>
                        <td><?php echo html_escape($r->created_on_str); ?></td>
                        <td>
                            <?php if ($r->report_type === 'Dokumen') : ?>
                                <span class="badge badge-danger"><i class="fas fa-file-pdf"></i> Dokumen</span>
                            <?php else : ?>
                                <span class="badge badge-primary"><i class="fas fa-database"></i> Database</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($r->export_type === 'PDF') : ?>
                                <span class="badge badge-danger">PDF</span>
                            <?php else : ?>
                                <span class="badge badge-success">Excel</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo html_escape($r->filter_periode); ?></td>
                        <td class="text-center"><?php echo (int) $r->record_count; ?></td>
                        <td class="text-right"><?php echo _format_laporan_size($r->file_size); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
