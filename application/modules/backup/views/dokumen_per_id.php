<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');
Assets::add_js('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js', 'external');

$backupProcessUrl = site_url(SITE_AREA . '/backup/per_id/process');
$backupFilterUrl  = site_url(SITE_AREA . '/backup/per_id');

$js_rows = array();
if (!empty($dokumen)) {
    foreach ($dokumen as $d) {
        $js_rows[] = array(
            'id'             => (int) $d->id,
            'created_on_str' => html_escape($d->created_on_str),
            'jumlah'         => (int) $d->jumlah_dokumen,
        );
    }
}

$inline_js = "
$(function() {
    var allRows = " . json_encode($js_rows) . ";

    function initTable() {
        if (!$('#tbl-per-id').length) { return; }
        if ($.fn.DataTable.isDataTable('#tbl-per-id')) { $('#tbl-per-id').DataTable().destroy(); }
        var tbody = '';
        if (!allRows.length) {
            tbody = '<tr><td colspan=\"5\" class=\"text-center text-muted\">Tidak ada data.</td></tr>';
        } else {
            for (var i = 0; i < allRows.length; i++) {
                var r = allRows[i];
                tbody += '<tr>'
                    + '<td class=\"text-center\"><input type=\"checkbox\" name=\"ids[]\" class=\"row-check\" value=\"' + r.id + '\"></td>'
                    + '<td class=\"text-center\">' + r.id + '</td>'
                    + '<td>' + r.created_on_str + '</td>'
                    + '<td class=\"text-center\"><span class=\"badge badge-info\">' + r.jumlah + ' Dokumen</span></td>'
                    + '<td>ID_' + r.id + '</td>'
                    + '</tr>';
            }
        }
        $('#tbl-per-id tbody').html(tbody);
        $('#total-id').text(allRows.length);

        $('#tbl-per-id').DataTable({
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

    initTable();

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
        window.location.href = '" . $backupFilterUrl . "' + '?tgl_mulai=' + encodeURIComponent(mulai) + '&tgl_akhir=' + encodeURIComponent(akhir);
    });

    $('#btn-reset').on('click', function(e) {
        e.preventDefault();
        window.location.href = '" . $backupFilterUrl . "';
    });

    $(document).on('change', '#check-all', function() {
        var checked = $(this).is(':checked');
        $('#tbl-per-id tbody').find('input.row-check').prop('checked', checked);
    });

    $(document).on('change', '.row-check', function() {
        var total = allRows.length;
        var checked = $('#tbl-per-id tbody').find('input.row-check:checked').length;
        $('#check-all').prop('checked', total > 0 && total === checked);
    });

    $('#form-backup-per-id').on('submit', function(e) {
        if ($('#tbl-per-id tbody').find('input.row-check:checked').length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu ID transaksi.');
            return false;
        }
        if (!confirm('Arsipkan semua dokumen dari ID yang dipilih?')) {
            e.preventDefault();
            return false;
        }
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
                <h3 class="card-title"><i class="fas fa-filter text-primary"></i> Filter Dokumen Transaksi</h3>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR ID TRANSaksi -->
        <form method="POST" action="<?php echo $backupProcessUrl; ?>" id="form-backup-per-id">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder-open text-primary"></i> Dokumen per ID Transaksi</h3>
                <span class="float-right">Total: <span id="total-id"><?php echo count($dokumen); ?></span> ID</span>
            </div>
            <div class="card-body table-responsive">
                <table id="tbl-per-id" class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="check-all" title="Pilih Semua ID"></th>
                            <th style="width:90px">ID Transaksi</th>
                            <th>Tanggal</th>
                            <th style="width:140px">Jumlah Dokumen</th>
                            <th>Folder ZIP</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <?php if (!empty($can_document)) : ?>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-file-archive"></i> Backup Dokumen per ID</button>
                <?php else : ?>
                    <div class="alert alert-warning mb-0 text-left"><i class="fas fa-lock"></i> Tidak ada permission Backup Dokumen.</div>
                <?php endif; ?>
            </div>
        </div>
        </form>

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
