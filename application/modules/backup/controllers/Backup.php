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
	// INDEX â€” Filter Riwayat Cetak + Riwayat Backup Dokumen
	// --------------------------------------------------------------------
	public function index()
	{
		$this->load->model('backup/backup_model');

		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
		}

		// Report (PDF/Excel) DIAKTIFKAN KEMBALI.
		$riwayat_cetak = $this->backup_model->get_riwayat_cetak($tgl_mulai, $tgl_akhir);
		$dokumen_transaksi = $this->backup_model->get_dokumen_transaksi($tgl_mulai, $tgl_akhir);
		$backup_history = $this->backup_model->get_document_history();

		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('riwayat_cetak', $riwayat_cetak);
		Template::set('dokumen_transaksi', $dokumen_transaksi);
		Template::set('backup_history', $backup_history);
		Template::set('can_document', $this->auth->has_permission($this->permissionDocument));
		Template::set('can_database', $this->auth->has_permission($this->permissionDatabase));
		Template::set_view('backup/index');
		Template::render();
	}

	// --------------------------------------------------------------------
	// AJAX FILTER â€” return Riwayat Cetak rows as JSON
	// --------------------------------------------------------------------
	public function filter()
	{
		$this->load->model('backup/backup_model');

		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		// Report (PDF/Excel) DIAKTIFKAN KEMBALI.
		$riwayat_cetak = $this->backup_model->get_riwayat_cetak($tgl_mulai, $tgl_akhir);
		$dokumen_transaksi = $this->backup_model->get_dokumen_transaksi($tgl_mulai, $tgl_akhir);

		$rows = array();
		foreach ($riwayat_cetak as $r) {
			$tipe_badge = $r->tipe_report === 'pdf'
				? '<span class="badge badge-danger"><i class="fas fa-file-pdf"></i> PDF</span>'
				: '<span class="badge badge-success"><i class="fas fa-file-excel"></i> Excel</span>';
			$rows[] = array(
				'source' => 'report',
				'id' => (int) $r->id,
				'created_on' => html_escape($r->created_on_str),
				'tipe_badge' => $tipe_badge,
				'nama_file' => html_escape($r->nama_file),
				'jumlah_transaksi' => (int) $r->jumlah_transaksi,
			);
		}

		foreach ($dokumen_transaksi as $d) {
			$tipe_badge = '<span class="badge badge-info"><i class="fas fa-folder-open"></i> Dokumen Transaksi</span>';
			$rows[] = array(
				'source' => 'transaksi',
				'id' => (int) $d->id,
				'created_on' => html_escape($d->created_on_str),
				'tipe_badge' => $tipe_badge,
				'nama_file' => html_escape($d->nama_file),
				'jumlah_transaksi' => (int) $d->jumlah_transaksi,
			);
		}

		echo json_encode(array('success' => true, 'data' => $rows, 'total' => count($rows)));
		exit;
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN â€” POST, copy existing files from Riwayat Cetak, return JSON
	// --------------------------------------------------------------------
	public function document()
	{
		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup');
			return;
		}

		$this->load->model('backup/backup_model');

		// Report (PDF/Excel) DIAKTIFKAN KEMBALI.
		$report_ids = $this->input->post('report_ids');
		$trx_docs   = $this->input->post('trx_docs');
		if ((!is_array($report_ids) || empty($report_ids)) && (!is_array($trx_docs) || empty($trx_docs))) {
			$msg = 'Tidak ada dokumen yang dipilih.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup');
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

		// Fetch selected reports
		$reports = array();
		if (is_array($report_ids) && !empty($report_ids)) {
			$reports = $this->backup_model->get_reports_by_ids($report_ids);
		}

		// Kumpulkan dokumen transaksi terpilih: "id:nama_file".
		// VALIDASI: nama file harus benar-benar terdaftar di kolom dokumen transaksi tsb
		// (mencegah user menyebut path/file sewenang-wenang).
		$trx_items = array();
		if (is_array($trx_docs) && !empty($trx_docs)) {
			$trx_items = $this->backup_model->get_transaksi_docs_selected($trx_docs);
		}

		if (empty($reports) && empty($trx_items)) {
			$msg = 'Data dokumen yang dipilih tidak ditemukan.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$zipName = 'backup_dokumen_' . $stamp . '.zip';
		$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

		try {
			$zip = new ZipArchive();
			if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
				throw new Exception('Gagal membuat ZIP');
			}

			$added = array();
			$missing = array();

			// Bagian A: Report PDF/Excel â€” masuk folder "dokumen/report/".
			foreach ($reports as $report) {
				$fileName = trim((string) $report->nama_file);

				// path_file kosong = report lama (mis. export CSV) tanpa file fisik.
				if (trim((string) $report->path_file) === '') {
					$missing[] = '/report/' . ($fileName !== '' ? $fileName : ('#' . $report->id));
					log_message('error', 'Backup Dokumen: report #' . $report->id . ' tidak punya path_file (file fisik tidak ada).');
					continue;
				}

				$filePath = APPPATH . '../' . ltrim((string) $report->path_file, '/\\');
				if ($fileName === '' || !is_file($filePath) || filesize($filePath) <= 0) {
					$missing[] = '/report/' . ($fileName !== '' ? $fileName : '#' . $report->id);
					log_message('error', 'Backup Dokumen: file report tidak ditemukan â€” ' . $report->path_file);
					continue;
				}

				$zipNameEntry = 'dokumen/report/' . $fileName;
				$zip->addFile($filePath, $zipNameEntry);
				$added[] = $zipNameEntry;
			}

			// Bagian B: Dokumen transaksi (upload user) â€” masuk folder "dokumen/dokumen_transaksi/[id]/".
			foreach ($trx_items as $item) {
				$filePath = $this->resolve_transaksi_file((int) $item->id, $item->nama_file);
				$fileName = basename((string) $item->nama_file);

				if ($filePath === null || !is_file($filePath) || filesize($filePath) <= 0) {
					$missing[] = 'dokumen/dokumen_transaksi/' . (int) $item->id . '/' . $fileName;
					log_message('error', 'Backup Dokumen: file transaksi tidak ditemukan â€” transaksi #' . (int) $item->id . ', file ' . $fileName);
					continue;
				}

				$zipNameEntry = 'dokumen/dokumen_transaksi/' . (int) $item->id . '/' . $fileName;
				$zip->addFile($filePath, $zipNameEntry);
				$added[] = $zipNameEntry;
			}

			if (empty($added)) {
				$zip->close();
				@unlink($tmpZip);
				$missingList = implode(', ', $missing);
				throw new Exception('Semua file tidak ditemukan di server. (' . $missingList . ')');
			}

			$zip->close();

			// Validate ZIP
			if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
				throw new Exception('ZIP kosong');
			}

			$head = @file_get_contents($tmpZip, false, null, 0, 2);
			if ($head === false || strncmp($head, 'PK', 2) !== 0) {
				@unlink($tmpZip);
				throw new Exception('ZIP tidak valid');
			}

			// Verify ZIP entry count matches added files
			$zipVerify = new ZipArchive();
			if ($zipVerify->open($tmpZip) === true) {
				$zipCount = $zipVerify->numFiles;
				$zipVerify->close();
			} else {
				$zipCount = count($added);
			}
			if ($zipCount !== count($added)) {
				@unlink($tmpZip);
				throw new Exception('Jumlah file ZIP (' . $zipCount . ') tidak cocok dengan file berhasil (' . count($added) . ')');
			}

			// Store permanently
			$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
			if ($stored_path === false) {
				throw new Exception('Gagal menyimpan ZIP');
			}

			// Build filter label from dates
			$tgl_mulai = $this->normalize_tgl($this->input->post('tgl_mulai'));
			$tgl_akhir = $this->normalize_tgl($this->input->post('tgl_akhir'));
			$filter_label = $this->build_filter_label($tgl_mulai, $tgl_akhir);

			// Record history
			$this->backup_model->save_document_history(
				$zipName,
				$stored_path,
				filesize($stored_path),
				count($added),
				$filter_label
			);

			@unlink($tmpZip);

			$msg = 'Backup berhasil dibuat. ' . count($added) . ' file berhasil diarsipkan.';
			if (!empty($missing)) {
				$msg .= ' ' . count($missing) . ' file tidak ditemukan: ' . implode(', ', $missing);
			}

			if ($this->input->is_ajax_request()) {
				echo json_encode(array(
					'success' => true,
					'message' => $msg,
					'download_url' => site_url(SITE_AREA . '/backup/download/doc/' . $this->db->insert_id()),
				));
				exit;
			}

			// Non-AJAX: download ZIP directly
			$storedFile = $stored_path;
			if (is_file($storedFile) && filesize($storedFile) > 0) {
				while (ob_get_level() > 0) { @ob_end_clean(); }
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . $zipName . '"');
				header('Content-Length: ' . filesize($storedFile));
				header('Cache-Control: max-age=0');
				readfile($storedFile);
				exit;
			}
			Template::set_message($msg, 'success');
			redirect(SITE_AREA . '/backup');
			return;

		} catch (Exception $e) {
			log_message('error', 'Backup Dokumen gagal: ' . $e->getMessage());
			@unlink($tmpZip);
			$msg = 'Backup dokumen gagal. ' . $e->getMessage();
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
	// BACKUP DOKUMEN PER ID â€” page
	// --------------------------------------------------------------------
	public function dokumen_per_id()
	{
		Template::set_block('sub_nav', 'backup/_sub_nav');
		$this->load->model('backup/backup_model');

		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		if ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
			Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
		}

		$dokumen = $this->backup_model->get_dokumen_per_id($tgl_mulai, $tgl_akhir);
		$backup_history = $this->backup_model->get_document_history();

		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('dokumen', $dokumen);
		Template::set('backup_history', $backup_history);
		Template::set('can_document', $this->auth->has_permission($this->permissionDocument));
		Template::set('toolbar_title', 'Backup Dokumen ID');
		Template::set_view('backup/dokumen_per_id');
		Template::render();
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN PER ID â€” POST process
	// --------------------------------------------------------------------
	public function dokumen_per_id_process()
	{
		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup/per_id');
			return;
		}

		$this->load->model('backup/backup_model');

		$ids = $this->input->post('ids');
		if (!is_array($ids) || empty($ids)) {
			$msg = 'Tidak ada ID transaksi yang dipilih.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup/per_id');
			return;
		}

		if (!class_exists('ZipArchive')) {
			$msg = 'ZipArchive tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/per_id');
			return;
		}

		// Build map id => [file names] from DB. Backend determines the files
		// for each ID â€” never trusts arbitrary paths from the request.
		$map = $this->backup_model->get_transaksi_files_by_ids($ids);
		if (empty($map)) {
			$msg = 'Data dokumen untuk ID yang dipilih tidak ditemukan.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup/per_id');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$zipName = 'backup_dokumen_per_id_' . $stamp . '.zip';
		$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

		try {
			$zip = new ZipArchive();
			if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
				throw new Exception('Gagal membuat ZIP');
			}

			$added = array();
			$missing = array();

			foreach ($map as $id => $files) {
				$id = (int) $id;
				foreach ($files as $file) {
					$filePath = $this->resolve_transaksi_file($id, $file);
					$fileName = basename((string) $file);

					if ($filePath === null || !is_file($filePath) || filesize($filePath) <= 0) {
						$missing[] = 'ID_' . $id . '/' . $fileName;
						log_message('error', 'Backup Per ID: file tidak ditemukan â€” transaksi #' . $id . ', file ' . $fileName);
						continue;
					}

					$zipEntry = 'ID_' . $id . '/' . $fileName;
					$zip->addFile($filePath, $zipEntry);
					$added[] = $zipEntry;
				}
			}

			if (empty($added)) {
				$zip->close();
				@unlink($tmpZip);
				throw new Exception('Semua file tidak ditemukan di server.');
			}

			$zip->close();
			if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
				throw new Exception('ZIP kosong');
			}
			$head = @file_get_contents($tmpZip, false, null, 0, 2);
			if ($head === false || strncmp($head, 'PK', 2) !== 0) {
				@unlink($tmpZip);
				throw new Exception('ZIP tidak valid');
			}

			$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
			if ($stored_path === false) {
				throw new Exception('Gagal menyimpan ZIP');
			}

			$this->backup_model->save_document_history(
				$zipName,
				$stored_path,
				filesize($stored_path),
				count($added),
				'Per ID: ' . implode(', ', array_keys($map))
			);

			@unlink($tmpZip);

			$msg = 'Backup dokumen per ID berhasil. ' . count($added) . ' file diarsipkan dari ' . count($map) . ' ID.';
			if (!empty($missing)) {
				$msg .= ' ' . count($missing) . ' file tidak ditemukan: ' . implode(', ', $missing);
			}

			if ($this->input->is_ajax_request()) {
				echo json_encode(array(
					'success' => true,
					'message' => $msg,
					'download_url' => site_url(SITE_AREA . '/backup/download/doc/' . $this->db->insert_id()),
				));
				exit;
			}

			$storedFile = $stored_path;
			if (is_file($storedFile) && filesize($storedFile) > 0) {
				while (ob_get_level() > 0) { @ob_end_clean(); }
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . $zipName . '"');
				header('Content-Length: ' . filesize($storedFile));
				header('Cache-Control: max-age=0');
				readfile($storedFile);
				exit;
			}
			Template::set_message($msg, 'success');
			redirect(SITE_AREA . '/backup/per_id');
			return;

		} catch (Exception $e) {
			log_message('error', 'Backup Dokumen per ID gagal: ' . $e->getMessage());
			@unlink($tmpZip);
			$msg = 'Backup dokumen per ID gagal. ' . $e->getMessage();
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/per_id');
			return;
		}
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN PER FOLDER â€” page
	// --------------------------------------------------------------------
	public function dokumen_per_folder()
	{
		Template::set_block('sub_nav', 'backup/_sub_nav');
		$this->load->model('backup/backup_model');

		// Define the selectable folders. Backend fixes the real paths â€” the
		// request never supplies a free-form path.
		$trxRoot = FCPATH . 'assets/dokumen/dokumen_transaksi/';
		$reportRoot = FCPATH . 'assets/dokumen/report/';

		$folders = array(
			'transaksi' => array(
				'label' => 'Dokumen Transaksi',
				'path'  => $trxRoot,
				'icon'  => 'fas fa-folder-open',
			),
			'report' => array(
				'label' => 'Report',
				'path'  => $reportRoot,
				'icon'  => 'fas fa-file-alt',
			),
		);

		// Determine presence + counts per folder for display.
		foreach ($folders as $key => $folder) {
			$count = 0;
			if (is_dir($folder['path'])) {
				$count = $this->count_files_recursive($folder['path']);
			}
			$folders[$key]['exists'] = is_dir($folder['path']);
			$folders[$key]['count'] = $count;
		}

		$backup_history = $this->backup_model->get_document_history();

		Template::set('folders', $folders);
		Template::set('backup_history', $backup_history);
		Template::set('can_document', $this->auth->has_permission($this->permissionDocument));
		Template::set('toolbar_title', 'Backup Dokumen Folder');
		Template::set_view('backup/dokumen_per_folder');
		Template::render();
	}

	// --------------------------------------------------------------------
	// BACKUP DOKUMEN PER FOLDER â€” POST process (recursive)
	// --------------------------------------------------------------------
	public function dokumen_per_folder_process()
	{
		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup/per_folder');
			return;
		}

		$this->load->model('backup/backup_model');

		$selected = $this->input->post('folders');
		if (!is_array($selected) || empty($selected)) {
			$msg = 'Tidak ada folder yang dipilih.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'warning');
			redirect(SITE_AREA . '/backup/per_folder');
			return;
		}

		if (!class_exists('ZipArchive')) {
			$msg = 'ZipArchive tidak tersedia.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/per_folder');
			return;
		}

		// Only allow whitelisted folders. Backend resolves the real path.
		$allowed = array(
			'transaksi' => FCPATH . 'assets/dokumen/dokumen_transaksi/',
			'report'    => FCPATH . 'assets/dokumen/report/',
		);

		$chosen = array();
		foreach ($selected as $key) {
			$key = (string) $key;
			if (isset($allowed[$key])) {
				$chosen[$key] = $allowed[$key];
			}
		}
		if (empty($chosen)) {
			$msg = 'Folder yang dipilih tidak valid.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/per_folder');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$zipName = 'backup_dokumen_per_folder_' . $stamp . '.zip';
		$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

		try {
			$zip = new ZipArchive();
			if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
				throw new Exception('Gagal membuat ZIP');
			}

			$added = array();
			$skippedEmpty = array();

			foreach ($chosen as $key => $rootPath) {
				$rootPath = rtrim($rootPath, '/\\');
				// Strip any symlink/security concern â€” rootPath is backend-defined.
				$label = ($key === 'transaksi') ? 'dokumen_transaksi' : 'report';

				if (!is_dir($rootPath)) {
					$skippedEmpty[] = $label;
					continue;
				}

				$localBaseReal = realpath($rootPath);
				$this->add_dir_recursive($zip, $rootPath, $label, $localBaseReal, $added);
			}

			if (empty($added)) {
				$zip->close();
				@unlink($tmpZip);
				if (!empty($skippedEmpty)) {
					throw new Exception('Folder yang dipilih kosong atau tidak tersedia: ' . implode(', ', $skippedEmpty));
				}
				throw new Exception('Tidak ada file yang ditemukan di folder terpilih.');
			}

			$zip->close();
			if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
				throw new Exception('ZIP kosong');
			}
			$head = @file_get_contents($tmpZip, false, null, 0, 2);
			if ($head === false || strncmp($head, 'PK', 2) !== 0) {
				@unlink($tmpZip);
				throw new Exception('ZIP tidak valid');
			}

			$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
			if ($stored_path === false) {
				throw new Exception('Gagal menyimpan ZIP');
			}

			$this->backup_model->save_document_history(
				$zipName,
				$stored_path,
				filesize($stored_path),
				count($added),
				'Per Folder: ' . implode(', ', array_keys($chosen))
			);

			@unlink($tmpZip);

			$msg = 'Backup dokumen per folder berhasil. ' . count($added) . ' file diarsipkan.';
			if (!empty($skippedEmpty)) {
				$msg .= ' Folder kosong/tidak tersedia: ' . implode(', ', $skippedEmpty) . '.';
			}

			if ($this->input->is_ajax_request()) {
				echo json_encode(array(
					'success' => true,
					'message' => $msg,
					'download_url' => site_url(SITE_AREA . '/backup/download/doc/' . $this->db->insert_id()),
				));
				exit;
			}

			$storedFile = $stored_path;
			if (is_file($storedFile) && filesize($storedFile) > 0) {
				while (ob_get_level() > 0) { @ob_end_clean(); }
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . $zipName . '"');
				header('Content-Length: ' . filesize($storedFile));
				header('Cache-Control: max-age=0');
				readfile($storedFile);
				exit;
			}
			Template::set_message($msg, 'success');
			redirect(SITE_AREA . '/backup/per_folder');
			return;

		} catch (Exception $e) {
			log_message('error', 'Backup Dokumen per Folder gagal: ' . $e->getMessage());
			@unlink($tmpZip);
			$msg = 'Backup dokumen per folder gagal. ' . $e->getMessage();
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/per_folder');
			return;
		}
	}

	// --------------------------------------------------------------------
	// DATABASE PAGE
	// --------------------------------------------------------------------
	public function database_page()
	{
		Template::set_block('sub_nav', 'backup/_sub_nav');
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
	// BACKUP DATABASE â€” POST, store ZIP + record history, return JSON
	// Uses proc_open() for clean stdout/stderr separation.
	// pg_dump output is written directly to file with ZERO modification.
	// --------------------------------------------------------------------
	public function database()
	{
		if ($this->input->method() !== 'post') {
			redirect(SITE_AREA . '/backup/database-page');
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
			redirect(SITE_AREA . '/backup/database-page');
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
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$pgDump = $this->find_pg_dump();
		if ($pgDump === false) {
			$msg = 'pg_dump tidak ditemukan di server.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$disabled = explode(',', ini_get('disable_functions'));
		$disabled = array_map('trim', $disabled);
		if (in_array('proc_open', $disabled, true) || in_array('exec', $disabled, true)) {
			$msg = 'Fungsi proc_open/exec dinonaktifkan di server.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$backupBase = APPPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
		$tmpDir = $backupBase . 'tmp' . DIRECTORY_SEPARATOR . uniqid('db_', true);
		if (!mkdir($tmpDir, 0755, true) && !is_dir($tmpDir)) {
			$msg = 'Gagal membuat folder temporary.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$stamp = $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5);
		$tmpSql = $tmpDir . DIRECTORY_SEPARATOR . 'database_backup.sql';
		$zipName = 'backup_database_' . $stamp . '.zip';
		$tmpZip  = $tmpDir . DIRECTORY_SEPARATOR . $zipName;

		$host   = $cfg['hostname'] ?: 'localhost';
		$port   = $cfg['port'] ?: '5432';
		$user   = $cfg['username'];
		$dbname = $cfg['database'];
		$pass   = $cfg['password'];

		// Build pg_dump args (password via PGPASSWORD env, never on CLI)
		$args = array(
			'-h', $host,
			'-p', $port,
			'-U', $user,
			'-d', $dbname,
			'-F', 'p',
			'--no-owner',
			'--no-privileges',
			'--no-password',
			'--inserts',
			'-f', $tmpSql,
		);

		// ponytail: pass essential env vars for Windows proc_open
		$env = array(
			'PATH' => getenv('PATH'),
			'PGPASSWORD' => $pass,
			'SystemRoot' => getenv('SystemRoot'),
			'COMSPEC' => getenv('COMSPEC'),
			'WINDIR' => getenv('WINDIR'),
		);
		$descriptors = array(
			0 => array('pipe', 'r'),  // stdin
			1 => array('pipe', 'w'),  // stdout â€” pg_dump writes to -f file, stdout is empty
			2 => array('pipe', 'w'),  // stderr â€” captured separately
		);

		// ponytail: use array command to avoid cmd.exe space-mangling on Windows
		$cmdArray = array_merge(array($pgDump), $args);
		$process = proc_open($cmdArray, $descriptors, $pipes, null, $env);
		if (!is_resource($process)) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'Gagal menjalankan pg_dump.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		// Close stdin â€” pg_dump doesn't read from it
		fclose($pipes[0]);

		// Read stdout (empty since -f writes to file)
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		// Read stderr â€” captured separately, never mixed into SQL
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		$exitCode = proc_close($process);

		// Log stderr for debugging (password stripped)
		if ($stderr !== '') {
			$safeStderr = str_replace($pass, '***', $stderr);
			log_message('info', 'Backup Database pg_dump stderr: ' . substr($safeStderr, 0, 500));
		}

		// Validate: exit code, file exists, non-empty, valid header
		$sqlValid = false;
		if ($exitCode === 0 && is_file($tmpSql) && filesize($tmpSql) > 0) {
			$head = @file_get_contents($tmpSql, false, null, 0, 512);
			if ($head !== false && preg_match('/^(--|SET|\/\*|CREATE)/', $head)) {
				$sqlValid = true;
			} else {
				log_message('error', 'Backup Database: SQL header tidak valid: ' . substr($head, 0, 200));
			}
		}

		if (!$sqlValid) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'Backup database gagal dibuat. ';
			if ($exitCode !== 0) {
				$msg .= 'pg_dump exit code ' . $exitCode . '. ';
			} elseif (!is_file($tmpSql) || filesize($tmpSql) <= 0) {
				$msg .= 'File SQL kosong. ';
			} else {
				$msg .= 'Output pg_dump bukan SQL valid. ';
			}
			$msg .= 'Periksa koneksi database, versi pg_dump, dan permission.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		// --- Strip PG18 \restrict/\unrestrict â€” incompatible with older PG versions ---
		$sqlContent = file_get_contents($tmpSql);
		$cleaned = preg_replace('/^\\\\(un)?restrict\s+\S+\s*$/m', '', $sqlContent);
		if ($cleaned !== $sqlContent) {
			file_put_contents($tmpSql, $cleaned);
		}

		// --- Test restore ke database temporary (skip if BACKUP_SKIP_TEST=1) ---
		$skipTest = getenv('BACKUP_SKIP_TEST') === '1';
		if (!$skipTest) {
			$testDb = 'backup_test_' . uniqid('', true);
			$restoreOk = $this->test_restore($testDb, $tmpSql, $cfg, $pgDump, $pass);

			if (!$restoreOk) {
				$this->cleanup_tmp($tmpDir);
				$msg = 'Backup database lolos validasi pg_dump tetapi gagal saat test restore. File tidak disimpan.';
				if ($this->input->is_ajax_request()) {
					echo json_encode(array('success' => false, 'message' => $msg));
					exit;
				}
				Template::set_message($msg, 'error');
				redirect(SITE_AREA . '/backup/database-page');
				return;
			}
		}

		// --- Create ZIP ---
		$zip = new ZipArchive();
		if ($zip->open($tmpZip, ZipArchive::CREATE) !== true) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'Gagal membuat file ZIP.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}
		$zip->addFile($tmpSql, 'database_backup.sql');
		$zip->close();

		if (!is_file($tmpZip) || filesize($tmpZip) <= 0) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'File ZIP kosong.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$zipHead = @file_get_contents($tmpZip, false, null, 0, 2);
		if ($zipHead === false || strncmp($zipHead, 'PK', 2) !== 0) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'File ZIP tidak valid.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		// Store permanently
		$stored_path = $this->backup_model->store_zip($tmpZip, $zipName);
		if ($stored_path === false) {
			$this->cleanup_tmp($tmpDir);
			$msg = 'Gagal menyimpan ZIP.';
			if ($this->input->is_ajax_request()) {
				echo json_encode(array('success' => false, 'message' => $msg));
				exit;
			}
			Template::set_message($msg, 'error');
			redirect(SITE_AREA . '/backup/database-page');
			return;
		}

		$this->backup_model->save_database_history(
			$zipName,
			$stored_path,
			filesize($stored_path),
			'Berhasil'
		);

		$this->cleanup_tmp($tmpDir);

		if ($this->input->is_ajax_request()) {
			$msg = $skipTest ? 'Backup database berhasil dibuat.' : 'Backup database berhasil dibuat. Test restore: lulus.';
			echo json_encode(array(
				'success' => true,
				'message' => $msg,
				'download_url' => site_url(SITE_AREA . '/backup/download/db/' . $this->db->insert_id()),
			));
			exit;
		}
		Template::set_message('Backup database berhasil dibuat.', 'success');
		redirect(SITE_AREA . '/backup/database-page');
		return;
	}

	// --------------------------------------------------------------------
	// DOWNLOAD â€” serve stored ZIP from history
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

	/**
	 * Resolve path fisik file dokumen transaksi.
	 *
	 * Urutan pencarian (sesuai struktur penyimpanan saat ini & historis):
	 * 1. public/assets/dokumen/dokumen_transaksi/[id]/   (struktur baru)
	 * 2. public/assets/dokumen_transaksi/[id]/                 (struktur lama)
	 * 3. uploads/transaksi/                                    (legacy)
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file.
	 *
	 * @return string|null
	 */
	private function resolve_transaksi_file($id, $file)
	{
		$id = (int) $id;
		$file = basename((string) $file);
		if ($id <= 0 || $file === '') {
			return null;
		}

		$candidates = array(
			FCPATH . 'assets/dokumen/dokumen_transaksi/' . $id . '/' . $file,
			FCPATH . 'assets/dokumen_transaksi/' . $id . '/' . $file,
			APPPATH . '../uploads/transaksi/' . $file,
		);

		foreach ($candidates as $p) {
			if (is_file($p)) {
				return $p;
			}
		}

		return null;
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

	// --------------------------------------------------------------------
	// Test Restore â€” create temp DB, restore dump, verify tables, drop DB
	// Returns true if restore succeeds with zero syntax errors.
	// --------------------------------------------------------------------
	private function test_restore($testDbName, $sqlFile, $cfg, $pgDump, $pass)
	{
		$host = $cfg['hostname'] ?: 'localhost';
		$port = $cfg['port'] ?: '5432';
		$user = $cfg['username'];
		$psql = str_replace('pg_dump', 'psql', $pgDump);
		if (!is_file($psql)) {
			$psqlDir = dirname($pgDump);
			$candidates = glob($psqlDir . DIRECTORY_SEPARATOR . 'psql*');
			$psql = $candidates ? $candidates[0] : $psql;
		}

		$env = array(
			'PATH' => getenv('PATH'),
			'PGPASSWORD' => $pass,
			'SystemRoot' => getenv('SystemRoot'),
			'COMSPEC' => getenv('COMSPEC'),
			'WINDIR' => getenv('WINDIR'),
		);

		$createResult = $this->run_pg_command($psql, $env, $host, $port, $user, 'postgres',
			'CREATE DATABASE ' . $this->pg_quote_ident($testDbName));
		if ($createResult['exit'] !== 0) {
			return false;
		}

		$restoreResult = $this->run_pg_file($psql, $env, $host, $port, $user, $testDbName, $sqlFile);
		if ($restoreResult['exit'] !== 0) {
			$this->drop_test_db($psql, $env, $host, $port, $user, $testDbName);
			return false;
		}

		$verifyResult = $this->run_pg_command($psql, $env, $host, $port, $user, $testDbName,
			"SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public'");
		$this->drop_test_db($psql, $env, $host, $port, $user, $testDbName);

		if ($verifyResult['exit'] !== 0 || $verifyResult['stdout'] === '') {
			return false;
		}

		$count = intval(trim($verifyResult['stdout']));
		return $count > 0;
	}

	private function run_pg_command($psql, $env, $host, $port, $user, $dbname, $sql)
	{
		$args = array('-h', $host, '-p', $port, '-U', $user, '-d', $dbname, '-t', '-A', '-c', $sql);
		$desc = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$cmdArray = array_merge(array($psql), $args);
		$proc = proc_open($cmdArray, $desc, $pipes, null, $env);
		if (!is_resource($proc)) {
			return array('exit' => -1, 'stdout' => '', 'stderr' => 'proc_open failed');
		}
		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		$exit = proc_close($proc);
		return array('exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr);
	}

	private function run_pg_file($psql, $env, $host, $port, $user, $dbname, $sqlFile)
	{
		$args = array('-h', $host, '-p', $port, '-U', $user, '-d', $dbname,
			'--single-transaction', '--set=ON_ERROR_STOP=1', '-f', $sqlFile);
		$desc = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$cmdArray = array_merge(array($psql), $args);
		$proc = proc_open($cmdArray, $desc, $pipes, null, $env);
		if (!is_resource($proc)) {
			return array('exit' => -1, 'stdout' => '', 'stderr' => 'proc_open failed');
		}
		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		$exit = proc_close($proc);
		return array('exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr);
	}

	private function drop_test_db($psql, $env, $host, $port, $user, $dbName)
	{
		$this->run_pg_command($psql, $env, $host, $port, $user, 'postgres',
			"SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '" . addslashes($dbName) . "' AND pid <> pg_backend_pid()");
		$result = $this->run_pg_command($psql, $env, $host, $port, $user, 'postgres',
			'DROP DATABASE IF EXISTS ' . $this->pg_quote_ident($dbName));
		return $result['exit'] === 0;
	}

	private function pg_quote_ident($ident)
	{
		return '"' . str_replace('"', '""', $ident) . '"';
	}

	private function find_pg_dump()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$out = array();
			@exec('where pg_dump 2>&1', $out, $ret);
			if ($ret === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
				return trim($out[0]);
			}
		} else {
			$out = array();
			@exec('which pg_dump 2>&1', $out, $ret);
			if ($ret === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
				return trim($out[0]);
			}
		}

		$commonPaths = array(
			'/usr/bin/pg_dump',
			'/usr/local/bin/pg_dump',
			'/opt/postgres/bin/pg_dump',
			'/usr/pgsql/bin/pg_dump',
		);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$programFiles = array(
				getenv('ProgramFiles') ?: 'C:\\Program Files',
				getenv('ProgramFiles(x86)') ?: 'C:\\Program Files (x86)',
			);
			foreach ($programFiles as $pf) {
				$commonPaths[] = $pf . '\\PostgreSQL\\*\\bin\\pg_dump.exe';
				$commonPaths[] = $pf . '\\postgresql\\*\\bin\\pg_dump.exe';
			}
			$laragonBin = getenv('LARAGON_DIR') ?: 'C:\\laragon\\bin';
			$commonPaths[] = $laragonBin . '\\postgresql\\*\\bin\\pg_dump.exe';
			$commonPaths[] = $laragonBin . '\\postgresql*\\bin\\pg_dump.exe';
		}

		foreach ($commonPaths as $pat) {
			if (strpos($pat, '*') !== false) {
				foreach (glob($pat) as $g) {
					if (is_file($g)) return $g;
				}
			} elseif (is_file($pat) && is_executable($pat)) {
				return $pat;
			}
		}

		return 'pg_dump';
	}

	private function build_filter_label($tgl_mulai, $tgl_akhir)
	{
		if ($tgl_mulai !== '' && $tgl_akhir !== '') {
			return date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
		}
		if ($tgl_mulai !== '') {
			return '>= ' . date('d-m-Y', strtotime($tgl_mulai));
		}
		if ($tgl_akhir !== '') {
			return '<= ' . date('d-m-Y', strtotime($tgl_akhir));
		}
		return 'Semua';
	}

	/**
	 * Hitung jumlah file (rekursif) di dalam sebuah folder.
	 *
	 * @param string $dir
	 * @return int
	 */
	private function count_files_recursive($dir)
	{
		$count = 0;
		$items = @scandir($dir);
		if ($items === false) {
			return 0;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$full = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($full)) {
				$count += $this->count_files_recursive($full);
			} elseif (is_file($full)) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Tambahkan seluruh isi folder (rekursif) ke dalam ZIP.
	 * Path di dalam ZIP dibatasi di dalam root folder (prevent traversal).
	 *
	 * @param ZipArchive $zip
	 * @param string     $rootPath  Path folder absolut di server.
	 * @param string     $prefix    Prefix entry ZIP (mis. 'report', 'dokumen_transaksi').
	 * @param string     $baseReal  realpath($rootPath).
	 * @param array      &$added    Daftar entry yang ditambahkan.
	 */
	private function add_dir_recursive($zip, $rootPath, $prefix, $baseReal, &$added)
	{
		$items = @scandir($rootPath);
		if ($items === false) {
			return;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$full = $rootPath . DIRECTORY_SEPARATOR . $item;
			if (is_dir($full)) {
				$this->add_dir_recursive($zip, $full, $prefix . '/' . $item, $baseReal, $added);
			} elseif (is_file($full)) {
				$realFull = realpath($full);
				if ($realFull === false || $baseReal === false || strpos($realFull, $baseReal) !== 0) {
					continue;
				}
				$entry = $prefix . '/' . $item;
				$zip->addFile($realFull, $entry);
				$added[] = $entry;
			}
		}
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
