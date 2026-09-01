<?php defined('BASEPATH') || exit('No direct script access allowed');

class Order_baju_model extends DT_Model
{
	public $table_name = 'order_baju';
	protected $key = 'id';
	protected $date_format = 'datetime';

	// Filter status (order_baju.status_order) untuk DataTable Content.
	protected $filter_status_field = 'order_baju.status_order';
	protected $filter_status_allowed = array('Diproses', 'Diambil', 'Selesai');

	// HARD DELETE + tanpa audit Bonfire (kolom audit sudah dihapus dari tabel).
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
			'field' => 'kode_order',
			'label' => 'lang:order_baju_field_kode_order',
			'rules' => 'max_length[50]',
		),
		array(
			'field' => 'nama_customer',
			'label' => 'lang:order_baju_field_nama_customer',
			'rules' => 'required|max_length[100]',
		),
		array(
			'field' => 'produk',
			'label' => 'lang:order_baju_field_produk',
			'rules' => 'required|max_length[100]',
		),
		array(
			'field' => 'ukuran_id',
			'label' => 'lang:order_baju_field_ukuran',
			'rules' => 'integer',
		),
		array(
			'field' => 'warna_id',
			'label' => 'lang:order_baju_field_warna',
			'rules' => 'integer',
		),
		array(
			'field' => 'jenis_baju_id',
			'label' => 'lang:order_baju_field_jenis_baju',
			'rules' => 'required|integer',
		),
		array(
			'field' => 'jumlah',
			'label' => 'lang:order_baju_field_jumlah',
			'rules' => 'integer|greater_than[0]',
		),
		array(
			'field' => 'harga',
			'label' => 'lang:order_baju_field_harga',
			'rules' => 'numeric',
		),
		array(
			'field' => 'status_order',
			'label' => 'lang:order_baju_field_status_order',
			'rules' => 'required|max_length[30]',
		),
		array(
			'field' => 'tanggal_order',
			'label' => 'lang:order_baju_field_tanggal_order',
			'rules' => 'required',
		),
	);
	protected $insert_validation_rules = array();
	protected $skip_validation = false;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Membuat kode order otomatis dengan format ORD-YYYYMMDD-NNNN.
	 *
	 * @return string
	 */
	public function generate_kode_order()
	{
		$prefix = 'ORD-' . date('Ymd') . '-';
		$last = $this->db->select('kode_order')
			->order_by('id', 'desc')
			->limit(1)
			->get($this->table_name)
			->row();

		if ($last) {
			$lastNum = (int) substr($last->kode_order, -4);
			$newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
		} else {
			$newNum = '0001';
		}

		return $prefix . $newNum;
	}
}
