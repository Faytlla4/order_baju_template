<?php defined('BASEPATH') || exit('No direct script access allowed');

class Report_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Ambil data laporan transaksi (JOIN order_baju + master).
	 *
	 * @param string $periode   'all' | 'today' | 'month' | 'custom'
	 * @param string $tgl_mulai Tanggal mulai (YYYY-MM-DD) utk custom.
	 * @param string $tgl_akhir Tanggal akhir (YYYY-MM-DD) utk custom.
	 * @param string $status    'Diproses' | 'Diambil' | 'Selesai' | '' (Semua)
	 *
	 * @return array
	 */
	public function get_report($periode = 'all', $tgl_mulai = '', $tgl_akhir = '', $status = '')
	{
		$periode = in_array($periode, array('all', 'today', 'month', 'custom'), true) ? $periode : 'all';

		$this->db->select('
			transaksi.id,
			order_baju.kode_order,
			order_baju.nama_customer,
			order_baju.produk,
			master_jenis_baju.nama_jenis AS jenis_nama,
			master_ukuran.nama_ukuran AS ukuran_nama,
			master_warna.nama_warna AS warna_nama,
			transaksi.jumlah,
			transaksi.harga,
			transaksi.total_harga,
			transaksi.status_transaksi,
			to_char(transaksi.created_on, \'DD-MM-YYYY HH24:MI\') AS tanggal
		')
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'inner')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left');

		if ($periode === 'today') {
			$this->db->where(
				"transaksi.created_on::date = (CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta')::date",
				null, false
			);
		} elseif ($periode === 'month') {
			$this->db->where(
				"transaksi.created_on::date >= (date_trunc('month', (CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta'))::date)",
				null, false
			);
			$this->db->where(
				"transaksi.created_on::date <= (date_trunc('month', (CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta'))::date + interval '1 month - 1 day')",
				null, false
			);
		} elseif ($periode === 'custom') {
			if ($tgl_mulai !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_mulai)) {
				$this->db->where("transaksi.created_on::date >= '{$tgl_mulai}'::date", null, false);
			}
			if ($tgl_akhir !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_akhir)) {
				$this->db->where("transaksi.created_on::date <= '{$tgl_akhir}'::date", null, false);
			}
		}

		if ($status !== '' && in_array($status, array('Diproses', 'Diambil', 'Selesai'), true)) {
			$this->db->where('transaksi.status_transaksi', $status);
		}

		$this->db->order_by('transaksi.created_on', 'desc')
			->order_by('transaksi.id', 'desc');

		$rows = $this->db->get()->result();

		foreach ($rows as &$row) {
			if (empty($row->jenis_nama)) {
				$row->jenis_nama = '-';
			}
			if (empty($row->ukuran_nama)) {
				$row->ukuran_nama = '-';
			}
			if (empty($row->warna_nama)) {
				$row->warna_nama = '-';
			}
		}

		return $rows;
	}

	/**
	 * Total nilai (sum total_harga) dari baris laporan.
	 *
	 * @param array $rows Hasil get_report().
	 *
	 * @return float
	 */
	public function grand_total($rows)
	{
		$total = 0;
		foreach ($rows as $r) {
			$total += (float) $r->total_harga;
		}
		return $total;
	}

	/**
	 * Label periode utk judul halaman/PDF.
	 *
	 * @param string $periode
	 * @param string $tgl_mulai
	 * @param string $tgl_akhir
	 *
	 * @return string
	 */
	public function periode_label($periode = 'all', $tgl_mulai = '', $tgl_akhir = '')
	{
		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$d = function ($t) {
			if ($t === '') {
				return '';
			}
			$dt = new DateTime($t, new DateTimeZone('Asia/Jakarta'));
			return $dt->format('d-m-Y');
		};

		switch ($periode) {
			case 'today':
				return $wib->format('d-m-Y');
			case 'month':
				return $wib->format('m-Y');
			case 'custom':
				if ($d($tgl_mulai) !== '' && $d($tgl_akhir) !== '') {
					return $d($tgl_mulai) . ' s/d ' . $d($tgl_akhir);
				}
				if ($d($tgl_mulai) !== '') {
					return 'Mulai ' . $d($tgl_mulai);
				}
				if ($d($tgl_akhir) !== '') {
					return 'Sampai ' . $d($tgl_akhir);
				}
				return '';
			case 'all':
			default:
				return 'Semua';
		}
	}

	/**
	 * Simpan metadata/histori laporan ke tabel report.
	 *
	 * Schema final: tipe_report, nama_file, path_file.
	 *
	 * @param array $data Harus berisi: tipe_report, nama_file, path_file.
	 *
	 * @return int|false ID report yang disimpan, atau false bila gagal.
	 */
	public function save_history($data)
	{
		if (!is_array($data) || empty($data)) {
			return false;
		}

		$allowed = array(
			'tipe_report',
			'periode',
			'tgl_mulai',
			'tgl_akhir',
			'jumlah_transaksi',
			'total_nilai',
			'nama_file',
			'path_file',
		);

		$insert = array();
		foreach ($allowed as $key) {
			if (array_key_exists($key, $data)) {
				$insert[$key] = $data[$key];
			}
		}

		if (empty($insert)) {
			return false;
		}

		$insert['created_on'] = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s.u');

		if (!$this->db->insert('report', $insert)) {
			return false;
		}

		return (int) $this->db->insert_id();
	}

	/**
	 * Ambil daftar histori laporan (terbaru dulu).
	 *
	 * @param int    $limit
	 * @param string $h_periode   'all'|'today'|'month'|'custom'
	 * @param string $h_tgl_mulai YYYY-MM-DD (untuk custom)
	 * @param string $h_tgl_akhir YYYY-MM-DD (untuk custom)
	 * @param string $tipe_report 'pdf'|'excel'|'' (kosong = semua)
	 *
	 * @return array
	 */
	public function get_history_list($limit = 20, $h_periode = 'all', $h_tgl_mulai = '', $h_tgl_akhir = '', $tipe_report = '')
	{
		$this->db->select('
			id,
			tipe_report,
			periode,
			tgl_mulai,
			tgl_akhir,
			jumlah_transaksi,
			total_nilai,
			nama_file,
			path_file,
			to_char(created_on, \'DD-MM-YYYY HH24:MI:SS\') AS dibuat_pada
		')
			->from('report');

		if ($tipe_report !== '' && in_array($tipe_report, array('pdf', 'excel'), true)) {
			$this->db->where('tipe_report', $tipe_report);
		}

		if ($h_periode === 'today') {
			$this->db->where(
				"created_on::date = (CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta')::date",
				null, false
			);
		} elseif ($h_periode === 'month') {
			$this->db->where(
				"created_on::date >= (date_trunc('month', CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta'))::date",
				null, false
			);
			$this->db->where(
				"created_on::date <= (date_trunc('month', CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta') + interval '1 month - 1 day')::date",
				null, false
			);
		} elseif ($h_periode === 'custom') {
			if ($h_tgl_mulai !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $h_tgl_mulai)) {
				$this->db->where("created_on::date >= '{$h_tgl_mulai}'::date", null, false);
			}
			if ($h_tgl_akhir !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $h_tgl_akhir)) {
				$this->db->where("created_on::date <= '{$h_tgl_akhir}'::date", null, false);
			}
		}

		$this->db->order_by('id', 'desc')
			->limit((int) $limit);

		return $this->db->get()->result();
	}

	/**
	 * Ambil satu histori laporan berdasarkan id.
	 *
	 * @param int $id
	 *
	 * @return object|null
	 */
	public function find_history($id)
	{
		$id = (int) $id;
		if ($id <= 0) {
			return null;
		}

		$this->db->select('
			id,
			tipe_report,
			periode,
			tgl_mulai,
			tgl_akhir,
			jumlah_transaksi,
			total_nilai,
			nama_file,
			path_file
		')
			->from('report')
			->where('id', $id);

		return $this->db->get()->row();
	}

	/**
	 * Hapus histori laporan berdasarkan id.
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete_history($id)
	{
		$id = (int) $id;
		if ($id <= 0) {
			return false;
		}

		$row = $this->find_history($id);
		if (!$row) {
			return false;
		}

		$this->db->where('id', $id)->delete('report');

		return $this->db->affected_rows() > 0;
	}
}
