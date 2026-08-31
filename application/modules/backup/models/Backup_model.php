<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Backup_model — thin wrapper, delegasi ke Report_model existing.
 * Tidak duplikasi query transaksi; reuse get_report() yang sudah ada.
 */
class Backup_model extends CI_Model
{
	/**
	 * Ambil data transaksi untuk backup dokumen via logic Report existing.
	 * Filter tgl_mulai/tgl_akhir/status diterapkan ke sumber transaksi sebelum generate.
	 *
	 * @param string $tgl_mulai YYYY-MM-DD atau ''
	 * @param string $tgl_akhir  YYYY-MM-DD atau ''
	 * @param string $status     Diproses|Diambil|Selesai atau ''
	 * @return array
	 */
	public function get_transaksi_for_backup($tgl_mulai = '', $tgl_akhir = '', $status = '')
	{
		$this->load->model('report_pdf/report_model');
		// Backup Dokumen: periode=custom bila ada tanggal, else all.
		// Reuse exact query Report (JOIN transaksi+order_baju+master).
		if ($tgl_mulai !== '' || $tgl_akhir !== '') {
			return $this->report_model->get_report('custom', $tgl_mulai, $tgl_akhir, $status);
		}
		return $this->report_model->get_report('all', '', '', $status);
	}

	/**
	 * Ambil konfigurasi database aktif (untuk pg_dump).
	 *
	 * @return array {hostname, username, password, database, port}
	 */
	public function get_database_config()
	{
		$this->load->database();
		$hostname = $this->db->hostname;
		$username = $this->db->username;
		$password = $this->db->password;
		$database = $this->db->database;

		// Parse host:port jika ada
		$host = $hostname;
		$port = '5432';
		if (strpos($hostname, ':') !== false) {
			list($h, $p) = explode(':', $hostname, 2);
			$host = trim($h);
			$port = trim($p) !== '' ? trim($p) : '5432';
		}
		// Fallback baca file jika $this->db kosong (misal belum load)
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
}
