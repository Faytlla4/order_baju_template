<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Content controller for Order Baju
 */
class Content extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('order_baju/order_baju_model');
		$this->load->model('master_ukuran/master_ukuran_model');
		$this->load->model('master_warna/master_warna_model');
		$this->load->model('master_jenis_baju/master_jenis_baju_model');
		$this->lang->load('order_baju');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'content/_sub_nav');
		Assets::add_module_js('order_baju', 'order_baju.js');
	}

	/**
	 * Display a list of Order Baju data.
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set('toolbar_title', lang('order_baju_manage'));
		Template::render();
	}

	/**
	 * Create an Order Baju object.
	 *
	 * @return void
	 */
	public function create()
	{
		if (isset($_POST['save'])) {
			if ($insert_id = $this->save_order_baju()) {
				log_activity($this->auth->user_id(), lang('order_baju_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'order_baju');
				Template::set_message(lang('order_baju_create_success'), 'success');

				redirect(SITE_AREA . '/content/order_baju');
			}

			// Not validation error
			if (!empty($this->order_baju_model->error)) {
				Template::set_message(lang('order_baju_create_failure') . $this->order_baju_model->error, 'error');
			}
		}

		Template::set('toolbar_title', lang('order_baju_action_create'));
		$this->set_master_options();
		Template::render();
	}

	/**
	 * Allows editing of Order Baju data.
	 *
	 * @return void
	 */
	public function edit()
	{
		$id = $this->uri->segment(5);
		if (empty($id)) {
			Template::set_message(lang('order_baju_invalid_id'), 'error');

			redirect(SITE_AREA . '/content/order_baju');
		}

		if (isset($_POST['save'])) {
			if ($this->save_order_baju('update', $id)) {
				log_activity($this->auth->user_id(), lang('order_baju_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'order_baju');
				Template::set_message(lang('order_baju_edit_success'), 'success');
				redirect(SITE_AREA . '/content/order_baju');
			}

			// Not validation error
			if (!empty($this->order_baju_model->error)) {
				Template::set_message(lang('order_baju_edit_failure') . $this->order_baju_model->error, 'error');
			}
		} elseif (isset($_POST['delete'])) {
			if ($this->hard_delete_order($id)) {
				log_activity($this->auth->user_id(), lang('order_baju_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'order_baju');
				Template::set_message(lang('order_baju_delete_success'), 'success');

				redirect(SITE_AREA . '/content/order_baju');
			}

			Template::set_message('Gagal menghapus order: ' . ($this->order_baju_model->error ? $this->order_baju_model->error : 'Data order tidak ditemukan atau sudah dihapus.'), 'error');
		}

		Template::set('order_baju', $this->order_baju_model->find($id));
		Template::set('toolbar_title', lang('order_baju_edit_heading'));
		$this->set_master_options();
		$this->merge_current_master_options();
		Template::render();
	}

	//--------------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------------

	/**
	 * Siapkan data dropdown untuk ukuran, warna, dan jenis baju dari tabel master.
	 *
	 * @return void
	 */
	private function set_master_options()
	{
		$ukuran_options = $this->dedupe_options('master_ukuran', 'nama_ukuran');
		$warna_options  = $this->dedupe_options('master_warna', 'nama_warna');
		$jenis_options  = $this->dedupe_options('master_jenis_baju', 'nama_jenis');

		Template::set('ukuran_options', $ukuran_options);
		Template::set('warna_options', $warna_options);
		Template::set('jenis_options', $jenis_options);
	}

	/**
	 * Ambil pilihan dropdown master yang aktif, dengan menghilangkan duplikat
	 * berdasarkan nama. Yang dipakai adalah id TERBARU (id terbesar) untuk tiap
	 * nama, tanpa menghapus data di database.
	 *
	 * @param string $table Tabel master (master_ukuran / master_warna / master_jenis_baju).
	 * @param string $name_field Kolom nama pada tabel tersebut.
	 *
	 * @return array id => nama.
	 */
	private function dedupe_options($table, $name_field)
	{
		$options = array();
		$by_name = array();

		// Ambil data aktif, urut dari id terbesar ke terkecil.
		// Dengan begitu nilai yang tersimpan per nama adalah id terbaru.
		$rows = $this->db->select('id, ' . $name_field)
			->where('status', 1)
			->order_by('id', 'desc')
			->get($table)
			->result();

		foreach ($rows as $row) {
			if (!isset($by_name[$row->{$name_field}])) {
				$by_name[$row->{$name_field}] = $row->id;
			}
		}

		foreach ($by_name as $nama => $id) {
			$options[$id] = $nama;
		}

		return $options;
	}

	/**
	 * Pastikan pada saat EDIT, master yang sedang dipakai order (meskipun status
	 * Non Aktif / deleted) tetap muncul sebagai nilai terpilih dengan label
	 * "(Non Aktif)". Opsi Aktif tetap bersumber dari master yang aktif.
	 *
	 * @return void
	 */
	private function merge_current_master_options()
	{
		$order = Template::get('order_baju');
		if (!$order) {
			return;
		}

		$maps = array(
			'ukuran_options' => array('master_ukuran', 'nama_ukuran', $order->ukuran_id),
			'warna_options'  => array('master_warna', 'nama_warna', $order->warna_id),
			'jenis_options'  => array('master_jenis_baju', 'nama_jenis', $order->jenis_baju_id),
		);

		foreach ($maps as $option_name => $cfg) {
			list($table, $name_field, $master_id) = $cfg;
			if (empty($master_id)) {
				continue;
			}

			$current = $this->db->select($name_field . ', status')
				->where('id', $master_id)
				->get($table)
				->row();

			if (!$current) {
				continue;
			}

			$bahwa_nonaktif =
				((int) $current->status !== 1);
			$label = $current->{$name_field};
			if ($bahwa_nonaktif) {
				$label .= ' (Non Aktif)';
			}

			$options = Template::get($option_name);
			if (!is_array($options)) {
				$options = array();
			}
			if (!isset($options[$master_id])) {
				$options[$master_id] = $label;
			} elseif ($bahwa_nonaktif) {
				$options[$master_id] = $label;
			}
			Template::set($option_name, $options);
		}
	}

	/**
	 * Save the data.
	 *
	 * @param string $type Either 'insert' or 'update'.
	 * @param int    $id   The ID of the record to update, ignored on inserts.
	 *
	 * @return boolean|integer An ID for successful inserts, true for successful
	 * updates, else false.
	 */
	private function save_order_baju($type = 'insert', $id = 0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

		// Validate the data
		$this->form_validation->set_rules($this->order_baju_model->get_validation_rules());
		if ($this->form_validation->run() === false) {
			return false;
		}

		// Make sure we only pass in the fields we want
		$data = $this->order_baju_model->prep_data($this->input->post());

		// Compute total_harga = jumlah * harga
		$jumlah = (float) $this->input->post('jumlah');
		$harga = (float) $this->input->post('harga');
		if ($jumlah <= 0) {
			$jumlah = 1;
		}
		$data['jumlah'] = $jumlah;
		$data['harga'] = $harga;
		$data['total_harga'] = $jumlah * $harga;

		// Status default Diproses untuk order baru (workflow: dibuat -> diproses)
		if ($type == 'insert' && empty($data['status_order'])) {
			$data['status_order'] = 'Diproses';
		}

		// Handle tanggal_order
		$data['tanggal_order'] = $this->input->post('tanggal_order') ? $this->input->post('tanggal_order') : '0000-00-00';

		// Generate kode_order if empty (for insert only)
		if ($type == 'insert' && empty($data['kode_order'])) {
			$data['kode_order'] = $this->generate_kode_order();
		}

		// Saat update: jika kode_order kosong, jangan timpa kode lama
		if ($type == 'update' && empty($data['kode_order'])) {
			unset($data['kode_order']);
		}

		$return = false;
		if ($type == 'insert') {
			$id = $this->order_baju_model->insert($data);

			if (is_numeric($id)) {
				$return = $id;
			}
		} elseif ($type == 'update') {
			$return = $this->order_baju_model->update($id, $data);
		}

		return $return;
	}

	/**
	 * Generate a unique order code.
	 *
	 * @return string
	 */
	private function generate_kode_order()
	{
		$prefix = 'ORD-' . date('Ymd') . '-';
		$last = $this->db->select('kode_order')->order_by('id', 'desc')->limit(1)->get('order_baju')->row();

		if ($last) {
			$lastNum = (int) substr($last->kode_order, -4);
			$newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
		} else {
			$newNum = '0001';
		}

		return $prefix . $newNum;
	}

	/**
	 * Get all data as JSON for DataTable.
	 *
	 * @return void
	 */
	public function get_data()
	{
		$return = $this->order_baju_model->find_all();

		if (!empty($return['data']) && is_array($return['data'])) {
			// Peta id -> nama dari tabel master
			$ukuran_names = $this->id_name_map('master_ukuran', 'nama_ukuran');
			$warna_names = $this->id_name_map('master_warna', 'nama_warna');
			$jenis_names = $this->id_name_map('master_jenis_baju', 'nama_jenis');

			foreach ($return['data'] as &$row) {
				$row->ukuran_nama = isset($ukuran_names[$row->ukuran_id]) ? $ukuran_names[$row->ukuran_id] : '-';
				$row->warna_nama = isset($warna_names[$row->warna_id]) ? $warna_names[$row->warna_id] : '-';
				$row->jenis_nama = isset($jenis_names[$row->jenis_baju_id]) ? $jenis_names[$row->jenis_baju_id] : '-';
			}
		}

		echo json_encode($return);
	}

	private function id_name_map($table, $name_field)
	{
		$map = array();
		$rows = $this->db->select('id, ' . $name_field . ', status')->get($table)->result();
		foreach ($rows as $row) {
			$label = $row->{$name_field};
			if ((int) $row->status !== 1) {
				$label .= ' (Non Aktif)';
			}
			$map[$row->id] = $label;
		}
		return $map;
	}

	/**
	 * Hapus order secara permanen (hard delete) dan bersihkan master terkait
	 * yang tidak lagi digunakan oleh order lain.
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	private function hard_delete_order($order_id)
	{
		$order_id = (int) $order_id;
		if ($order_id <= 0) {
			$this->order_baju_model->error = 'Data order tidak ditemukan atau sudah dihapus.';
			return false;
		}

		// Ambil order + FK master terkait
		$order = $this->db->select('id, jenis_baju_id, ukuran_id, warna_id')
			->where('id', $order_id)
			->get('order_baju')
			->row();

		if (!$order) {
			$this->order_baju_model->error = 'Data order tidak ditemukan atau sudah dihapus.';
			return false;
		}

		$jenis_id = $order->jenis_baju_id;
		$ukuran_id = $order->ukuran_id;
		$warna_id = $order->warna_id;

		$this->db->trans_start();

		// Hapus transaksi terkait terlebih dahulu agar hard-delete order
		// tidak melanggar FK fk_transaksi_order_baju.
		$this->db->where('order_baju_id', $order_id)->delete('transaksi');

		// Hapus order terlebih dahulu (jangan langkah pelanggaran FK)
		$this->db->where('id', $order_id)->delete('order_baju');

		// Bersihkan master yang tidak lagi dipakai order lain
		$this->cleanup_master('master_jenis_baju', 'jenis_baju_id', $jenis_id, $order_id);
		$this->cleanup_master('master_ukuran', 'ukuran_id', $ukuran_id, $order_id);
		$this->cleanup_master('master_warna', 'warna_id', $warna_id, $order_id);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$this->order_baju_model->error = 'Terjadi kesalahan saat menghapus data.';
			return false;
		}

		// ID kembali ke 1 bila tabel kosong setelah hard delete.
		$this->reset_sequence_if_empty('order_baju', 'order_baju_id_seq');
		$this->reset_sequence_if_empty('master_jenis_baju', 'master_jenis_baju_id_seq');
		$this->reset_sequence_if_empty('master_ukuran', 'master_ukuran_id_seq');
		$this->reset_sequence_if_empty('master_warna', 'master_warna_id_seq');

		return true;
	}

	/**
	 * Reset sequence PostgreSQL ke 1 bila tabel sudah kosong.
	 *
	 * @param string $table Nama tabel.
	 * @param string $seq   Nama sequence.
	 *
	 * @return void
	 */
	private function reset_sequence_if_empty($table, $seq)
	{
		$count = (int) $this->db->count_all($table);
		if ($count === 0) {
			$this->db->query('ALTER SEQUENCE ' . $seq . ' RESTART WITH 1');
		}
	}

	/**
	 * Hapus satu master bila master tersebut tidak dipakai oleh order lain
	 * (order yang sedang dihapus dikecualikan). Menjaga master yang masih
	 * direferensikan order lain tetap aman.
	 *
	 * @param string $table     Tabel master (master_jenis_baju / master_ukuran / master_warna).
	 * @param string $fk_field  Kolom FK pada order_baju.
	 * @param int    $master_id ID master.
	 * @param int    $except_id ID order yang sedang dihapus.
	 *
	 * @return void
	 */
	private function cleanup_master($table, $fk_field, $master_id, $except_id)
	{
		if (empty($master_id)) {
			return;
		}

		$count = $this->db->where($fk_field, $master_id)
			->where('id !=', $except_id)
			->count_all_results('order_baju');

		if ($count > 0) {
			// Masih dipakai order lain → pertahankan.
			return;
		}

		$this->db->where('id', $master_id)->delete($table);

		// Rapikan urutan aktif master bila model punya reorder_aktif()
		switch ($table) {
			case 'master_jenis_baju':
				$this->load->model('master_jenis_baju/master_jenis_baju_model');
				$this->master_jenis_baju_model->reorder_aktif();
				break;
			case 'master_ukuran':
				$this->load->model('master_ukuran/master_ukuran_model');
				$this->master_ukuran_model->reorder_aktif();
				break;
			case 'master_warna':
				$this->load->model('master_warna/master_warna_model');
				$this->master_warna_model->reorder_aktif();
				break;
		}
	}
}
