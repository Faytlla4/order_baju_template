<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');
Assets::add_js('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js', 'external');

$backupIndexUrl = site_url(SITE_AREA . '/backup');
$backupDocUrl   = site_url(SITE_AREA . '/backup/document');
$backupFilterUrl = site_url(SITE_AREA . '/backup/filter');

$js_report = array();
if (!empty($riwayat_cetak)) {
    foreach ($riwayat_cetak as $r) {
        $tipe_badge = $r->tipe_report === 'pdf'
            ? '<span class=\"badge badge-danger\"><i class=\"fas fa-file-pdf\"></i> PDF</span>'
            : '<span class=\"badge badge-success\"><i class=\"fas fa-file-excel\"></i> Excel</span>';
        $js_report[] = array(
            'source' => 'report',
            'id' => (int) $r->id,
            'created_on' => $r->created_on_str,
            'tipe_badge' => $tipe_badge,
            'nama_file' => $r->nama_file,
            'jumlah_transaksi' => (int) $r->jumlah_transaksi,
        );
    }
}

$js_trx = array();
if (!empty($dokumen_transaksi)) {
    foreach ($dokumen_transaksi as $d) {
        $tipe_badge = '<span class=\"badge badge-info\"><i class=\"fas fa-folder-open\"></i> Dokumen Transaksi</span>';
        $js_trx[] = array(
            'source' => 'transaksi',
            'id' => (int) $d->id,
            'created_on' => $d->created_on_str,
            'tipe_badge' => $tipe_badge,
            'nama_file' => $d->nama_file,
            'jumlah_transaksi' => (int) $d->jumlah_transaksi,
        );
    }
}

$inline_js = "
$(function() {
    var tblCetak;
    var tblTrx;

    function dtLangFile() {
        return {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
        };
    }

    // Tabel 1: Laporan (PDF/Excel) — checkbox report_ids[]
    function initTableReport(data) {
        if (tblCetak) tblCetak.destroy();
        var tbody = '';
        if (data.length === 0) {
            tbody = '<tr><td colspan=\"6\" class=\"text-center text-muted\">Tidak ada data.</td></tr>';
        } else {
            for (var i = 0; i < data.length; i++) {
                var r = data[i];
                tbody += '<tr>'
                    + '<td class=\"text-center\"><input type=\"checkbox\" name=\"report_ids[]\" class=\"row-check-report\" value=\"' + r.id + '\"></td>'
                    + '<td class=\"text-center\">' + r.id + '</td>'
                    + '<td>' + r.created_on + '</td>'
                    + '<td class=\"text-center\">' + r.tipe_badge + '</td>'
                    + '<td>' + r.nama_file + '</td>'
                    + '<td class=\"text-center\">' + r.jumlah_transaksi + '</td>'
                    + '</tr>';
            }
        }
        $('#tbl-riwayat-cetak tbody').html(tbody);
        $('#total-dokumen').text(data.length);

        tblCetak = $('#tbl-riwayat-cetak').DataTable({
            language: dtLangFile(),
            pageLength: 10, order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: 0 }],
            destroy: true
        });
        $('#check-all').prop('checked', false);
    }

    // Tabel 2: Dokumen Transaksi (upload user) — checkbox trx_docs[]
    function initTableTrx(data) {
        if (tblTrx) tblTrx.destroy();
        var tbody = '';
        if (data.length === 0) {
            tbody = '<tr><td colspan=\"6\" class=\"text-center text-muted\">Tidak ada data.</td></tr>';
        } else {
            for (var i = 0; i < data.length; i++) {
                var r = data[i];
                tbody += '<tr>'
                    + '<td class=\"text-center\"><input type=\"checkbox\" name=\"trx_docs[]\" class=\"row-check-trx\" value=\"' + r.id + ':' + r.nama_file + '\"></td>'
                    + '<td class=\"text-center\">' + r.id + '</td>'
                    + '<td>' + r.created_on + '</td>'
                    + '<td class=\"text-center\">' + r.tipe_badge + '</td>'
                    + '<td>' + r.nama_file + '</td>'
                    + '<td class=\"text-center\">' + r.jumlah_transaksi + '</td>'
                    + '</tr>';
            }
        }
        $('#tbl-dokumen-transaksi tbody').html(tbody);
        $('#total-dokumen-trx').text(data.length);

        tblTrx = $('#tbl-dokumen-transaksi').DataTable({
            language: dtLangFile(),
            pageLength: 10, order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: 0 }],
            destroy: true
        });
        $('#check-all-trx').prop('checked', false);
    }

    // Init with server data
    initTableReport(" . json_encode($js_report) . ");
    initTableTrx(" . json_encode($js_trx) . ");

    if ($('#tbl-backup-history').length) {
        $('#tbl-backup-history').DataTable({
            language: {
                search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            pageLength: 10, order: [[0, 'desc']], destroy: true
        });
    }

    if ($('#dp_mulai').length && $.fn.datetimepicker) {
        $('#dp_mulai').datetimepicker({ format: 'DD-MM-YYYY', useCurrent: false });
        $('#dp_mulai').on('change.datetimepicker', function(e) {
            $('#filter-tgl_mulai').val(e.date ? e.date.format('DD-MM-YYYY') : '');
        });
    }
    if ($('#dp_akhir').length && $.fn.datetimepicker) {
        $('#dp_akhir').datetimepicker({ format: 'DD-MM-YYYY', useCurrent: false });
        $('#dp_akhir').on('change.datetimepicker', function(e) {
            $('#filter-tgl_akhir').val(e.date ? e.date.format('DD-MM-YYYY') : '');
        });
    }

    // AJAX Filter
    $('#btn-filter').on('click', function(e) {
        e.preventDefault();
        var mulai = $('#filter-tgl_mulai').val() || '';
        var akhir = $('#filter-tgl_akhir').val() || '';
        if (mulai && akhir) {
            function toIso(v) {
                var m = /^(\\d{2})-(\\d{2})-(\\d{4})$/.exec(v || '');
                return m ? m[3]+'-'+m[2]+'-'+m[1] : '';
            }
            var mi = toIso(mulai), ai = toIso(akhir);
            if (mi && ai && mi > ai) { alert('Tanggal Mulai tidak boleh setelah Tanggal Akhir.'); return; }
        }
        $.ajax({
            url: '" . $backupFilterUrl . "',
            data: { tgl_mulai: mulai, tgl_akhir: akhir },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var report = [], trx = [];
                    for (var i = 0; i < res.data.length; i++) {
                        if (res.data[i].source === 'transaksi') { trx.push(res.data[i]); }
                        else { report.push(res.data[i]); }
                    }
                    initTableReport(report);
                    initTableTrx(trx);
                }
            }
        });
    });

    // Reset
    $('#btn-reset').on('click', function(e) {
        e.preventDefault();
        $('#filter-tgl_mulai').val('');
        $('#filter-tgl_akhir').val('');
        $.ajax({
            url: '" . $backupFilterUrl . "',
            data: { tgl_mulai: '', tgl_akhir: '' },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var report = [], trx = [];
                    for (var i = 0; i < res.data.length; i++) {
                        if (res.data[i].source === 'transaksi') { trx.push(res.data[i]); }
                        else { report.push(res.data[i]); }
                    }
                    initTableReport(report);
                    initTableTrx(trx);
                }
            }
        });
    });

    // Select All — Laporan (PDF/Excel)
    $(document).on('change', '#check-all', function() {
        var checked = $(this).is(':checked');
        if (!tblCetak) return;
        tblCetak.rows().every(function() {
            $(this.node()).find('.row-check-report').prop('checked', checked);
        });
    });

    $(document).on('change', '.row-check-report', function() {
        if (!tblCetak) return;
        var total = tblCetak.rows().nodes().length;
        var checked = tblCetak.nodes().toJQuery().find('.row-check-report:checked').length;
        $('#check-all').prop('checked', total > 0 && total === checked);
    });

    // Select All — Dokumen Transaksi (upload user)
    $(document).on('change', '#check-all-trx', function() {
        var checked = $(this).is(':checked');
        if (!tblTrx) return;
        tblTrx.rows().every(function() {
            $(this.node()).find('.row-check-trx').prop('checked', checked);
        });
    });

    $(document).on('change', '.row-check-trx', function() {
        if (!tblTrx) return;
        var total = tblTrx.rows().nodes().length;
        var checked = tblTrx.nodes().toJQuery().find('.row-check-trx:checked').length;
        $('#check-all-trx').prop('checked', total > 0 && total === checked);
    });

    // Backup button — handled by form submit, sync dates before submit
    $('#form-backup-dokumen').on('submit', function() {
        $('#form-tgl_mulai').val($('#filter-tgl_mulai').val());
        $('#form-tgl_akhir').val($('#filter-tgl_akhir').val());
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<link rel="stylesheet" href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">

<?php if ($this->session->flashdata('message')) : ?>
<div class="alert alert-<?php echo $this->session->flashdata('type') ?: 'info'; ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $this->session->flashdata('message'); ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">

        <!-- FILTER -->
        <form method="POST" action="<?php echo $backupDocUrl; ?>" id="form-backup-dokumen">
        <input type="hidden" name="tgl_mulai" id="form-tgl_mulai" value="<?php echo html_escape($tgl_mulai); ?>">
        <input type="hidden" name="tgl_akhir" id="form-tgl_akhir" value="<?php echo html_escape($tgl_akhir); ?>">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter text-primary"></i> Filter Riwayat Cetak</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <div class="input-group date" id="dp_mulai" data-target-input="nearest">
                                <input type="text" id="filter-tgl_mulai" class="form-control datetimepicker-input" data-target="#dp_mulai" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_mulai ? date('d-m-Y', strtotime($tgl_mulai)) : ''); ?>" />
                                <div class="input-group-append" data-target="#dp_mulai" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Akhir</label>
                            <div class="input-group date" id="dp_akhir" data-target-input="nearest">
                                <input type="text" id="filter-tgl_akhir" class="form-control datetimepicker-input" data-target="#dp_akhir" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_akhir ? date('d-m-Y', strtotime($tgl_akhir)) : ''); ?>" />
                                <div class="input-group-append" data-target="#dp_akhir" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="button" id="btn-filter" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                            <button type="button" id="btn-reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</button>
                            <button type="submit" id="btn-backup-dokumen" class="btn btn-danger float-right" onclick="return confirm('Arsipkan dokumen yang dipilih?')">
                                    <i class="fas fa-file-archive"></i> Backup Dokumen Terpilih
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOKUMEN TRANSAKSI (UPLOAD USER) -->
        <div class="card" id="card-data-trx">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder-open text-primary"></i> Dokumen Transaksi (Upload User)</h3>
                <span class="float-right">Total: <span id="total-dokumen-trx"><?php echo count($dokumen_transaksi); ?></span> dokumen</span>
            </div>
            <div class="card-body table-responsive">
                <table id="tbl-dokumen-transaksi" class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="check-all-trx" title="Pilih Semua Dokumen Transaksi"></th>
                            <th style="width:60px">ID Transaksi</th>
                            <th>Tanggal</th>
                            <th style="width:160px">Tipe</th>
                            <th>Nama File</th>
                            <th style="width:80px">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIWAYAT CETAK DOKUMEN (LAPORAN PDF/EXCEL) -->
        <div class="card mt-3" id="card-data">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-print text-info"></i> Riwayat Cetak (Laporan PDF/Excel)</h3>
                <span class="float-right">Total: <span id="total-dokumen"><?php echo count($riwayat_cetak); ?></span> laporan</span>
            </div>
            <div class="card-body table-responsive">
                <table id="tbl-riwayat-cetak" class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="check-all" title="Pilih Semua Laporan"></th>
                            <th style="width:60px">ID</th>
                            <th>Tanggal</th>
                            <th style="width:100px">Tipe</th>
                            <th>Nama File</th>
                            <th style="width:80px">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIWAYAT BACKUP DOKUMEN -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history text-info"></i> Riwayat Backup Dokumen</h3>
            </div>
            <div class="card-body table-responsive">
                <?php if (empty($backup_history)) : ?>
                    <div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Belum ada riwayat backup dokumen.</div>
                <?php else : ?>
                    <table id="tbl-backup-history" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Tanggal</th>
                                <th>Nama File</th>
                                <th>Jumlah Dokumen</th>
                                <th>Periode</th>
                                <th style="width:100px">Ukuran</th>
                                <th style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach ($backup_history as $h) : $no++; ?>
                            <tr>
                                <td class="text-center"><?php echo $no; ?></td>
                                <td><?php echo html_escape(date('d-m-Y H:i', strtotime($h->created_on))); ?></td>
                                <td><?php echo html_escape($h->file_name); ?></td>
                                <td class="text-center"><?php echo (int) $h->jumlah_dokumen; ?></td>
                                <td><?php echo html_escape($h->filter_used); ?></td>
                                <td class="text-right"><?php $s = $h->file_size; echo ($s >= 1048576) ? round($s/1048576, 2).' MB' : (($s >= 1024) ? round($s/1024, 1).' KB' : $s.' B'); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo site_url(SITE_AREA . '/backup/download/doc/' . $h->id); ?>" class="btn btn-sm btn-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
</form>

<!-- MODAL SUKSES -->
<div class="modal fade" id="modal-backup-success" tabindex="-1" role="dialog" aria-labelledby="modalBackupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3"><i class="fas fa-check-circle text-success" style="font-size:48px;"></i></div>
                <h5>Backup berhasil dibuat</h5>
                <p class="text-muted">File backup telah disimpan di server.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#" id="modal-download-url" class="btn btn-primary"><i class="fas fa-download"></i> Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
