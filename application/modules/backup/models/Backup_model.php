<?php defined('BASEPATH') || exit('No direct script access allowed');

class Backup_model extends CI_Model
{
	private $doc_history_table = 'backup_document_history';
	private $db_history_table = 'backup_database_history';
	private $upload_dir;
	private $document_root;

	public function __construct()
	{
		parent::__construct();
		$this->upload_dir = APPPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
		$this->document_root = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'dokumen' . DIRECTORY_SEPARATOR;
		if (!is_dir($this->upload_dir)) {
			mkdir($this->upload_dir, 0755, true);
		}
	}

	// --- Riwayat Cetak (Report History) ---

	/**
	 * Ambil riwayat cetak dari tabel report, difilter tanggal.
	 *
	 * @param string $tgl_mulai YYYY-MM-DD
	 * @param string $tgl_akhir YYYY-MM-DD
	 * @return array
	 */
	public function get_riwayat_cetak($tgl_mulai = '', $tgl_akhir = '')
	{
		$sql = "SELECT id, tipe_report, nama_file, path_file, jumlah_transaksi,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str
			FROM report";

		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}

		if (!empty($conditions)) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}

		$sql .= ' ORDER BY created_on DESC';

		if (!empty($params)) {
			return $this->db->query($sql, $params)->result();
		}
		return $this->db->query($sql)->result();
	}

	/**
	 * Ambil beberapa report berdasarkan ID.
	 *
	 * @param array $ids
	 * @return array
	 */
	public function get_reports_by_ids($ids)
	{
		if (empty($ids)) {
			return array();
		}
		$this->db->where_in('id', $ids);
		return $this->db->get('report')->result();
	}

	/**
	 * Ambil daftar dokumen yang diupload user pada transaksi
	 * (per file, dari kolom dokumen JSON), difilter tanggal transaksi.
	 *
	 * @param string $tgl_mulai YYYY-MM-DD
	 * @param string $tgl_akhir YYYY-MM-DD
	 * @return array
	 */
	public function get_dokumen_transaksi($tgl_mulai = '', $tgl_akhir = '')
	{
		$sql = "SELECT id, dokumen,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str
			FROM transaksi
			WHERE dokumen IS NOT NULL
			  AND dokumen <> ''
			  AND dokumen <> '[]'
			  AND dokumen <> '[[]]'";

		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}

		if (!empty($conditions)) {
			$sql .= ' AND ' . implode(' AND ', $conditions);
		}

		$sql .= ' ORDER BY created_on DESC, id DESC';

		$rows = empty($params)
			? $this->db->query($sql)->result()
			: $this->db->query($sql, $params)->result();

		$out = array();
		foreach ($rows as $r) {
			$files = json_decode($r->dokumen, true);
			if (!is_array($files)) {
				continue;
			}
			foreach ($files as $f) {
				$f = basename(trim((string) $f));
				if ($f === '' || $f === 'null') {
					continue;
				}
				$out[] = (object) array(
					'id'             => (int) $r->id,
					'source'         => 'transaksi',
					'tipe_report'    => 'transaksi',
					'nama_file'      => $f,
					'path_file'      => 'dokumen/dokumen_transaksi/' . (int) $r->id . '/' . $f,
					'created_on_str' => $r->created_on_str,
					'jumlah_transaksi' => 1,
				);
			}
		}

		return $out;
	}

	/**
	 * Validasi & ambil dokumen transaksi terpilih dari input "id:nama_file".
	 * Hanya mengembalikan file yang BENAR-BENAR terdaftar di kolom dokumen
	 * transaksi tersebut (mencegah path/file sewenang-wenang).
	 *
	 * @param array $pairs Contoh: array("60:20260901_abc.pdf", ...).
	 *
	 * @return array of objects (id, nama_file)
	 */
	public function get_transaksi_docs_selected($pairs)
	{
		$out = array();
		if (empty($pairs)) {
			return $out;
		}

		$ids = array();
		foreach ($pairs as $p) {
			$p = (string) $p;
			if ($p === '') {
				continue;
			}
			$parts = explode(':', $p, 2);
			if (count($parts) === 2) {
				$id = (int) $parts[0];
				if ($id > 0) {
					$ids[$id] = true;
				}
			}
		}

		if (empty($ids)) {
			return $out;
		}

		$id_list = array_keys($ids);
		$this->db->select('id, dokumen');
		$this->db->where_in('id', $id_list);
		$rows = $this->db->get('transaksi')->result();

		$map = array();
		foreach ($rows as $row) {
			$files = json_decode($row->dokumen, true);
			if (!is_array($files)) {
				continue;
			}
			foreach ($files as $f) {
				$f = basename(trim((string) $f));
				if ($f === '' || $f === 'null') {
					continue;
				}
				$map[(int) $row->id . ':' . $f] = (int) $row->id;
			}
		}

		foreach ($pairs as $p) {
			$p = (string) $p;
			if ($p === '') {
				continue;
			}
			$parts = explode(':', $p, 2);
			if (count($parts) !== 2) {
				continue;
			}
			$id = (int) $parts[0];
			$file = basename(trim($parts[1]));
			if ($id <= 0 || $file === '') {
				continue;
			}
			if (!isset($map[$id . ':' . $file])) {
				continue;
			}
			$out[] = (object) array(
				'id' => $id,
				'nama_file' => $file,
			);
		}

		return $out;
	}

	/**
	 * Ambil daftar transaksi yang memiliki dokumen, dikelompokkan per ID.
	 *
	 * SATU ID = SATU BARIS. Folder fisik menjadi sumber utama daftar file.
	 * JSON tetap menjadi fallback untuk transaksi lama yang belum mempunyai
	 * folder fisik pada struktur dokumen_transaksi/[id].
	 *
	 * @param string $tgl_mulai YYYY-MM-DD (opsional)
	 * @param string $tgl_akhir YYYY-MM-DD (opsional)
	 * @return array of objects (id, created_on_str, jumlah_dokumen, files[])
	 */
	public function get_dokumen_per_id($tgl_mulai = '', $tgl_akhir = '')
	{
		$sql = "SELECT id,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str,
				dokumen
			FROM transaksi
			WHERE 1 = 1";

		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}

		if (!empty($conditions)) {
			$sql .= ' AND ' . implode(' AND ', $conditions);
		}

		$sql .= ' ORDER BY created_on DESC, id DESC';

		$rows = empty($params)
			? $this->db->query($sql)->result()
			: $this->db->query($sql, $params)->result();

		$out = array();
		foreach ($rows as $r) {
			$clean = $this->clean_json_files($r->dokumen);
			if (empty($clean)) {
				$clean = $this->get_physical_transaction_files((int) $r->id);
			}
			if (empty($clean)) {
				continue;
			}
			$out[] = (object) array(
				'id'             => (int) $r->id,
				'created_on_str' => $r->created_on_str,
				'jumlah_dokumen' => count($clean),
				'files'          => $clean,
			);
		}

		return $out;
	}

	/**
	 * Ambil transaksi yang dokumennya akan dibackup berdasarkan ID terpilih.
	 * Mengembalikan peta id => daftar file (nama file) yang benar terdaftar.
	 *
	 * @param array $ids
	 * @return array id => array of file names
	 */
	public function get_transaksi_files_by_ids($ids)
	{
		$out = array();
		if (empty($ids)) {
			return $out;
		}

		$id_list = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$id_list[$id] = true;
			}
		}
		if (empty($id_list)) {
			return $out;
		}

		$this->db->select('id, dokumen');
		$this->db->where_in('id', array_keys($id_list));
		$rows = $this->db->get('transaksi')->result();

		foreach ($rows as $row) {
			$clean = $this->clean_json_files($row->dokumen);
			if (empty($clean)) {
				$clean = $this->get_physical_transaction_files((int) $row->id);
			}
			if (!empty($clean)) {
				$out[(int) $row->id] = $clean;
			}
		}

		return $out;
	}

	/**
	 * Ambil seluruh file terbaru dari folder transaksi fisik, rekursif.
	 * Nilai yang dikembalikan adalah path relatif terhadap folder ID.
	 */
	public function get_physical_transaction_files($id)
	{
		$dir = $this->document_root . 'dokumen_transaksi' . DIRECTORY_SEPARATOR . (int) $id;
		if ((int) $id <= 0 || !is_dir($dir)) {
			return array();
		}

		$files = array();
		$this->collect_files_recursive($dir, '', $files);
		sort($files, SORT_NATURAL | SORT_FLAG_CASE);
		return $files;
	}

	private function collect_files_recursive($dir, $relative, &$files)
	{
		$items = @scandir($dir);
		if ($items === false) {
			return;
		}
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$full = $dir . DIRECTORY_SEPARATOR . $item;
			$child = ($relative === '') ? $item : $relative . '/' . $item;
			if (is_dir($full)) {
				$this->collect_files_recursive($full, $child, $files);
			} elseif (is_file($full)) {
				$files[] = $child;
			}
		}
	}

	public function clean_json_files($json)
	{
		$files = json_decode((string) $json, true);
		$clean = array();
		if (!is_array($files)) {
			return $clean;
		}
		foreach ($files as $file) {
			$file = basename(trim((string) $file));
			if ($file !== '' && $file !== 'null') {
				$clean[] = $file;
			}
		}
		return array_values(array_unique($clean));
	}

	/**
	 * Scan folder dokumen utama langsung dari file system.
	 *
	 * Semua folder yang berada satu level di bawah public/assets/dokumen/
	 * akan ditampilkan, termasuk folder yang dibuat manual melalui File Explorer.
	 *
	 * @return array keyed by nama folder
	 */
	public function get_document_folders()
	{
		$root = rtrim($this->document_root, '/\\') . DIRECTORY_SEPARATOR;
		if (!is_dir($root)) {
			return array();
		}

		$items = @scandir($root);
		if ($items === false) {
			return array();
		}

		$folders = array();
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}

			$path = $root . $item;
			if (!is_dir($path)) {
				continue;
			}

			$folders[$item] = array(
				'key'   => $item,
				'label' => $item,
				'path'  => $path,
				'icon'  => 'fas fa-folder',
				'exists' => true,
				'count' => $this->count_files_recursive($path),
			);
		}

		if (!empty($folders)) {
			uksort($folders, 'strnatcasecmp');
		}

		return $folders;
	}

	/**
	 * Ambil isi folder berdasarkan key folder yang sudah discan server.
	 * Path request tidak pernah dipakai langsung sebagai path filesystem.
	 *
	 * @param string $key Nama folder satu level di bawah document root.
	 * @return array|null NULL bila folder tidak valid/tidak ditemukan.
	 */
	public function get_document_folder_files($key)
	{
		$key = trim((string) $key);
		$folders = $this->get_document_folders();
		if ($key === '' || !isset($folders[$key])) {
			return null;
		}

		$files = array();
		$this->collect_files_recursive($folders[$key]['path'], '', $files);
		sort($files, SORT_NATURAL | SORT_FLAG_CASE);
		return $files;
	}

	// --- Database Config ---

	public function get_database_config()
	{
		$this->load->database();
		$hostname = $this->db->hostname;
		$username = $this->db->username;
		$password = $this->db->password;
		$database = $this->db->database;
		$host = $hostname;
		$port = '5432';
		if (strpos($hostname, ':') !== false) {
			list($h, $p) = explode(':', $hostname, 2);
			$host = trim($h);
			$port = trim($p) !== '' ? trim($p) : '5432';
		}
		if (empty($host) || empty($database)) {
			$db = array();
			@include APPPATH . 'config/database.php';
			if (!empty($db['default'])) {
				$cfg = $db['default'];
				$host = $cfg['hostname'] ?: $host;
				$username = $cfg['username'] ?: $username;
				$password = $cfg['password'] ?: $password;
				$database = $cfg['database'] ?: $database;
				if (strpos($host, ':') !== false) {
					list($h, $p) = explode(':', $host, 2);
					$host = $h;
					$port = $p ?: $port;
				}
			}
		}
		return array(
			'hostname' => $host,
			'port'     => $port,
			'username' => $username,
			'password' => $password,
			'database' => $database,
		);
	}

	// --- Document History ---

	public function save_document_history($file_name, $file_path, $file_size, $jumlah_dokumen, $filter_used)
	{
		return $this->db->insert($this->doc_history_table, array(
			'file_name'       => $file_name,
			'file_path'       => $file_path,
			'file_size'       => (int) $file_size,
			'jumlah_dokumen'  => (int) $jumlah_dokumen,
			'filter_used'     => $filter_used,
			'created_on'      => date('Y-m-d H:i:s'),
		));
	}

	public function get_document_history()
	{
		return $this->db->order_by('id', 'desc')->get($this->doc_history_table)->result();
	}

	public function get_document_history_by_id($id)
	{
		return $this->db->where('id', (int) $id)->get($this->doc_history_table)->row();
	}

	// --- Database History ---

	public function save_database_history($file_name, $file_path, $file_size, $status = 'Berhasil')
	{
		return $this->db->insert($this->db_history_table, array(
			'file_name'  => $file_name,
			'file_path'  => $file_path,
			'file_size'  => (int) $file_size,
			'status'     => $status,
			'created_on' => date('Y-m-d H:i:s'),
		));
	}

	public function get_database_history()
	{
		return $this->db->order_by('id', 'desc')->get($this->db_history_table)->result();
	}

	public function get_database_history_by_id($id)
	{
		return $this->db->where('id', (int) $id)->get($this->db_history_table)->row();
	}

	// --- ZIP Storage ---

	public function store_zip($source_path, $file_name)
	{
		$dest = $this->upload_dir . $file_name;
		if (copy($source_path, $dest)) {
			return $dest;
		}
		return false;
	}

	public function get_zip_path($file_name)
	{
		$path = $this->upload_dir . $file_name;
		return (is_file($path) && filesize($path) > 0) ? $path : false;
	}

	/**
	 * Hitung jumlah file dalam folder secara rekursif.
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
}
