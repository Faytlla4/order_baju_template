<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Laporan Order Baju</h3>
			</div>
			<div class="card-body">

				<!-- Filter Form -->
				<form method="get" action="" class="mb-4">
					<div class="row">
						<div class="col-md-2">
							<label>Dari Tanggal</label>
							<input type="date" name="date_from" class="form-control" value="<?php echo html_escape($filter_from); ?>">
						</div>
						<div class="col-md-2">
							<label>Sampai Tanggal</label>
							<input type="date" name="date_to" class="form-control" value="<?php echo html_escape($filter_to); ?>">
						</div>
						<div class="col-md-2">
							<label>Status</label>
							<select name="status" class="form-control">
								<option value="">-- Semua Status --</option>
								<?php foreach ($status_list as $s) : ?>
									<option value="<?php echo $s; ?>" <?php echo $filter_status === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-4 d-flex align-items-end">
							<button type="submit" class="btn btn-info mr-2"><i class="fas fa-search"></i> Tampilkan</button>
							<a href="<?php echo site_url(SITE_AREA . '/reports'); ?>" class="btn btn-default mr-2">Reset</a>
							<!-- Export buttons submit GET params -->
							<a href="<?php echo site_url(SITE_AREA . '/reports/exportExcel?date_from=' . urlencode($filter_from) . '&date_to=' . urlencode($filter_to) . '&status=' . urlencode($filter_status)); ?>" class="btn btn-success mr-2">
								<i class="fas fa-file-excel"></i> Export Excel
							</a>
							<a href="<?php echo site_url(SITE_AREA . '/reports/printView?date_from=' . urlencode($filter_from) . '&date_to=' . urlencode($filter_to) . '&status=' . urlencode($filter_status)); ?>" target="_blank" class="btn btn-secondary">
								<i class="fas fa-print"></i> Print / PDF
							</a>
						</div>
					</div>
				</form>

				<!-- Summary -->
				<div class="row mb-3">
					<div class="col-md-3">
						<div class="small-box bg-info">
							<div class="inner">
								<h3><?php echo $total_transaksi; ?></h3>
								<p>Total Order</p>
							</div>
							<div class="icon"><i class="fas fa-shopping-cart"></i></div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="small-box bg-success">
							<div class="inner">
								<h3>Rp <?php echo number_format($total_nilai, 0, ',', '.'); ?></h3>
								<p>Total Nilai</p>
							</div>
							<div class="icon"><i class="fas fa-coins"></i></div>
						</div>
					</div>
				</div>

				<!-- Preview Table -->
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode Order</th>
								<th>Customer</th>
								<th>Produk</th>
								<th>Jenis</th>
								<th>Ukuran</th>
								<th>Warna</th>
								<th>Qty</th>
								<th>Harga</th>
								<th>Total</th>
								<th>Status</th>
								<th>Tanggal</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($preview_rows)) : ?>
							<?php $no = 1; foreach ($preview_rows as $r) : ?>
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
											$cls = array(
												'Draft' => 'badge-secondary',
												'Diproses' => 'badge-warning',
												'Selesai' => 'badge-success',
												'Dibatalkan' => 'badge-danger',
											);
											$c = isset($cls[$r['status_order']]) ? $cls[$r['status_order']] : 'badge-secondary';
										?>
										<span class="badge <?php echo $c; ?>"><?php echo $r['status_order']; ?></span>
									</td>
									<td><?php echo date('d/m/Y', strtotime($r['tanggal_order'])); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="12" class="text-center">Tidak ada data pada filter ini.</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Report History -->
		<?php if (!empty($reports)) : ?>
		<div class="card mt-3">
			<div class="card-header">
				<h3 class="card-title">Riwayat Export Laporan</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead>
							<tr>
								<th>No</th>
								<th>Periode</th>
								<th>Tgl Mulai</th>
								<th>Tgl Akhir</th>
								<th>Jml Order</th>
								<th>Total Nilai</th>
								<th>Tipe</th>
								<th>File</th>
								<th>Dibuat</th>
							</tr>
						</thead>
						<tbody>
						<?php $no = 1; foreach ($reports as $rp) : ?>
							<tr>
								<td><?php echo $no++; ?></td>
								<td><?php echo html_escape($rp['periode']); ?></td>
								<td><?php echo $rp['tgl_mulai'] ? date('d/m/Y', strtotime($rp['tgl_mulai'])) : '-'; ?></td>
								<td><?php echo $rp['tgl_akhir'] ? date('d/m/Y', strtotime($rp['tgl_akhir'])) : '-'; ?></td>
								<td><?php echo (int)$rp['jumlah_transaksi']; ?></td>
								<td>Rp<?php echo number_format($rp['total_nilai'], 0, ',', '.'); ?></td>
								<td><?php echo html_escape(strtoupper($rp['tipe_report'])); ?></td>
								<td><?php echo html_escape($rp['nama_file'] ?: '-'); ?></td>
								<td><?php echo date('d/m/Y H:i', strtotime($rp['created_on'])); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
