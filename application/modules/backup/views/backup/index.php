<?php
$backupUrl = site_url(SITE_AREA . '/backup');
$backupDocUrl = site_url(SITE_AREA . '/backup/document');
$backupDbUrl = site_url(SITE_AREA . '/backup/database');
?>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">

<div class="row">
	<div class="col-12">
		<!-- Header -->
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><i class="fas fa-download text-primary"></i> BACKUP</h3>
			</div>
			<div class="card-body">
				<p class="text-muted mb-0">
					Backup Dokumen membackup laporan transaksi (PDF + Excel) dalam bentuk ZIP. Backup Database membackup seluruh database PostgreSQL.
				</p>
			</div>
		</div>

		<!-- Filter untuk Backup Dokumen -->
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><i class="fas fa-filter"></i> Filter Backup Dokumen</h3>
				<div class="card-tools">
					<span class="badge badge-info">Hanya untuk Backup Dokumen</span>
				</div>
			</div>
			<div class="card-body">
				<form id="backup-filter-form" method="post" action="<?php echo $backupDocUrl; ?>">
					<div class="row align-items-end">
						<div class="col-md-3">
							<div class="form-group">
								<label>Tanggal Mulai</label>
								<div class="input-group date" id="dp_mulai" data-target-input="nearest">
									<input type="text" name="tgl_mulai" id="tgl_mulai" class="form-control datetimepicker-input" data-target="#dp_mulai" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_mulai ? date('d-m-Y', strtotime($tgl_mulai)) : ''); ?>" />
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
									<input type="text" name="tgl_akhir" id="tgl_akhir" class="form-control datetimepicker-input" data-target="#dp_akhir" placeholder="DD-MM-YYYY" value="<?php echo html_escape($tgl_akhir ? date('d-m-Y', strtotime($tgl_akhir)) : ''); ?>" />
									<div class="input-group-append" data-target="#dp_akhir" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Status Transaksi</label>
								<select name="status" id="status" class="form-control">
									<option value="" <?php echo ($status === '') ? 'selected' : ''; ?>>Semua Status</option>
									<option value="Diproses" <?php echo ($status === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
									<option value="Diambil" <?php echo ($status === 'Diambil') ? 'selected' : ''; ?>>Diambil</option>
									<option value="Selesai" <?php echo ($status === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<a href="<?php echo site_url(SITE_AREA . '/backup'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset Filter</a>
								<small class="form-text text-muted">Kosongkan untuk backup semua data.</small>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Dua kartu -->
		<div class="row">
			<!-- Backup Dokumen -->
			<div class="col-md-6">
				<div class="card card-outline card-primary h-100">
					<div class="card-header">
						<h3 class="card-title"><i class="fas fa-file-archive text-primary"></i> BACKUP DOKUMEN</h3>
					</div>
					<div class="card-body d-flex flex-column">
						<p class="text-muted">
							Backup seluruh report transaksi dalam bentuk ZIP.<br>
							Isi ZIP: <code>laporan_transaksi_*.pdf</code> + <code>laporan_transaksi_*.xlsx</code><br>
							Filter tanggal &amp; status diterapkan sebelum generate.
						</p>
						<ul class="text-sm text-muted">
							<li>Reuse logic Report existing (PDF + Excel)</li>
							<li>1 PDF + 1 XLSX per backup (opsi A)</li>
							<li>File temporary otomatis dihapus</li>
						</ul>
						<div class="mt-auto">
							<?php if (!empty($can_document)) : ?>
								<button type="button" id="btn-backup-dokumen" class="btn btn-primary btn-block">
									<i class="fas fa-file-archive"></i> Backup Dokumen
								</button>
								<small class="text-muted d-block text-center mt-1">Download: backup_dokumen_YYYY-MM-DD_HHMMSS.zip</small>
							<?php else : ?>
								<div class="alert alert-warning mb-0"><i class="fas fa-lock"></i> Tidak ada permission Backup Dokumen.</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Backup Database -->
			<div class="col-md-6">
				<div class="card card-outline card-success h-100">
					<div class="card-header">
						<h3 class="card-title"><i class="fas fa-database text-success"></i> BACKUP DATABASE</h3>
					</div>
					<div class="card-body d-flex flex-column">
						<p class="text-muted">
							Backup seluruh database PostgreSQL dalam bentuk ZIP.<br>
							Isi ZIP: <code>database_backup.sql</code><br>
							Tidak menggunakan filter — full dump via <code>pg_dump</code>.
						</p>
						<ul class="text-sm text-muted">
							<li>Struktur + data + index + sequence</li>
							<li>Credential dari konfigurasi aplikasi</li>
							<li>File temporary otomatis dihapus</li>
						</ul>
						<div class="mt-auto">
							<?php if (!empty($can_database)) : ?>
								<form method="post" action="<?php echo $backupDbUrl; ?>" onsubmit="return confirm('Backup seluruh database? Proses dapat memakan waktu beberapa detik.');">
									<button type="submit" class="btn btn-success btn-block">
										<i class="fas fa-database"></i> Backup Database
									</button>
								</form>
								<small class="text-muted d-block text-center mt-1">Download: backup_database_YYYY-MM-DD_HHMMSS.zip</small>
							<?php else : ?>
								<div class="alert alert-warning mb-0"><i class="fas fa-lock"></i> Tidak ada permission Backup Database.</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="alert alert-info mt-3">
			<i class="fas fa-info-circle"></i> <strong>Restore database:</strong> <code>psql -h HOST -U USER -d DATABASE -f database_backup.sql</code>
		</div>
	</div>
</div>

<?php
$inline_js = "
$(function() {
    if ($('#dp_mulai').length && $.fn.datetimepicker) {
        $('#dp_mulai').datetimepicker({ format: 'DD-MM-YYYY' });
    }
    if ($('#dp_akhir').length && $.fn.datetimepicker) {
        $('#dp_akhir').datetimepicker({ format: 'DD-MM-YYYY' });
    }

    // Backup Dokumen -> POST via form submit dengan filter
    $('#btn-backup-dokumen').on('click', function(e) {
        e.preventDefault();
        var mulai = $('#tgl_mulai').val() || '';
        var akhir = $('#tgl_akhir').val() || '';
        // Validasi ringan
        if (mulai && akhir) {
            function toIso(v) {
                var m = /^(\\d{2})-(\\d{2})-(\\d{4})$/.exec(v || '');
                return m ? m[3] + '-' + m[2] + '-' + m[1] : '';
            }
            var mi = toIso(mulai);
            var ai = toIso(akhir);
            if (mi && ai && mi > ai) {
                alert('Tanggal Mulai tidak boleh setelah Tanggal Akhir.');
                return;
            }
        }
        if (!confirm('Backup dokumen dengan filter yang dipilih?')) return;
        $('#backup-filter-form').submit();
    });
});
";
Assets::add_js($inline_js, 'inline');
?>
