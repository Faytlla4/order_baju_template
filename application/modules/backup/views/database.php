<?php
$backupDbUrl = site_url(SITE_AREA . '/backup/database/run');
$backupIndexUrl = site_url(SITE_AREA . '/backup');

$inline_js = "
$(function() {
    $('#tbl-db-history').DataTable({
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
        },
        pageLength: 10, order: [[0, 'desc']], destroy: true
    });

    $('#btn-backup-db').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Backup seluruh database? Proses dapat memakan waktu beberapa detik.')) return;

        var btn = $(this);
        btn.prop('disabled', true).html('<i class=\"fas fa-spinner fa-spin\"></i> Memproses...');

        $.ajax({
            url: '" . $backupDbUrl . "',
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class=\"fas fa-database\"></i> Backup Database');
                if (res.success) {
                    $('#modal-download-url-db').attr('href', res.download_url);
                    $('#modal-db-success').modal('show');
                } else {
                    alert(res.message || 'Gagal membuat backup database.');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class=\"fas fa-database\"></i> Backup Database');
                alert('Terjadi kesalahan server. Silakan coba lagi.');
            }
        });
    });

    $('#modal-db-success').on('hidden.bs.modal', function() {
        window.location.reload();
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<?php if ($this->session->flashdata('message')) : ?>
<div class="alert alert-<?php echo $this->session->flashdata('type') ?: 'info'; ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $this->session->flashdata('message'); ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">

        <!-- BACKUP DATABASE -->
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-database text-success"></i> BACKUP DATABASE</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" style="max-width:500px;">
                    <tr>
                        <th style="width:180px;">Database</th>
                        <td><?php echo html_escape($db_name); ?></td>
                    </tr>
                    <tr>
                        <th>Host</th>
                        <td><?php echo html_escape($db_host . ':' . $db_port); ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Backup</th>
                        <td><?php echo html_escape($backup_date); ?> (WIB)</td>
                    </tr>
                </table>

                <?php if (!empty($can_database)) : ?>
                    <button type="button" id="btn-backup-db" class="btn btn-success btn-lg">
                        <i class="fas fa-database"></i> Backup Database
                    </button>
                <?php else : ?>
                    <div class="alert alert-warning mb-0"><i class="fas fa-lock"></i> Tidak ada permission Backup Database.</div>
                <?php endif; ?>

                <a href="<?php echo $backupIndexUrl; ?>" class="btn btn-secondary btn-lg ml-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- RIWAYAT BACKUP DATABASE -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history text-info"></i> Riwayat Backup Database</h3>
            </div>
            <div class="card-body table-responsive">
                <?php if (empty($backup_history)) : ?>
                    <div class="alert alert-info mb-0"><i class="fas fa-info-circle"></i> Belum ada riwayat backup database.</div>
                <?php else : ?>
                    <table id="tbl-db-history" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Tanggal</th>
                                <th>Nama File</th>
                                <th style="width:100px">Ukuran</th>
                                <th style="width:80px">Status</th>
                                <th style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach ($backup_history as $h) : $no++; ?>
                            <tr>
                                <td class="text-center"><?php echo $no; ?></td>
                                <td><?php echo html_escape(date('d-m-Y H:i', strtotime($h->created_on))); ?></td>
                                <td><?php echo html_escape($h->file_name); ?></td>
                                <td class="text-right"><?php $s = $h->file_size; echo ($s >= 1048576) ? round($s/1048576, 2).' MB' : (($s >= 1024) ? round($s/1024, 1).' KB' : $s.' B'); ?></td>
                                <td>
                                    <?php if ($h->status === 'Berhasil') : ?>
                                        <span class="badge badge-success"><?php echo html_escape($h->status); ?></span>
                                    <?php else : ?>
                                        <span class="badge badge-danger"><?php echo html_escape($h->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo site_url(SITE_AREA . '/backup/download/db/' . $h->id); ?>" class="btn btn-sm btn-success" title="Download">
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

<!-- MODAL SUKSES DB -->
<div class="modal fade" id="modal-db-success" tabindex="-1" role="dialog" aria-labelledby="modalDbLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3"><i class="fas fa-check-circle text-success" style="font-size:48px;"></i></div>
                <h5>Backup database berhasil dibuat</h5>
                <p class="text-muted">File backup telah disimpan di server.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#" id="modal-download-url-db" class="btn btn-primary"><i class="fas fa-download"></i> Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
