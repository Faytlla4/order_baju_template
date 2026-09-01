<?php defined('BASEPATH') || exit('No direct script access allowed');

class Master_warna_model extends DT_Model
{
	public $table_name = 'master_warna';
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
			'field' => 'nama_warna',
			'label' => 'lang:master_warna_field_nama_warna',
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
	 * Cari nomor urutan berikutnya untuk data aktif pada tabel ini.
	 *
	 * @return int
	 */
	public function get_next_urutan()
	{
$max = $this->db->select_max('urutan')
			->get($this->table_name)
			->row()->urutan;

		return (int) $max + 1;
	}

	/**
	 * Rapikan ulang urutan data aktif menjadi 1, 2, 3, ... dst.
	 * Hanya mengubah kolom urutan, tidak pernah mengubah id.
	 *
	 * @return void
	 */
	public function reorder_aktif()
	{
$rows = $this->db->select('id')
			->order_by('urutan', 'asc')->order_by('id', 'asc')
			->get($this->table_name)
			->result();

		foreach ($rows as $i => $row) {
			$this->db->update($this->table_name, array('urutan' => $i + 1), array('id' => $row->id));
		}
	}
}

