<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Data Order Baju</h3>
				<div class="card-tools">
					<a href="<?php echo site_url(SITE_AREA . '/transaksi/create'); ?>" class="btn btn-primary btn-sm">
						<i class="fas fa-plus"></i> Tambah Order
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filter Form -->
				<form method="get" action="" class="mb-3">
					<div class="row">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Cari kode, customer, produk..." value="<?php echo html_escape($filter_search); ?>">
						</div>
						<div class="col-md-2">
							<select name="status" class="form-control">
								<option value="">-- Semua Status --</option>
								<?php foreach ($status_list as $s) : ?>
									<option value="<?php echo $s; ?>" <?php echo $filter_status === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-2">
							<input type="date" name="from" class="form-control" placeholder="Dari Tanggal" value="<?php echo html_escape($filter_from); ?>">
						</div>
						<div class="col-md-2">
							<input type="date" name="to" class="form-control" placeholder="Sampai Tanggal" value="<?php echo html_escape($filter_to); ?>">
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-info">Cari</button>
							<a href="<?php echo site_url(SITE_AREA . '/transaksi'); ?>" class="btn btn-default">Reset</a>
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode Order</th>
								<th>Customer</th>
								<th>Produk</th>
								<th>Jenis Baju</th>
								<th>Ukuran</th>
								<th>Warna</th>
								<th>Jumlah</th>
								<th>Harga</th>
								<th>Total</th>
								<th>Status</th>
								<th>Tanggal</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($records)) : ?>
							<?php $no = 1; foreach ($records as $r) : ?>
								<tr>
									<td><?php echo $no++; ?></td>
									<td><?php echo html_escape($r['kode_order']); ?></td>
									<td><?php echo html_escape($r['nama_customer']); ?></td>
									<td><?php echo html_escape($r['produk']); ?></td>
									<td><?php echo html_escape($r['nama_jenis'] ?? '-'); ?></td>
									<td><?php echo html_escape($r['nama_ukuran'] ?? '-'); ?></td>
									<td><?php echo html_escape($r['nama_warna'] ?? '-'); ?></td>
									<td class="text-center"><?php echo (int)$r['jumlah']; ?></td>
									<td>Rp<?php echo number_format($r['harga'], 0, ',', '.'); ?></td>
									<td><strong>Rp<?php echo number_format($r['total_harga'], 0, ',', '.'); ?></strong></td>
									<td>
										<?php
											$statusClass = array(
												'Draft' => 'badge-secondary',
												'Diproses' => 'badge-warning',
												'Selesai' => 'badge-success',
												'Dibatalkan' => 'badge-danger',
											);
											$cls = isset($statusClass[$r['status_order']]) ? $statusClass[$r['status_order']] : 'badge-secondary';
										?>
										<span class="badge <?php echo $cls; ?>"><?php echo html_escape($r['status_order']); ?></span>
									</td>
									<td><?php echo date('d/m/Y', strtotime($r['tanggal_order'])); ?></td>
									<td>
										<a href="<?php echo site_url(SITE_AREA . '/transaksi/edit/' . $r['id']); ?>" class="btn btn-warning btn-xs">
											<i class="fas fa-edit"></i>
										</a>
										<a href="<?php echo site_url(SITE_AREA . '/transaksi/delete/' . $r['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus order ini?');">
											<i class="fas fa-trash"></i>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="13" class="text-center">Tidak ada data order.</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>