<?php defined('BASEPATH') || exit('No direct script access allowed');

class Backup_model extends CI_Model
{
	private $doc_history_table = 'backup_document_history';
	private $db_history_table = 'backup_database_history';
	private $upload_dir;

	public function __construct()
	{
		parent::__construct();
		$this->upload_dir = APPPATH . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
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
	 * SATU ID = SATU BARIS. Jumlah dokumen dihitung dari kolom `dokumen`
	 * (JSON array nama file) — diambil dari struktur & data sebenarnya.
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
			$clean = array();
			foreach ($files as $f) {
				$f = basename(trim((string) $f));
				if ($f === '' || $f === 'null') {
					continue;
				}
				$clean[] = $f;
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
			$files = json_decode($row->dokumen, true);
			$clean = array();
			if (is_array($files)) {
				foreach ($files as $f) {
					$f = basename(trim((string) $f));
					if ($f === '' || $f === 'null') {
						continue;
					}
					$clean[] = $f;
				}
			}
			if (!empty($clean)) {
				$out[(int) $row->id] = $clean;
			}
		}

		return $out;
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
}
