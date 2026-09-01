<?php defined('BASEPATH') || exit('No direct script access allowed');

class Transaksi_model extends DT_Model
{
	public $table_name = 'transaksi';
	protected $key = 'id';
	protected $date_format = 'datetime';

	protected $log_user = false;
	protected $set_created = true;
	protected $set_modified = false;
	protected $soft_deletes = false;

	protected $created_field = 'created_on';
	protected $created_by_field = 'created_by';
	protected $modified_field = 'modified_on';
	protected $modified_by_field = 'modified_by';
	protected $deleted_field = 'deleted';
	protected $deleted_by_field = 'deleted_by';

	protected $before_insert = array();
	protected $after_insert = array();
	protected $before_update = array();
	protected $after_update = array();
	protected $before_find = array();
	protected $after_find = array();
	protected $before_delete = array();
	protected $after_delete = array();

	protected $return_insert_id = true;

	protected $return_type = 'object';

	protected $protected_attributes = array('id');

protected $validation_rules = array(
		array(
			'field' => 'order_baju_id',
			'label' => 'Order',
			'rules' => 'required|integer',
		),
		array(
			'field' => 'jumlah',
			'label' => 'Jumlah',
			'rules' => 'required|integer|greater_than[0]',
		),
		array(
			'field' => 'harga',
			'label' => 'Harga',
			'rules' => 'required|numeric|greater_than_equal_to[0]',
		),
array(
			'field' => 'status_transaksi',
			'label' => 'Status Transaksi',
			'rules' => 'required|max_length[30]',
		),
	);
	protected $insert_validation_rules = array();
	protected $skip_validation = false;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Cari transaksi untuk satu order (data non-terhapus).
	 *
	 * @param int $order_baju_id
	 *
	 * @return object|null
	 */
	public function find_by_order($order_baju_id)
	{
		return $this->db
			->where('order_baju_id', (int) $order_baju_id)
			->limit(1)
			->get($this->table_name)
			->row();
	}

	/**
	 * Cek apakah order sudah memiliki transaksi.
	 *
	 * @param int $order_baju_id
	 *
	 * @return bool
	 */
	public function has_transaksi($order_baju_id)
	{
		$row = $this->find_by_order($order_baju_id);
		return $row !== null;
	}

/**
	 * Ambil detail satu transaksi dengan JOIN order_baju + master
	 * (reuse pola query get_list_data).
	 *
	 * Hanya baca (SELECT), tidak mengubah data apa pun.
	 *
	 * @param int $id ID transaksi.
	 *
	 * @return object|null
	 */
	public function get_detail($id)
	{
		$id = (int) $id;
		if ($id <= 0) {
			return null;
		}

		$row = $this->db
			->select('
				transaksi.id,
				transaksi.order_baju_id,
				transaksi.jumlah,
				transaksi.harga,
				transaksi.total_harga,
				transaksi.status_transaksi,
				transaksi.dokumen,
				to_char(transaksi.created_on, \'DD-MM-YYYY HH24:MI\') AS created_on,
				order_baju.kode_order,
				order_baju.nama_customer,
				order_baju.produk,
				order_baju.tanggal_order,
				master_jenis_baju.nama_jenis AS jenis_nama,
				master_ukuran.nama_ukuran AS ukuran_nama,
				master_warna.nama_warna AS warna_nama
			')
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'inner')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left')
			->where('transaksi.id', $id)
			->get()
			->row();

		if (!$row) {
			return null;
		}

		if (empty($row->jenis_nama)) {
			$row->jenis_nama = '-';
		}
		if (empty($row->ukuran_nama)) {
			$row->ukuran_nama = '-';
		}
		if (empty($row->warna_nama)) {
			$row->warna_nama = '-';
		}

		return $row;
	}

	/**
	 * Update parsial data transaksi (jumlah/harga/total/status) tanpa
	 * menjalankan validasi penuh model (kolom lain seperti order_baju_id
	 * dan tanggal_transaksi tidak ikut dikirim).
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return bool
	 */
	public function update_partial($id, $data)
	{
		$this->skip_validation = true;
		$result = $this->update($id, $data);
		$this->skip_validation = false;

		return $result;
	}

	/**
* Data untuk DataTable daftar transaksi.
	 *
	 * Sumber data utama tabel transaksi, JOIN ke order_baju (detail order)
	 * dan master (nama jenis/ukuran/warna). Semua status transaksi
	 * (Diproses/Selesai/Diambil) tetap tampil pada daftar.
	 *
	 * @param array $request Data POST dari DataTable (draw/length/start/search/sort).
	 *
	 * @return array Protocol DataTable (draw/recordsTotal/data/recordsFiltered).
	 */
	public function get_list_data($request = null)
	{
		if ($request === null) {
			$request = $this->input->post();
		}
		if (!is_array($request)) {
			$request = array();
		}

		$output = array();
		$output['draw'] = (int) (isset($request['draw']) ? $request['draw'] : 0);

		// Kolom yang dipakai daftar (allowlist untuk menghindari injeksi nama kolom).
		$sort_map = array(
			'id'               => 'transaksi.id',
			'kode_order'       => 'order_baju.kode_order',
			'nama_customer'    => 'order_baju.nama_customer',
			'produk'           => 'order_baju.produk',
			'jenis_nama'       => 'master_jenis_baju.nama_jenis',
			'ukuran_nama'      => 'master_ukuran.nama_ukuran',
			'warna_nama'       => 'master_warna.nama_warna',
			'jumlah'           => 'transaksi.jumlah',
			'harga'            => 'transaksi.harga',
			'total_harga'      => 'transaksi.total_harga',
			'status_transaksi' => 'transaksi.status_transaksi',
			'tanggal_order'    => 'order_baju.tanggal_order',
			'created_on'       => 'transaksi.created_on',
		);

		// Kondisi dasar: semua status transaksi (termasuk Diambil) ditampilkan.
		$this->db
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'inner')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left');

		// Filter status opsional (dari bfDataTable `params.status`).
		$status = isset($request['params']['status']) ? trim((string) $request['params']['status']) : '';
		if ($status !== '' && in_array($status, array('Diproses', 'Diambil', 'Selesai'), true)) {
			$this->db->where('transaksi.status_transaksi', $status);
		}

		// recordsTotal = total after base filter, before search.
		$output['recordsTotal'] = $this->db->count_all_results('', false);

		// Pencarian (satu kolom, sesuai bfDataTable).
		$search_col = isset($request['search']['column']) ? $request['search']['column'] : '';
		$search_val = isset($request['search']['value']) ? $request['search']['value'] : '';
		if ($search_col !== '' && isset($sort_map[$search_col]) && strlen(trim($search_val)) > 0) {
			$this->db->where('LOWER(' . $sort_map[$search_col] . '::TEXT) LIKE', '%' . strtolower($search_val) . '%');
		}

		// recordsFiltered = total after search filter, before pagination.
		$output['recordsFiltered'] = $this->db->count_all_results('', false);

		// Sort & limit setelah conditions sama.
		$sort_key = isset($request['sort']) && is_array($request['sort']) ? key($request['sort']) : 'id';
		$sort_dir = isset($request['sort']) && is_array($request['sort']) ? strtolower(current($request['sort'])) : 'desc';
		$sort_col = isset($sort_map[$sort_key]) ? $sort_map[$sort_key] : 'transaksi.id';
		$sort_dir = in_array($sort_dir, array('asc', 'desc'), true) ? $sort_dir : 'desc';

		$this->db->order_by($sort_col, $sort_dir);

		$length = isset($request['length']) ? (int) $request['length'] : 10;
		$start  = isset($request['start']) ? (int) $request['start'] : 0;
		if ($length > 0) {
			$this->db->limit($length, $start);
		}

		$rows = $this->db
			->select('
				transaksi.id,
				transaksi.order_baju_id,
				transaksi.jumlah,
				transaksi.harga,
				transaksi.total_harga,
				transaksi.status_transaksi,
				transaksi.dokumen,
				to_char(transaksi.created_on, \'DD-MM-YYYY HH24:MI\') AS created_on,
				order_baju.kode_order,
				order_baju.nama_customer,
				order_baju.produk,
				order_baju.tanggal_order,
				master_jenis_baju.nama_jenis AS jenis_nama,
				master_ukuran.nama_ukuran AS ukuran_nama,
				master_warna.nama_warna AS warna_nama
			')
			->get()
			->result();

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

			$dokumen_files = array();
			if (isset($row->dokumen) && $row->dokumen !== '') {
				$decoded = json_decode($row->dokumen, true);
				if (is_array($decoded)) {
					foreach ($decoded as $file) {
						$file = basename((string) $file);
						if ($file !== '') {
							$dokumen_files[] = $file;
						}
					}
				}
			}

			$row->dokumen_files = $dokumen_files;
			$row->dokumen_count = count($dokumen_files);
		}

		$output['data'] = $rows;

		return $output;
	}
}

