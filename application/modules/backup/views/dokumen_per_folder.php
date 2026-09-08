<?php
Assets::add_css('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css', 'external');
Assets::add_js('plugins/datatables/jquery.dataTables.min.js', 'external');
Assets::add_js('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 'external');

$backupProcessUrl = site_url(SITE_AREA . '/backup/per_folder/process');
$backupPageUrl    = site_url(SITE_AREA . '/backup/per_folder');
$folderFilesUrl   = site_url(SITE_AREA . '/backup/per_folder/files');

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

    function updateFolderBackupLabel() {
        var selected = $('input[name=\"folders[]\"]:checked').length;
        var label = selected > 0 ? 'Backup ' + selected + ' Folder' : 'Backup Dokumen per Folder';
        $('#btn-backup-per-folder').html('<i class=\"fas fa-file-archive\"></i> ' + label);
        $('#selected-folder-count').text(selected);
    }

    $(document).on('change', 'input[name=\"folders[]\"]', updateFolderBackupLabel);
    updateFolderBackupLabel();

    $(document).on('click', '.btn-lihat-isi-folder', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var folder = $(this).attr('data-folder') || '';
        var modalTitle = $('#modal-folder-files-title');
        var modalBody = $('#modal-folder-files-body');
        modalTitle.text('Isi Folder: ' + folder);
        modalBody.html('<p class=\"text-muted mb-0\"><i class=\"fas fa-spinner fa-spin\"></i> Memindai isi folder...</p>');
        $('#modal-folder-files').modal('show');

        $.getJSON('" . $folderFilesUrl . "?folder=' + encodeURIComponent(folder))
            .done(function(res) {
                if (!res || !res.success) {
                    modalBody.html('<p class=\"text-danger mb-0\">' + $('<div>').text((res && res.message) || 'Gagal membaca isi folder.').html() + '</p>');
                    return;
                }
                if (!res.files || !res.files.length) {
                    modalBody.html('<p class=\"text-muted mb-0\">Folder tidak berisi file.</p>');
                    return;
                }
                var html = '<ol class=\"mb-0 pl-4\">';
                for (var i = 0; i < res.files.length; i++) {
                    html += '<li class=\"mb-1\">' + $('<div>').text(res.files[i]).html() + '</li>';
                }
                html += '</ol>';
                modalBody.html(html);
            })
            .fail(function() {
                modalBody.html('<p class=\"text-danger mb-0\">Gagal membaca isi folder.</p>');
            });
    });
});
";
Assets::add_js($inline_js, 'inline');
?>

<style>
.folder-list {
    border: 1px solid #e4d6c2;
    border-radius: 6px;
    background: #fffdf9;
    overflow: hidden;
}

.folder-row {
    display: flex;
    align-items: center;
    min-height: 42px;
    padding: 6px 12px;
    border-bottom: 1px solid #eee5da;
}

.folder-row:last-child {
    border-bottom: 0;
}

.folder-check {
    flex: 0 0 28px;
    margin: 0;
}

.folder-check input {
    margin: 0;
}

.folder-icon {
    flex: 0 0 28px;
    text-align: center;
}

.folder-name {
    flex: 1 1 auto;
    min-width: 0;
    text-align: left;
    line-height: 1.2;
}

.folder-name:hover {
    text-decoration: none;
}

.folder-count {
    flex: 0 0 auto;
    margin-left: 16px;
    color: #766b60;
    font-size: 0.875rem;
    white-space: nowrap;
}

.folder-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    width: 100%;
    margin: 0;
}

.folder-actions > .text-muted {
    margin-right: auto;
}

.folder-actions > #btn-backup-per-folder {
    margin-left: auto;
}

@media (max-width: 576px) {
    .folder-row {
        padding: 6px 8px;
    }

    .folder-count {
        margin-left: 8px;
        font-size: 0.8rem;
    }

    .folder-actions {
        align-items: flex-end;
        flex-direction: column;
        gap: 8px;
    }

    .folder-actions > .text-muted {
        align-self: flex-start;
        margin-right: 0;
    }

    .folder-actions > #btn-backup-per-folder {
        margin-left: 0;
    }
}
</style>

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
                <div class="card-tools">
                    <a href="<?php echo $backupPageUrl; ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Scan Ulang / Refresh Folder
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <i class="fas fa-info-circle"></i> Halaman ini memindai ulang folder dokumen utama di server setiap kali dibuka atau saat tombol refresh ditekan. Seluruh isi folder yang dipilih (termasuk subfolder) akan dimasukkan ke dalam satu file ZIP.
                </div>

                <div class="folder-list">
                    <?php
                    $hasAny = false;
                    foreach ($folders as $key => $folder) :
                        if (!$folder['exists']) { continue; }
                        $hasAny = true;
                    ?>
                    <div class="folder-row">
                        <div class="folder-check">
                            <input type="checkbox" name="folders[]" value="<?php echo html_escape($key); ?>" id="folder-<?php echo html_escape($key); ?>">
                        </div>
                        <div class="folder-icon">
                            <i class="<?php echo html_escape($folder['icon']); ?> text-info"></i>
                        </div>
                        <button type="button" class="btn btn-link p-0 folder-name btn-lihat-isi-folder" data-folder="<?php echo html_escape($key); ?>">
                            <strong><?php echo html_escape($folder['label']); ?></strong>
                        </button>
                        <span class="folder-count"><?php echo (int) $folder['count']; ?> file (rekursif)</span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!$hasAny) : ?>
                    <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Tidak ada folder yang tersedia untuk dibackup.</div>
                <?php endif; ?>
            </div>
            <div class="card-footer folder-actions">
                <?php if (!empty($can_document)) : ?>
                    <span class="text-muted">Jumlah folder dipilih: <strong id="selected-folder-count">0</strong></span>
                    <button type="submit" class="btn btn-info" id="btn-backup-per-folder"><i class="fas fa-file-archive"></i> Backup Dokumen per Folder</button>
                <?php else : ?>
                    <div class="alert alert-warning mb-0 text-left"><i class="fas fa-lock"></i> Tidak ada permission Backup Dokumen.</div>
                <?php endif; ?>
            </div>
        </div>
        </form>

        <div class="modal fade" id="modal-folder-files" tabindex="-1" role="dialog" aria-labelledby="modalFolderFilesLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-folder-files-title">Isi Folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-folder-files-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
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
