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

	// --- Transaksi for Riwayat Cetak ---

	public function get_transaksi_for_backup($tgl_mulai = '', $tgl_akhir = '', $status = '')
	{
		$this->load->model('report_pdf/report_model');
		if ($tgl_mulai !== '' || $tgl_akhir !== '') {
			return $this->report_model->get_report('custom', $tgl_mulai, $tgl_akhir, $status);
		}
		return $this->report_model->get_report('all', '', '', $status);
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
