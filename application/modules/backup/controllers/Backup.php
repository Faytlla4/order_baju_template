<?php defined('BASEPATH') || exit('No direct script access allowed');

class Backup extends App_Controller
{
	protected $permissionView     = 'Backup.Backup.View';
	protected $permissionDocument = 'Backup.Backup.Document';
	protected $permissionDatabase = 'Backup.Backup.Database';

	public function __construct()
	{
		parent::__construct();
		Template::set('toolbar_title', 'Backup');
	}

	// --------------------------------------------------------------------
	// INDEX — Filter + Riwayat Cetak + Riwayat Backup Dokumen
	// --------------------------------------------------------------------
	public function index()
	{
		$this->load->model('backup/backup_model');

		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get('status'));

		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
		}

		$rows = array();
		$has_filter = ($tgl_mulai !== '' || $tgl_akhir !== '' || $status !== '');
		if ($has_filter) {
			$this->load->model('report_pdf/report_model');
			$rows = $this->report_model->get_report('custom', $tgl_mulai, $tgl_akhir, $status);
		}

		$backup_history = $this->backup_model->get_document_history();

		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('status', $status);
		Template::set('rows', $rows);
		Template::set('grand_total', empty($rows) ? 0 : $this->report_model->grand_total($rows));
		Template::set('has_filter', $has_filter);
		Template::set('periode_label', $this->build_periode_label($tgl_mulai, $tgl_akhir, $status));
		Template::set('backup_history', $backup_history);
		Template::set('can_document', $this->auth->has_permission($this->permissionDocument));
		Template::set('can_database', $this->auth->has_permission($this->permissionDatabase));
		Template::set_view('backup/index');
		Template::render();
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN — POST, store ZIP + record history, return JSON
	// --------------------------------------------------------------------
	public function document()
	{
		if ($this->auth->permission_exists($this->permissionDocument)) {
			$this->auth->restrict($this->permissionDocument);
		}

		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup');
			return;
		}

		$this->load->model('backup/backup_model');

		$tgl_mulai = $this->normalize_tgl($this->input->post('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->post('tgl_akhir'));
		$status    = $this->normalize_status($this->input->post('status'));

		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => 'Tanggal Mulai tidak boleh setelah Tanggal Akhir.'));
				exit;
			}
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$rows = $this->backup_model->get_transaksi_for_backup($tgl_mulai, $tgl_akhir, $status);

		if (empty($rows)) {
			$msg = 'Tidak ada data transaksi untuk periode/status yang dipilih.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup?' . http_build_query(array('status' => $status, 'tgl_mulai' => $tgl_mulai, 'tgl_akhir' => $tgl_akhir)));
			return;
		}

		if (!class_exists('ZipArchive')) {
			$msg = 'ZipArchive tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$tmpDir = $this->create_tmp_dir('backup_dokumen');
		if ($tmpDir === false) {
			$msg = 'Permission directory tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$zipName = 'backup_dokumen_' . $stamp . '.zip';
		$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

		$periode_label = $this->build_periode_label($tgl_mulai, $tgl_akhir, $status);

		try {
			$pdfFile = $tmpDir . DIRECTORY_SEPARATOR . 'laporan_transaksi_' . $wib->format('Y-m-d_His') . '.pdf';
			if (!$this->generate_pdf_file($rows, $periode_label, $pdfFile)) {
				throw new Exception('Gagal generate PDF');
			}

			$xlsxFile = $tmpDir . DIRECTORY_SEPARATOR . 'laporan_transaksi_' . $wib->format('Y-m-d_His') . '.xlsx';
			if (!$this->generate_excel_file($rows, $periode_label, $xlsxFile)) {
				throw new Exception('Gagal generate Excel');
			}

			$zip = new ZipArchive();
			if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
				throw new Exception('Gagal membuat ZIP');
			}
			$zip->addFile($pdfFile, basename($pdfFile));
			$zip->addFile($xlsxFile, basename($xlsxFile));
			$zip->close();

			if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
				throw new Exception('ZIP kosong');
			}

			$head = @file_get_contents($tmpZip, false, null, 0, 2);
			if ($head === false || strncmp($head, 'PK', 2) !== 0) {
				@unlink($tmpZip);
				throw new Exception('ZIP tidak valid');
			}

			// Store permanently
			$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
			if ($stored_path === false) {
				throw new Exception('Gagal menyimpan ZIP');
			}

			// Record history
			$filter_label = $periode_label;
			$this->backup_model->save_document_history(
				$zipName,
				$stored_path,
				filesize($stored_path),
				count($rows),
				$filter_label
			);

			$this->cleanup_tmp($tmpDir);
			@unlink($tmpZip);

			if ($this->input->is_ajax_request()) {
				echo json_encode(array(
					'success' => true,
					'message' => 'Backup berhasil dibuat.',
					'download_url' => site_url(SITE_AREA . '/backup/download/doc/' . $this->db->insert_id()),
				));
				exit;
			}
			Template::set_message('Backup berhasil dibuat.', 'success');
			redirect(SITE_AREA . '/backup?' . http_build_query(array('status' => $status, 'tgl_mulai' => $tgl_mulai, 'tgl_akhir' => $tgl_akhir)));
			return;

		} catch (Exception $e) {
			log_message('error', 'Backup Dokumen gagal: ' . $e->getMessage());
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpZip);
			$msg = 'Backup dokumen gagal dibuat. ' . $e->getMessage();
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}
	}

	// --------------------------------------------------------------------
	// DATABASE PAGE
	// --------------------------------------------------------------------
	public function database_page()
	{
		if ($this->auth->permission_exists($this->permissionDatabase)) {
			$this->auth->restrict($this->permissionDatabase);
		}

		$this->load->model('backup/backup_model');
		$cfg = $this->backup_model->get_database_config();
		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$backup_history = $this->backup_model->get_database_history();

		Template::set('db_name', $cfg['database']);
		Template::set('db_host', $cfg['hostname']);
		Template::set('db_port', $cfg['port']);
		Template::set('backup_date', $wib->format('d-m-Y H:i:s'));
		Template::set('backup_history', $backup_history);
		Template::set('can_database', $this->auth->has_permission($this->permissionDatabase));
		Template::set_view('backup/database');
		Template::render();
	}

	// --------------------------------------------------------------------
	// BACKUP DATABASE — POST, store ZIP + record history, return JSON
	// --------------------------------------------------------------------
	public function database()
	{
		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		if ($this->auth->permission_exists($this->permissionDatabase)) {
			$this->auth->restrict($this->permissionDatabase);
		}

		if (!class_exists('ZipArchive')) {
			$msg = 'ZipArchive tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$this->load->model('backup/backup_model');
		$cfg = $this->backup_model->get_database_config();
		if (empty($cfg['database']) || empty($cfg['username'])) {
			$msg = 'Konfigurasi database tidak lengkap.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$pgDump = $this->find_pg_dump();
		if ($pgDump === false) {
			$msg = 'pg_dump tidak ditemukan.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$disabled = explode(',', ini_get('disable_functions'));
		$disabled = array_map('trim', $disabled);
		if (in_array('exec', $disabled, true)) {
			$msg = 'Fungsi exec dinonaktifkan di server.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$tmpDir = $this->create_tmp_dir('backup_database');
		if ($tmpDir === false) {
			$msg = 'Permission directory tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$tmpSql = $tmpDir . DIRECTORY_SEPARATOR . 'database_backup.sql';
		$zipName = 'backup_database_' . $stamp . '.zip';
		$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

		$host   = $cfg['hostname'] ?: 'localhost';
		$port   = $cfg['port'] ?: '5432';
		$user   = $cfg['username'];
		$dbname = $cfg['database'];
		$pass   = $cfg['password'];

		$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
		if ($isWindows) {
			$safePass = str_replace('"', '""', $pass);
			$cmd = 'set "PGPASSWORD=' . $safePass . '"&& '
				. escapeshellcmd($pgDump)
				. ' -h ' . escapeshellarg($host)
				. ' -p ' . escapeshellarg($port)
				. ' -U ' . escapeshellarg($user)
				. ' -d ' . escapeshellarg($dbname)
				. ' -F p --no-password -f ' . escapeshellarg($tmpSql)
				. ' 2>&1';
		} else {
			$cmd = 'PGPASSWORD=' . escapeshellarg($pass) . ' '
				. escapeshellcmd($pgDump)
				. ' -h ' . escapeshellarg($host)
				. ' -p ' . escapeshellarg($port)
				. ' -U ' . escapeshellarg($user)
				. ' -d ' . escapeshellarg($dbname)
				. ' -F p --no-password -f ' . escapeshellarg($tmpSql)
				. ' 2>&1';
		}

		$output = array();
		$ret = 0;
		@exec($cmd, $output, $ret);

		if ($ret !== 0 || !is_file($tmpSql) || filesize($tmpSql) <= 0) {
			$safeOut = str_replace($pass, '***', implode("\n", $output));
			log_message('error', 'Backup Database pg_dump gagal (ret=' . $ret . '): ' . substr($safeOut, 0, 500));
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			@unlink($tmpZip);
			$msg = 'Backup database gagal dibuat. Periksa koneksi database dan permission pg_dump.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		$zip = new ZipArchive();
		if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			$msg = 'Gagal membuat file ZIP.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}
		$zip->addFile($tmpSql, 'database_backup.sql');
		$zip->close();

		if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			@unlink($tmpZip);
			$msg = 'File ZIP kosong.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		// Store permanently
		$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
		if ($stored_path === false) {
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			@unlink($tmpZip);
			$msg = 'Gagal menyimpan ZIP.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database');
			return;
		}

		// Record history
		$this->backup_model->save_database_history(
			$zipName,
			$stored_path,
			filesize($stored_path),
			'Berhasil'
		);

		$this->cleanup_tmp($tmpDir);
		@unlink($tmpSql);
		@unlink($tmpZip);

		if ($this->input->is_ajax_request()) {
			echo json_encode(array(
				'success' => true,
				'message' => 'Backup database berhasil dibuat.',
				'download_url' => site_url(SITE_AREA . '/backup/download/db/' . $this->db->insert_id()),
			));
			exit;
		}
		Template::set_message('Backup database berhasil dibuat.', 'success');
		redirect(SITE_AREA . '/backup/database');
		return;
	}

	// --------------------------------------------------------------------
	// DOWNLOAD — serve stored ZIP from history
	// --------------------------------------------------------------------
	public function download($type = '', $id = 0)
	{
		$this->load->model('backup/backup_model');

		if ($type === 'doc') {
			$row = $this->backup_model->get_document_history_by_id($id);
		} elseif ($type === 'db') {
			$row = $this->backup_model->get_database_history_by_id($id);
		} else {
			show_404();
			return;
		}

		if (empty($row) || empty($row->file_path)) {
			Template::set_message('File backup tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$path = $row->file_path;
		if (!is_file($path) || filesize($path) <= 0) {
			Template::set_message('File backup sudah tidak tersedia di server.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$zipName = $row->file_name;
		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . basename($zipName) . '"');
		header('Content-Length: ' . filesize($path));
		header('Cache-Control: max-age=0');
		header('Pragma: public');
		header('Expires: 0');
		readfile($path);
		exit;
	}

	// --------------------------------------------------------------------
	// Helpers
	// --------------------------------------------------------------------

	private function create_tmp_dir($prefix)
	{
		$base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . '_' . uniqid('', true);
		$base = preg_replace('/[^a-zA-Z0-9_\-\/\\\\]/', '_', $base);
		if (!mkdir($base, 0755, true) && !is_dir($base)) {
			return false;
		}
		if (!is_writable($base)) {
			return false;
		}
		return $base;
	}

	private function cleanup_tmp($dir)
	{
		if (empty($dir) || !is_dir($dir)) {
			return;
		}
		$realBase = realpath(sys_get_temp_dir());
		$realDir  = realpath($dir);
		if ($realDir === false || strpos($realDir, $realBase) !== 0) {
			return;
		}
		$files = glob($realDir . DIRECTORY_SEPARATOR . '*');
		if ($files) {
			foreach ($files as $f) {
				if (is_file($f)) {
					@unlink($f);
				}
			}
		}
		@rmdir($realDir);
	}

	private function generate_pdf_file($rows, $periode_label, $fullPath)
	{
		$this->load->model('report_pdf/report_model');
		$this->load->library('report_pdf/report_pdf');
		$headers = array('No', 'Kode', 'Customer', 'Produk', 'Jenis', 'Ukuran', 'Warna', 'Jumlah', 'Harga', 'Total', 'Status', 'Tanggal');
		$widths  = array(25, 85, 90, 80, 70, 35, 50, 40, 75, 80, 55, 75);
		$aligns  = array('center', 'left', 'left', 'left', 'left', 'center', 'left', 'center', 'right', 'right', 'center', 'center');

		$data_rows = array();
		foreach ($rows as $r) {
			$data_rows[] = array(
				$r->kode_order,
				$r->nama_customer,
				$r->produk,
				$r->jenis_nama,
				$r->ukuran_nama,
				$r->warna_nama,
				$r->jumlah,
				$r->harga,
				$r->total_harga,
				$r->status_transaksi,
				$r->tanggal,
			);
		}

		$footers = array();
		if (!empty($rows)) {
			$footers[] = 'Total Transaksi: ' . count($rows);
			$footers[] = 'Total Nilai: Rp ' . number_format($this->report_model->grand_total($rows), 0, ',', '.');
		}

		$this->report_pdf->set_data('LAPORAN TRANSAKSI ORDER BAJU', $periode_label, $headers, $widths, $data_rows, $footers, $aligns);
		$pdf = $this->report_pdf->build();

		if (!is_string($pdf) || $pdf === '' || strncmp($pdf, '%PDF', 4) !== 0) {
			log_message('error', 'Backup Dokumen: build PDF tidak valid');
			return false;
		}

		$written = @file_put_contents($fullPath, $pdf);
		if ($written === false || $written <= 0 || !is_file($fullPath)) {
			log_message('error', 'Backup Dokumen: file_put_contents PDF gagal');
			return false;
		}
		$head = @file_get_contents($fullPath, false, null, 0, 4);
		if ($head === false || strncmp($head, '%PDF', 4) !== 0) {
			@unlink($fullPath);
			return false;
		}
		return true;
	}

	private function generate_excel_file($rows, $periode_label, $fullPath)
	{
		$this->load->library('report_excel/report_excel');
		$headers = array('No', 'Kode', 'Customer', 'Produk', 'Jenis', 'Ukuran', 'Warna', 'Jumlah', 'Harga', 'Total', 'Status', 'Tanggal');
		$widths  = array(25, 85, 90, 80, 70, 35, 50, 40, 75, 80, 55, 75);
		$aligns  = array('center', 'left', 'left', 'left', 'left', 'center', 'left', 'center', 'right', 'right', 'center', 'center');

		$data_rows = array();
		$no = 0;
		foreach ($rows as $r) {
			$no++;
			$data_rows[] = array(
				$no,
				$r->kode_order,
				$r->nama_customer,
				$r->produk,
				$r->jenis_nama,
				$r->ukuran_nama,
				$r->warna_nama,
				(int) $r->jumlah,
				(float) $r->harga,
				(float) $r->total_harga,
				$r->status_transaksi,
				$r->tanggal,
			);
		}

		$footers = array();
		if (!empty($rows)) {
			$footers[] = 'Total Transaksi: ' . count($rows);
			$footers[] = 'Total Nilai: Rp ' . number_format($this->report_model->grand_total($rows), 0, ',', '.');
		}

		require_once APPPATH . '../vendor/autoload.php';
		$excelLib = new Report_excel();
		$excelLib->set_data('LAPORAN TRANSAKSI ORDER BAJU', $periode_label, $headers, $widths, $data_rows, $footers, $aligns);
		$saved = $excelLib->save($fullPath);
		if (!$saved || !is_file($fullPath) || filesize($fullPath) <= 0) {
			log_message('error', 'Backup Dokumen: save Excel gagal');
			return false;
		}
		$head = @file_get_contents($fullPath, false, null, 0, 2);
		if ($head === false || strncmp($head, 'PK', 2) !== 0) {
			@unlink($fullPath);
			return false;
		}
		return true;
	}

	private function find_pg_dump()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$out = array();
			@exec('where pg_dump 2>&1', $out, $ret);
			if ($ret === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
				return trim($out[0]);
			}
			$patterns = array(
				'C:\\laragon\\bin\\postgresql\\*\\bin\\pg_dump.exe',
				'C:\\laragon\\bin\\postgresql*\\bin\\pg_dump.exe',
				'C:\\Program Files\\PostgreSQL\\*\\bin\\pg_dump.exe',
				'C:\\Program Files\\postgresql\\*\\bin\\pg_dump.exe',
			);
			foreach ($patterns as $pat) {
				foreach (glob($pat) as $g) {
					if (is_file($g)) return $g;
				}
			}
			return false;
		} else {
			$out = array();
			@exec('which pg_dump 2>&1', $out, $ret);
			if ($ret === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
				return trim($out[0]);
			}
			foreach (array('/usr/bin/pg_dump', '/usr/local/bin/pg_dump', '/opt/postgres/bin/pg_dump') as $p) {
				if (is_file($p) && is_executable($p)) return $p;
			}
			return 'pg_dump';
		}
	}

	private function build_periode_label($tgl_mulai, $tgl_akhir, $status)
	{
		$label = 'Semua';
		if ($tgl_mulai !== '' && $tgl_akhir !== '') {
			$label = date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
		} elseif ($tgl_mulai !== '' && $tgl_akhir === '') {
			$label = '>= ' . date('d-m-Y', strtotime($tgl_mulai));
		} elseif ($tgl_mulai === '' && $tgl_akhir !== '') {
			$label = '<= ' . date('d-m-Y', strtotime($tgl_akhir));
		}
		if ($status !== '') {
			$label .= ' | Status: ' . $status;
		}
		return $label;
	}

	private function normalize_status($status)
	{
		$status = (string) $status;
		return in_array($status, array('Diproses', 'Diambil', 'Selesai'), true) ? $status : '';
	}

	private function normalize_tgl($tgl)
	{
		$tgl = trim((string) $tgl);
		if ($tgl === '') return '';
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tgl, $m)) {
			$iso = $m[1] . '-' . $m[2] . '-' . $m[3];
			return (strtotime($iso) !== false) ? $iso : '';
		}
		if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $tgl, $m)) {
			$iso = $m[3] . '-' . $m[2] . '-' . $m[1];
			return (strtotime($iso) !== false) ? $iso : '';
		}
		return '';
	}
}
