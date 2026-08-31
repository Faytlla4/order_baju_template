<?php
Assets::add_js('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js', 'external');
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$backupIndexUrl = site_url(SITE_AREA . '/backup');
$backupDocUrl   = site_url(SITE_AREA . '/backup/document');

$inline_js = "
$(function() {
    $('#tbl-transaksi').DataTable({
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
        },
        pageLength: 10, order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: 0 }],
        destroy: true
    });

    $('#tbl-backup-history').DataTable({
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
        },
        pageLength: 10, order: [[0, 'desc']], destroy: true
    });

    if ($('#dp_mulai').length && $.fn.datetimepicker) { $('#dp_mulai').datetimepicker({ format: 'DD-MM-YYYY' }); }
    if ($('#dp_akhir').length && $.fn.datetimepicker) { $('#dp_akhir').datetimepicker({ format: 'DD-MM-YYYY' }); }

    $('#btn-backup-dokumen').on('click', function(e) {
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
        if (!confirm('Backup dokumen dengan filter yang dipilih?')) return;

        var btn = $(this);
        btn.prop('disabled', true).html('<i class=\"fas fa-spinner fa-spin\"></i> Memproses...');

        $.ajax({
            url: '" . $backupDocUrl . "',
            method: 'POST',
            data: {
                tgl_mulai: mulai,
                tgl_akhir: akhir,
                status: $('#filter-status').val()
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class=\"fas fa-file-archive\"></i> Backup Dokumen');
                if (res.success) {
                    $('#modal-download-url').attr('href', res.download_url);
                    $('#modal-backup-success').modal('show');
                } else {
                    alert(res.message || 'Gagal membuat backup.');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class=\"fas fa-file-archive\"></i> Backup Dokumen');
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
                <h3 class="card-title"><i class="fas fa-filter text-primary"></i> Filter Data</h3>
            </div>
            <div class="card-body">
                <form id="filter-form" method="get" action="<?php echo $backupIndexUrl; ?>">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <div class="input-group date" id="dp_mulai" data-target-input="nearest">
                                    <input type="text" name="tgl_mulai" id="filter-tgl_mulai" class="form-control datetimepicker-input" data-target="#dp_mulai" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_mulai ? date('d-m-Y', strtotime($tgl_mulai)) : ''); ?>" />
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
                                    <input type="text" name="tgl_akhir" id="filter-tgl_akhir" class="form-control datetimepicker-input" data-target="#dp_akhir" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_akhir ? date('d-m-Y', strtotime($tgl_akhir)) : ''); ?>" />
                                    <div class="input-group-append" data-target="#dp_akhir" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="filter-status" class="form-control">
                                    <option value="" <?php echo ($status === '') ? 'selected' : ''; ?>>Semua</option>
                                    <option value="Diproses" <?php echo ($status === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="Diambil" <?php echo ($status === 'Diambil') ? 'selected' : ''; ?>>Diambil</option>
                                    <option value="Selesai" <?php echo ($status === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                <a href="<?php echo $backupIndexUrl; ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                                <?php if (!empty($can_document)) : ?>
                                    <button type="button" id="btn-backup-dokumen" class="btn btn-danger float-right">
                                        <i class="fas fa-file-archive"></i> Backup Dokumen
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIWAYAT CETAK DOKUMEN -->
        <div class="card" id="card-data">
            <div class="card-header">
                <h3 class="card-title">Riwayat Cetak Dokumen &mdash; Periode: <?php echo html_escape($periode_label); ?> &mdash; Status: <?php echo html_escape($status !== '' ? $status : 'Semua'); ?></h3>
                <span class="float-right">Jumlah: <?php echo count($rows); ?> transaksi</span>
            </div>
            <div class="card-body table-responsive">
                <?php if (empty($rows)) : ?>
                    <div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> <?php echo $has_filter ? 'Tidak ada transaksi pada periode yang dipilih.' : 'Silakan pilih filter tanggal/status untuk melihat data transaksi.'; ?></div>
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
                                <th>Filter</th>
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
