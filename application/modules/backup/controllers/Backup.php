<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Backup controller — 2 fitur terpisah: Backup Dokumen & Backup Database.
 *
 * - Backup Dokumen: reuse Report_model + Report_pdf + Report_excel, filter tgl_mulai/tgl_akhir/status, ZIP 1 PDF+1 XLSX.
 * - Backup Database: pg_dump via PGPASSWORD env, ZIP database_backup.sql.
 */
class Backup extends App_Controller
{
	protected $permissionView     = 'Backup.Backup.View';
	protected $permissionDocument = 'Backup.Backup.Document';
	protected $permissionDatabase = 'Backup.Backup.Database';

	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict($this->permissionView);
		$this->load->model('backup/backup_model');
		$this->load->model('report_pdf/report_model');
		$this->load->library('report_pdf/report_pdf');
		$this->load->library('report_excel/report_excel');
		Template::set('toolbar_title', 'Backup');
		Template::set_block('sub_nav', 'backup/_sub_nav');
	}

	// --------------------------------------------------------------------
	// INDEX
	// --------------------------------------------------------------------
	public function index()
	{
		// Normalize filter for display (GET)
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get('status'));

		// Validate range for UI hint only (actual validation on POST document)
		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
		}

		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('status', $status);
		Template::set('can_document', $this->auth->has_permission($this->permissionDocument));
		Template::set('can_database', $this->auth->has_permission($this->permissionDatabase));
		Template::set_view('backup/index');
		Template::render();
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN — POST only, filter tgl_mulai/tgl_akhir/status
	// --------------------------------------------------------------------
	public function document()
	{
		$this->auth->restrict($this->permissionDocument);

		// Accept both POST and GET (form POST, but allow GET for testing)
		$tgl_mulai = $this->normalize_tgl($this->input->get_post('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get_post('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get_post('status'));

		// Validate
		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}
		if (($tgl_mulai !== '' && $tgl_akhir === '') || ($tgl_mulai === '' && $tgl_akhir !== '')) {
			// Allow single bound — Report_model handles >= or <=. No error.
		}

		// Ambil data via Report logic existing (no duplikasi query)
		$rows = $this->backup_model->get_transaksi_for_backup($tgl_mulai, $tgl_akhir, $status);

		if (empty($rows)) {
			Template::set_message('Tidak ada data transaksi untuk periode/status yang dipilih.', 'warning');
			redirect(SITE_AREA . '/backup?status=' . urlencode($status) . '&tgl_mulai=' . urlencode($tgl_mulai) . '&tgl_akhir=' . urlencode($tgl_akhir));
			return;
		}

		// Check ZipArchive
		if (!class_exists('ZipArchive')) {
			log_message('error', 'Backup Dokumen: ZipArchive tidak tersedia.');
			Template::set_message('Backup dokumen gagal dibuat. Ekstensi ZipArchive tidak tersedia di server.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		// Create tmp dir
		$tmpDir = $this->create_tmp_dir('backup_dokumen');
		if ($tmpDir === false) {
			log_message('error', 'Backup Dokumen: gagal membuat folder temporary.');
			Template::set_message('Backup dokumen gagal dibuat. Permission directory tidak tersedia.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$zipName = 'backup_dokumen_' . $stamp . '.zip';
		$tmpZip  = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $zipName;

		// Build label for report header
		$periode_label = $this->build_periode_label($tgl_mulai, $tgl_akhir, $status);

		try {
			// --- Generate PDF (reuse Report_pdf) ---
			$pdfFile = $tmpDir . DIRECTORY_SEPARATOR . 'laporan_transaksi_' . $wib->format('Y-m-d_His') . '.pdf';
			$pdfOk = $this->generate_pdf_file($rows, $periode_label, $pdfFile);
			if (!$pdfOk) {
				throw new Exception('Gagal generate PDF');
			}

			// --- Generate Excel (reuse Report_excel) ---
			$xlsxFile = $tmpDir . DIRECTORY_SEPARATOR . 'laporan_transaksi_' . $wib->format('Y-m-d_His') . '.xlsx';
			$xlsxOk = $this->generate_excel_file($rows, $periode_label, $xlsxFile);
			if (!$xlsxOk) {
				throw new Exception('Gagal generate Excel');
			}

			// --- ZIP ---
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

			// Verify ZIP header PK
			$head = @file_get_contents($tmpZip, false, null, 0, 2);
			if ($head === false || strncmp($head, 'PK', 2) !== 0) {
				@unlink($tmpZip);
				throw new Exception('ZIP tidak valid');
			}

			// Download
			$this->send_zip_download($tmpZip, $zipName);

			// Cleanup after download (register_shutdown + manual)
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpZip);
			exit;

		} catch (Exception $e) {
			log_message('error', 'Backup Dokumen gagal: ' . $e->getMessage());
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpZip);
			Template::set_message('Backup dokumen gagal dibuat. ' . $e->getMessage() . '. Silakan periksa data transaksi dan konfigurasi server.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}
	}

	// --------------------------------------------------------------------
	// BACKUP DATABASE — POST only, pg_dump full
	// --------------------------------------------------------------------
	public function database()
	{
		$this->auth->restrict($this->permissionDatabase);

		if (!class_exists('ZipArchive')) {
			log_message('error', 'Backup Database: ZipArchive tidak tersedia.');
			Template::set_message('Backup database gagal dibuat. Ekstensi ZipArchive tidak tersedia di server.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$cfg = $this->backup_model->get_database_config();
		if (empty($cfg['database']) || empty($cfg['username'])) {
			log_message('error', 'Backup Database: konfigurasi database tidak lengkap.');
			Template::set_message('Backup database gagal dibuat. Konfigurasi database tidak lengkap.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$pgDump = $this->find_pg_dump();
		if ($pgDump === false) {
			log_message('error', 'Backup Database: pg_dump tidak ditemukan.');
			Template::set_message('Backup database gagal dibuat. Tool pg_dump tidak ditemukan di server. Hubungi administrator.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		// Check exec availability
		$disabled = explode(',', ini_get('disable_functions'));
		$disabled = array_map('trim', $disabled);
		if (in_array('exec', $disabled, true)) {
			log_message('error', 'Backup Database: exec disabled.');
			Template::set_message('Backup database gagal dibuat. Fungsi exec dinonaktifkan di server.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$tmpDir = $this->create_tmp_dir('backup_database');
		if ($tmpDir === false) {
			log_message('error', 'Backup Database: gagal membuat folder temporary.');
			Template::set_message('Backup database gagal dibuat. Permission directory tidak tersedia.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$tmpSql = $tmpDir . DIRECTORY_SEPARATOR . 'database_backup.sql';
		$zipName = 'backup_database_' . $stamp . '.zip';
		$tmpZip  = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $zipName;

		// Build pg_dump command — PGPASSWORD via env, escapeshellarg semua param
		$host = $cfg['hostname'] ?: 'localhost';
		$port = $cfg['port'] ?: '5432';
		$user = $cfg['username'];
		$dbname = $cfg['database'];
		$pass = $cfg['password'];

		// Use temp file for password to avoid leaking in ps aux on some OS, but PGPASSWORD env is standard.
		// We use escapeshellarg for password in env assignment.
		$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

		if ($isWindows) {
			// Windows: set PGPASSWORD=xxx&& pg_dump ...
			$cmd = 'set PGPASSWORD=' . escapeshellarg($pass) . '&& '
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
			// Jangan log password; log output tanpa password
			$safeOut = implode("\n", $output);
			// Strip potential password leak if any
			$safeOut = str_replace($pass, '***', $safeOut);
			log_message('error', 'Backup Database pg_dump gagal (ret=' . $ret . '): ' . substr($safeOut, 0, 500));
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			@unlink($tmpZip);
			Template::set_message('Backup database gagal dibuat. Periksa koneksi database dan permission pg_dump.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		// Verify SQL header (optional)
		$head = @file_get_contents($tmpSql, false, null, 0, 200);
		if ($head === false || (stripos($head, 'PostgreSQL database dump') === false && stripos($head, 'CREATE TABLE') === false && stripos($head, 'SET ') === false)) {
			// Not fatal — pg_dump plain may start with SET etc. Just log if suspicious.
			log_message('debug', 'Backup Database: sql header tidak mengandung marker dump biasa, tapi lanjut ZIP.');
		}

		// ZIP
		$zip = new ZipArchive();
		if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
			log_message('error', 'Backup Database: gagal membuat ZIP.');
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			Template::set_message('Backup database gagal dibuat. Gagal membuat file ZIP.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}
		$zip->addFile($tmpSql, 'database_backup.sql');
		$zip->close();

		if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
			log_message('error', 'Backup Database: ZIP kosong.');
			$this->cleanup_tmp($tmpDir);
			@unlink($tmpSql);
			@unlink($tmpZip);
			Template::set_message('Backup database gagal dibuat. File ZIP kosong.', 'error');
			redirect(SITE_AREA . '/backup');
			return;
		}

		// Download
		$this->send_zip_download($tmpZip, $zipName);

		// Cleanup
		$this->cleanup_tmp($tmpDir);
		@unlink($tmpSql);
		@unlink($tmpZip);
		exit;
	}

	// --------------------------------------------------------------------
	// Helpers
	// --------------------------------------------------------------------

	private function create_tmp_dir($prefix)
	{
		$base = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $prefix . '_' . uniqid('', true);
		// Sanitize prefix
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
		// Prevent traversal — must be inside sys_get_temp_dir
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

	private function send_zip_download($filePath, $zipName)
	{
		// Sanitize zip name
		$zipName = basename($zipName);
		$zipName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $zipName);
		// Clear output buffers
		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $zipName . '"');
		header('Content-Length: ' . filesize($filePath));
		header('Cache-Control: max-age=0');
		header('Pragma: public');
		header('Expires: 0');
		readfile($filePath);
	}

	private function generate_pdf_file($rows, $periode_label, $fullPath)
	{
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
			log_message('error', 'Backup Dokumen: build PDF tidak valid (len=' . strlen((string) $pdf) . ').');
			return false;
		}

		$written = @file_put_contents($fullPath, $pdf);
		if ($written === false || $written <= 0 || !is_file($fullPath)) {
			log_message('error', 'Backup Dokumen: file_put_contents PDF gagal: ' . $fullPath);
			return false;
		}
		// Verify
		$head = @file_get_contents($fullPath, false, null, 0, 4);
		if ($head === false || strncmp($head, '%PDF', 4) !== 0) {
			@unlink($fullPath);
			return false;
		}
		return true;
	}

	private function generate_excel_file($rows, $periode_label, $fullPath)
	{
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

		// Need fresh instance — report_excel reuses Spreadsheet object; create new instance
		// Ponytail: destroy old and create new via new Report_excel
		require_once APPPATH . '../vendor/autoload.php';
		$excelLib = new Report_excel();
		$excelLib->set_data('LAPORAN TRANSAKSI ORDER BAJU', $periode_label, $headers, $widths, $data_rows, $footers, $aligns);
		$saved = $excelLib->save($fullPath);
		if (!$saved || !is_file($fullPath) || filesize($fullPath) <= 0) {
			log_message('error', 'Backup Dokumen: save Excel gagal: ' . $fullPath);
			return false;
		}
		$head = @file_get_contents($fullPath, false, null, 0, 2);
		if ($head === false || strncmp($head, 'PK', 2) !== 0) {
			@unlink($fullPath);
			log_message('error', 'Backup Dokumen: Excel header tidak valid: ' . $fullPath);
			return false;
		}
		return true;
	}

	private function find_pg_dump()
	{
		$candidates = array('pg_dump');
		// Common Windows Laragon/Postgres paths
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$candidates[] = 'C:\\laragon\\bin\\postgresql\\bin\\pg_dump.exe';
			// Try to find via where
			$out = array();
			@exec('where pg_dump 2>&1', $out, $ret);
			if ($ret === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
				return trim($out[0]);
			}
			foreach ($candidates as $c) {
				if (is_file($c) && is_executable($c)) {
					return $c;
				}
			}
			// Glob for versioned postgres
			foreach (glob('C:\\laragon\\bin\\postgresql*\\bin\\pg_dump.exe') as $g) {
				if (is_file($g)) return $g;
			}
			foreach (glob('C:\\Program Files\\PostgreSQL*\\bin\\pg_dump.exe') as $g) {
				if (is_file($g)) return $g;
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
