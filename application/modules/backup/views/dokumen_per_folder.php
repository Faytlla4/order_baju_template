<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$backupProcessUrl = site_url(SITE_AREA . '/backup/per_folder/process');

$inline_js = "
$(function() {
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

    $('#form-backup-per-folder').on('submit', function(e) {
        var selected = $('input[name=\"folders[]\"]:checked').length;
        if (selected === 0) {
            e.preventDefault();
            alert('Pilih minimal satu folder (Dokumen Transaksi dan/atau Report).');
            return false;
        }
        if (!confirm('Arsipkan seluruh isi folder yang dipilih?')) {
            e.preventDefault();
            return false;
        }
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

        <!-- PILIH FOLDER -->
        <form method="POST" action="<?php echo $backupProcessUrl; ?>" id="form-backup-per-folder">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder-open text-info"></i> Pilih Folder untuk Dibackup</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <i class="fas fa-info-circle"></i> Seluruh isi folder yang dipilih (termasuk subfolder) akan dimasukkan ke dalam satu file ZIP.
                </div>

                <?php
                $hasAny = false;
                foreach ($folders as $key => $folder) :
                    if (!$folder['exists']) { continue; }
                    $hasAny = true;
                ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="folders[]" value="<?php echo $key; ?>" id="folder-<?php echo $key; ?>">
                    <label class="form-check-label" for="folder-<?php echo $key; ?>">
                        <i class="<?php echo $folder['icon']; ?> text-info"></i>
                        <strong><?php echo html_escape($folder['label']); ?></strong>
                        <span class="text-muted">— <?php echo (int) $folder['count']; ?> file (rekursif)</span>
                    </label>
                </div>
                <?php endforeach; ?>

                <?php if (!$hasAny) : ?>
                    <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Tidak ada folder yang tersedia untuk dibackup.</div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-right">
                <?php if (!empty($can_document)) : ?>
                    <button type="submit" class="btn btn-info"><i class="fas fa-file-archive"></i> Backup Dokumen per Folder</button>
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
