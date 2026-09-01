<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo isset($toolbar_title) ? $toolbar_title : 'Form Master'; ?></h3>
			</div>
			<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
			<div class="card-body">
				<?php if (validation_errors()) : ?>
					<div class="alert alert-danger">
						<?php echo validation_errors(); ?>
					</div>
				<?php endif; ?>

				<div class="form-group row">
					<label for="nama_jenis" class="col-sm-2 col-form-label">Nama Jenis <span class="text-danger">*</span></label>
					<div class="col-sm-10">
						<input type="text" name="nama_jenis" class="form-control" id="nama_jenis" placeholder="Nama Jenis" value="<?php echo set_value('nama_jenis', isset($record['nama_jenis']) ? $record['nama_jenis'] : ''); ?>" required>
					</div>
				</div>
				<div class="form-group row">
					<label for="urutan" class="col-sm-2 col-form-label">Urutan</label>
					<div class="col-sm-4">
						<input type="number" name="urutan" class="form-control" id="urutan" placeholder="Urutan" value="<?php echo set_value('urutan', isset($record['urutan']) ? $record['urutan'] : '0'); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
					<div class="col-sm-10">
						<textarea name="keterangan" class="form-control" id="keterangan" placeholder="Keterangan"><?php echo set_value('keterangan', isset($record['keterangan']) ? $record['keterangan'] : ''); ?></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label for="status" class="col-sm-2 col-form-label">Status</label>
					<div class="col-sm-4">
						<select name="status" class="form-control" id="status">
							<option value="1" <?php echo set_select('status', '1', isset($record['status']) && $record['status'] == 1); ?>>Aktif</option>
							<option value="0" <?php echo set_select('status', '0', isset($record['status']) && $record['status'] == 0); ?>>Nonaktif</option>
						</select>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary">Simpan</button>
				<a href="<?php echo site_url(SITE_AREA . '/master'); ?>" class="btn btn-default float-right">Batal</a>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
