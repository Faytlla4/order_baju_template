<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo isset($toolbar_title) ? $toolbar_title : 'Form Order'; ?></h3>
			</div>
			<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
			<div class="card-body">
				<?php if (validation_errors()) : ?>
					<div class="alert alert-danger">
						<?php echo validation_errors(); ?>
					</div>
				<?php endif; ?>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="nama_customer">Nama Customer <span class="text-danger">*</span></label>
							<input type="text" name="nama_customer" id="nama_customer" class="form-control"
								value="<?php echo set_value('nama_customer', isset($record['nama_customer']) ? $record['nama_customer'] : ''); ?>"
								placeholder="Nama Customer" required maxlength="100">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="produk">Produk <span class="text-danger">*</span></label>
							<input type="text" name="produk" id="produk" class="form-control"
								value="<?php echo set_value('produk', isset($record['produk']) ? $record['produk'] : ''); ?>"
								placeholder="Nama Produk" required maxlength="100">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="jenis_baju_id">Jenis Baju</label>
							<select name="jenis_baju_id" id="jenis_baju_id" class="form-control">
								<?php foreach ($jenis_baju as $val => $label) : ?>
									<option value="<?php echo $val; ?>" <?php echo set_select('jenis_baju_id', $val, (isset($record['jenis_baju_id']) && $record['jenis_baju_id'] == $val)); ?>>
										<?php echo html_escape($label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="ukuran_id">Ukuran</label>
							<select name="ukuran_id" id="ukuran_id" class="form-control">
								<?php foreach ($ukuran as $val => $label) : ?>
									<option value="<?php echo $val; ?>" <?php echo set_select('ukuran_id', $val, (isset($record['ukuran_id']) && $record['ukuran_id'] == $val)); ?>>
										<?php echo html_escape($label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="warna_id">Warna</label>
							<select name="warna_id" id="warna_id" class="form-control">
								<?php foreach ($warna as $val => $label) : ?>
									<option value="<?php echo $val; ?>" <?php echo set_select('warna_id', $val, (isset($record['warna_id']) && $record['warna_id'] == $val)); ?>>
										<?php echo html_escape($label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="jumlah">Jumlah <span class="text-danger">*</span></label>
							<input type="number" name="jumlah" id="jumlah" class="form-control"
								value="<?php echo set_value('jumlah', isset($record['jumlah']) ? $record['jumlah'] : '1'); ?>"
								placeholder="Jumlah" min="1" required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="harga">Harga Satuan (Rp) <span class="text-danger">*</span></label>
							<input type="number" name="harga" id="harga" class="form-control"
								value="<?php echo set_value('harga', isset($record['harga']) ? $record['harga'] : ''); ?>"
								placeholder="Harga" min="0" step="1000" required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tanggal_order">Tanggal Order <span class="text-danger">*</span></label>
							<input type="date" name="tanggal_order" id="tanggal_order" class="form-control"
								value="<?php echo set_value('tanggal_order', isset($record['tanggal_order']) ? $record['tanggal_order'] : date('Y-m-d')); ?>"
								required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="status_order">Status Order</label>
							<select name="status_order" id="status_order" class="form-control">
								<?php foreach ($status_list as $s) : ?>
									<option value="<?php echo $s; ?>" <?php echo set_select('status_order', $s, (isset($record['status_order']) && $record['status_order'] == $s)); ?>>
										<?php echo $s; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary">Simpan</button>
				<a href="<?php echo site_url(SITE_AREA . '/transaksi'); ?>" class="btn btn-default float-right">Batal</a>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
