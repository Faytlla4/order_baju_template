<?php
Assets::add_js('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js', 'external');
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$backupIndexUrl = site_url(SITE_AREA . '/backup');
$backupDocUrl   = site_url(SITE_AREA . '/backup/document');
$backupFilterUrl = site_url(SITE_AREA . '/backup/filter');

$js_data = array();
if (!empty($riwayat_cetak)) {
    foreach ($riwayat_cetak as $r) {
        $tipe_badge = $r->tipe_report === 'pdf'
            ? '<span class=\"badge badge-danger\"><i class=\"fas fa-file-pdf\"></i> PDF</span>'
            : '<span class=\"badge badge-success\"><i class=\"fas fa-file-excel\"></i> Excel</span>';
        $js_data[] = array(
            'id' => (int) $r->id,
            'created_on' => $r->created_on_str,
            'tipe_badge' => $tipe_badge,
            'nama_file' => $r->nama_file,
            'jumlah_transaksi' => (int) $r->jumlah_transaksi,
        );
    }
}

$inline_js = "
$(function() {
    var tblCetak;

    function initTable(data) {
        if (tblCetak) tblCetak.destroy();
        var tbody = '';
        if (data.length === 0) {
            tbody = '<tr><td colspan=\"6\" class=\"text-center text-muted\">Tidak ada data.</td></tr>';
        } else {
            for (var i = 0; i < data.length; i++) {
                var r = data[i];
                tbody += '<tr>'
                    + '<td class=\"text-center\"><input type=\"checkbox\" class=\"row-check\" value=\"' + r.id + '\"></td>'
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
            language: {
                search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            pageLength: 10, order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: 0 }],
            destroy: true
        });

        $('#check-all').prop('checked', false);
    }

    // Init with server data
    initTable(" . json_encode($js_data) . ");

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
                    initTable(res.data);
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
                if (res.success) initTable(res.data);
            }
        });
    });

    // Select All checkbox
    $(document).on('change', '#check-all', function() {
        var checked = $(this).is(':checked');
        tblCetak.rows().every(function() {
            $(this.node()).find('.row-check').prop('checked', checked);
        });
    });

    $(document).on('change', '.row-check', function() {
        var total = tblCetak.rows().nodes().length;
        var checked = tblCetak.nodes().toJQuery().find('.row-check:checked').length;
        $('#check-all').prop('checked', total > 0 && total === checked);
    });

    // Backup button
    $('#btn-backup-dokumen').on('click', function(e) {
        e.preventDefault();
        var selected = [];
        tblCetak.nodes().toJQuery().find('.row-check:checked').each(function() {
            selected.push($(this).val());
        });
        if (selected.length === 0) {
            alert('Pilih minimal satu dokumen dari Riwayat Cetak.');
            return;
        }
        if (!confirm('Arsipkan ' + selected.length + ' dokumen yang dipilih?')) return;

        var btn = $(this);
        btn.prop('disabled', true).html('<i class=\"fas fa-spinner fa-spin\"></i> Memproses...');

        $.ajax({
            url: '" . $backupDocUrl . "',
            method: 'POST',
            data: {
                report_ids: selected,
                tgl_mulai: $('#filter-tgl_mulai').val() || '',
                tgl_akhir: $('#filter-tgl_akhir').val() || ''
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class=\"fas fa-file-archive\"></i> Backup Dokumen Terpilih');
                if (res.success) {
                    $('#modal-download-url').attr('href', res.download_url);
                    $('#modal-backup-success').modal('show');
                } else {
                    alert(res.message || 'Gagal membuat backup.');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class=\"fas fa-file-archive\"></i> Backup Dokumen Terpilih');
                alert('Terjadi kesalahan server. Silakan coba lagi.');
            }
        });
    });

    $('#modal-backup-success').on('hidden.bs.modal', function() {
        window.location.reload();
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
                            <?php if (!empty($can_document)) : ?>
                                <button type="button" id="btn-backup-dokumen" class="btn btn-danger float-right">
                                    <i class="fas fa-file-archive"></i> Backup Dokumen Terpilih
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIWAYAT CETAK DOKUMEN -->
        <div class="card" id="card-data">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-print text-info"></i> Riwayat Cetak Dokumen</h3>
                <span class="float-right">Total: <span id="total-dokumen"><?php echo count($riwayat_cetak); ?></span> dokumen</span>
            </div>
            <div class="card-body table-responsive">
                <table id="tbl-riwayat-cetak" class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="check-all" title="Pilih Semua"></th>
                            <th style="width:60px">ID</th>
                            <th>Tanggal Cetak</th>
                            <th style="width:80px">Tipe</th>
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
