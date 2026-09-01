<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Master Jenis Baju</h3>
				<div class="card-tools">
					<a href="<?php echo site_url(SITE_AREA .'/master/create'); ?>" class="btn btn-primary btn-sm">
						<i class="fas fa-plus"></i> Tambah Baru
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="50">No</th>
								<th>Nama Jenis Baju</th>
								<th>Urutan</th>
								<th>Keterangan</th>
								<th width="100">Status</th>
								<th width="150">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php if (isset($records) && is_array($records) && count($records)) : ?>
							<?php $no = 1; foreach ($records as $record) : ?>
								<tr>
									<td class="text-center"><?php echo $no++; ?></td>
									<td><?php echo html_escape($record['nama_jenis']); ?></td>
									<td><?php echo (int)$record['urutan']; ?></td>
									<td><?php echo html_escape($record['keterangan']); ?></td>
									<td>
										<?php if ($record['status'] == 1) : ?>
											<span class="badge badge-success">Aktif</span>
										<?php else : ?>
											<span class="badge badge-secondary">Nonaktif</span>
										<?php endif; ?>
									</td>
									<td>
										<a href="<?php echo site_url(SITE_AREA . '/master/edit/' . $record['id']); ?>" class="btn btn-warning btn-xs" title="Edit">
											<i class="fas fa-edit"></i> Edit
										</a>
										<a href="<?php echo site_url(SITE_AREA . '/master/delete/' . $record['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');" title="Hapus">
											<i class="fas fa-trash"></i> Hapus
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="6" class="text-center">Tidak ada data.</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
