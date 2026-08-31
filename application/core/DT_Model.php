<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Bonfire
 *
 * An open source project to allow developers to jumpstart their development of
 * CodeIgniter applications.
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2014, Bonfire Dev Team
 * @license   http://opensource.org/licenses/MIT
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * DT_Model extends BF_Model for backwards compatibility, and to provide a
 * placeholder class that your project can customize/extend as needed.
 *
 * @package    Bonfire\Core\DT_Model
 * @author     Lonnie Ezell
 * @link       http://cibonfire.com/docs/developer/bonfire_models
 */
class DT_Model extends BF_Model
{
	/**
	 * Nama kolom status untuk filter status opsional (mis. 'order_baju.status_order').
	 * Kosongkan (default) untuk menonaktifkan filter status pada model ini.
	 *
	 * @var string
	 */
	protected $filter_status_field = '';

	/**
	 * Status yang diizinkan untuk filter (whitelist).
	 *
	 * @var array
	 */
	protected $filter_status_allowed = array('Diproses', 'Diambil', 'Selesai');

	/**
	 * DT_Model's constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function find_all()
	{
		$request = $this->input->post();
		$output['draw'] = (int) $request['draw'];

		// Filter status opsional (dari bfDataTable `params.status`).
		$status = isset($request['params']['status']) ? trim((string) $request['params']['status']) : '';
		$apply_status = $this->filter_status_field !== '' && $status !== ''
			&& in_array($status, $this->filter_status_allowed, true);

		// recordsTotal = total setelah filter status, sebelum search.
		// count_all_results($table) dengan reset=TRUE: FROM ditambahkan sekali,
		// lalu dibersihkan sehingga tidak terjadi FROM duplikat.
		if ($apply_status) {
			$this->db->where($this->filter_status_field, $status);
		}
		$output['recordsTotal'] = $this->db->count_all_results($this->table_name);

		// recordsFiltered = total setelah status + search, sebelum pagination.
		if ($apply_status) {
			$this->db->where($this->filter_status_field, $status);
		}
		$has_search = !empty($request['search']['value']) && strlen($request['search']['value']) > 0;
		if ($has_search) {
			parent::where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}
		$output['recordsFiltered'] = $this->db->count_all_results($this->table_name);

		// Re-apply filters (cleared by count_all_results reset) for data query.
		if ($apply_status) {
			$this->db->where($this->filter_status_field, $status);
		}
		if ($has_search) {
			parent::where($request['search']['column'] . "::TEXT ILIKE '%" . $request['search']['value'] . "%'");
		}

		if (empty($request['sort'])) {
			parent::order_by($this->key, 'desc');
		} else {
			parent::order_by($request['sort']);
		}

		parent::limit($request['length'], $request['start']);
		$data = parent::find_all();
		$output['data'] = $data;

		return $output;
	}
}
/* End of file ./application/core/DT_Model.php */