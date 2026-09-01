<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Master controller
 */
class Master extends App_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('master_warna/master_warna_model');
		$this->lang->load('master_warna');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'master/_sub_nav');
		Assets::add_module_js('master_warna', 'master_warna.js');
	}

	public function index()
	{
		Template::set('toolbar_title', lang('master_warna_manage'));
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($insert_id = $this->save_master_warna()) {
				log_activity($this->auth->user_id(), lang('master_warna_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'master_warna');
				Template::set_message(lang('master_warna_create_success'), 'success');

				redirect(SITE_AREA . '/master/warna');
			}

			if (!empty($this->master_warna_model->error)) {
				Template::set_message(lang('master_warna_create_failure') . $this->master_warna_model->error, 'error');
			}
		}

		// Customer diambil OTOMATIS dari order berdasarkan kode order yang dimasukkan.
		$kode = trim((string) $this->input->post('kode_order'));
		Template::set('customer_value', $kode !== '' ? $this->customer_by_kode($kode) : '');

		Template::set('toolbar_title', lang('master_warna_action_create'));
		Template::render();
	}

	public function edit()
	{
		$id = $this->uri->segment(5);
		if (empty($id)) {
			Template::set_message(lang('master_warna_invalid_id'), 'error');

			redirect(SITE_AREA . '/master/warna');
		}

		if (isset($_POST['save'])) {
			if ($this->save_master_warna('update', $id)) {
				log_activity($this->auth->user_id(), lang('master_warna_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'master_warna');
				Template::set_message(lang('master_warna_edit_success'), 'success');
				redirect(SITE_AREA . '/master/warna');
			}

			if (!empty($this->master_warna_model->error)) {
				Template::set_message(lang('master_warna_edit_failure') . $this->master_warna_model->error, 'error');
			}
		} elseif (isset($_POST['delete'])) {
			if ($this->hapus_master_cascade('master_warna', 'warna_id', $id)) {
				log_activity($this->auth->user_id(), lang('master_warna_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'master_warna');
				Template::set_message(lang('master_warna_delete_success'), 'success');

				redirect(SITE_AREA . '/master/warna');
			}

			Template::set_message(lang('master_warna_delete_failure') . $this->master_warna_model->error, 'error');
		}

		Template::set('master_warna', $this->master_warna_model->find($id));
		Template::set('toolbar_title', lang('master_warna_edit_heading'));
		Template::render();
	}

	private function save_master_warna($type = 'insert', $id = 0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

		$this->form_validation->set_rules($this->master_warna_model->get_validation_rules());
		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = $this->master_warna_model->prep_data($this->input->post());

		if (!isset($data['status'])) {
			$data['status'] = 1;
		}

		// Validasi order target sebelum menyimpan master (jika kode order diisi).
		$target = $this->input->post('kode_order');
		if ($type == 'insert' && trim((string) $target) !== '') {
			if (!$this->validasi_order_target($target)) {
				$this->master_warna_model->error = 'Order tidak dapat diproses karena master terkait berstatus Non Aktif.';
				return false;
			}
		}

		// Urutan tidak boleh diisi/ubah oleh user.
		// Saat insert, sistem menentukan nomor berikutnya.
		// Saat update, urutan lama dipertahankan.
		if ($type == 'insert') {
			$data['urutan'] = $this->master_warna_model->get_next_urutan();
		} else {
			unset($data['urutan']);
		}

		$return = false;
		if ($type == 'insert') {
			$id = $this->master_warna_model->insert($data);

			if (is_numeric($id)) {
				$return = $id;

				// Lengkapi order yang dipilih user (no INSERT order baru)
				$this->lengkapi_order('warna_id', $id, $this->input->post('kode_order'));
			}
		} elseif ($type == 'update') {
			$return = $this->master_warna_model->update($id, $data);
		}

		return $return;
	}

	/**
	 * Cek semua master terkait pada order berada dalam kondisi Aktif
	 * (status = 1 dan deleted = 0).
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	private function order_master_aktif($order_id)
	{
		$order = $this->db->select('jenis_baju_id, ukuran_id, warna_id')
			->where('id', $order_id)
			->get('order_baju')
			->row();
		if (!$order) {
			return false;
		}

		$maps = array(
			'master_jenis_baju' => $order->jenis_baju_id,
			'master_ukuran' => $order->ukuran_id,
			'master_warna' => $order->warna_id,
		);

		foreach ($maps as $table => $master_id) {
			if (empty($master_id)) {
				continue;
			}
			$m = $this->db->select('status')
				->where('id', $master_id)
				->get($table)
				->row();
			if (!$m || (int) $m->status !== 1) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Update kolom master pada order yang dipilih (jangan INSERT order baru).
	 *
	 * @param string $field Kolom pada order_baju (ukuran_id / warna_id).
	 * @param int    $master_id ID master baru.
	 * @param mixed  $order_id ID order target.
	 *
	 * @return void
	 */
	private function lengkapi_order($field, $master_id, $order_id)
	{
		// Terima ID order ATAU kode order (mis. "ORD-20260809-0001").
		$order_id = trim((string) $order_id);
		if ($order_id === '') {
			return;
		}

		if (preg_match('/^\d+$/', $order_id)) {
			$where = array('id' => (int) $order_id);
		} else {
			$where = array('kode_order' => (string) $order_id);
		}

		$order = $this->db->select('id, status_order')
			->where($where)
			->get('order_baju')
			->row();

		if (!$order) {
			return;
		}
		if (strtolower($order->status_order) == 'selesai') {
			return;
		}
		if (!$this->order_master_aktif($order->id)) {
			return;
		}

		$this->db->where('id', $order->id)
			->update('order_baju', array($field => $master_id));
	}

	/**
	 * Cek apakah order target (id/kode) valid untuk dilengkapi.
	 *
	 * @param mixed $order_id ID atau kode order.
	 *
	 * @return bool
	 */
	private function validasi_order_target($order_id)
	{
		$order_id = trim((string) $order_id);
		if ($order_id === '') {
			return false;
		}
		if (preg_match('/^\d+$/', $order_id)) {
			$where = array('id' => (int) $order_id);
		} else {
			$where = array('kode_order' => (string) $order_id);
		}

		$order = $this->db->select('id, status_order')
			->where($where)
			->get('order_baju')
			->row();

		if (!$order) {
			return false;
		}
		if (strtolower($order->status_order) == 'selesai') {
			return false;
		}
		if (!$this->order_master_aktif($order->id)) {
			return false;
		}

		return true;
	}

	public function get_data()
	{
		$return = $this->master_warna_model->find_all();

		// Hanya tampilkan data aktif (deleted = 0)
		if (!empty($return['data']) && is_array($return['data'])) {
			$aktif = array();
			foreach ($return['data'] as $row) {
				$row->kode_order    = '-';
				$row->nama_customer = '-';
				$aktif[] = $row;
			}

			// Tampilkan relasi order (kode + customer) untuk tiap master.
			$link = array();
			$orders = $this->db->select('warna_id, kode_order, nama_customer')
				->where('warna_id IS NOT NULL')
				->get('order_baju')
				->result();
			foreach ($orders as $o) {
				if (!isset($link[$o->warna_id])) {
					$link[$o->warna_id] = array('kode' => $o->kode_order, 'customer' => $o->nama_customer);
				}
			}
			foreach ($aktif as $row) {
				if (isset($link[$row->id])) {
					$row->kode_order    = $link[$row->id]['kode'];
					$row->nama_customer = $link[$row->id]['customer'];
				}
			}

			$return['data'] = $aktif;
		}

		echo json_encode($return);
	}

	/**
	 * Ambil nama customer dari kode order (untuk isi otomatis di form).
	 *
	 * @return void
	 */
	public function lookup_customer()
	{
		$kode = trim((string) $this->input->post('kode_order'));
		if ($kode === '') {
			echo json_encode(array('found' => false));
			return;
		}

		$order = $this->ambil_order($kode);
		if ($order) {
			echo json_encode(array('found' => true, 'customer' => $order->nama_customer));
			return;
		}

		echo json_encode(array('found' => false));
	}

	/**
	 * Customer dari kode order (untuk ditampilkan readonly pada create).
	 *
	 * @param string $kode
	 *
	 * @return string
	 */
	private function customer_by_kode($kode)
	{
		$order = $this->ambil_order($kode);
		return $order ? $order->nama_customer : '';
	}

	/**
	 * Cari order (non-terhapus) berdasarkan kode order.
	 *
	 * @param string $kode
	 *
	 * @return object|null
	 */
	private function ambil_order($kode)
	{
		$kode = trim($kode);
		if ($kode === '') {
			return null;
		}

		return $this->db->select('id, kode_order, nama_customer')
			->where('kode_order', $kode)
			->limit(1)
			->get('order_baju')
			->row();
	}

	/**
	 * Hapus master permanen beserta order Content yang memakainya.
	 */
	private function hapus_master_cascade($master_table, $fk_field, $master_id)
	{
		$master_id = (int) $master_id;
		if ($master_id <= 0) {
			$this->master_warna_model->error = 'Data master tidak ditemukan.';
			return false;
		}

		$this->db->trans_start();

		$order_ids = $this->db->select('id')->where($fk_field, $master_id)->get('order_baju')->result();
		foreach ($order_ids as $oid) {
			$this->hapus_order_cascade($oid->id, $master_table, $fk_field);
		}

		$this->db->where('id', $master_id)->delete($master_table);
		$this->master_warna_model->reorder_aktif();

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$this->master_warna_model->error = 'Terjadi kesalahan saat menghapus data.';
			return false;
		}

		// ID kembali ke 1 bila tabel kosong setelah cascade delete.
		$this->reset_sequence_if_empty('order_baju', 'order_baju_id_seq');
		$this->reset_sequence_if_empty('master_warna', 'master_warna_id_seq');
		$this->reset_sequence_if_empty('master_ukuran', 'master_ukuran_id_seq');
		$this->reset_sequence_if_empty('master_jenis_baju', 'master_jenis_baju_id_seq');

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
	 * Hard-delete satu order Content dan bersihkan master lain yang tak terpakai.
	 */
	private function hapus_order_cascade($order_id, $skip_table, $skip_field)
	{
		$order = $this->db->select('id, jenis_baju_id, ukuran_id, warna_id')
			->where('id', $order_id)
			->get('order_baju')
			->row();
		if (!$order) {
			return;
		}

		// Hapus transaksi terkait terlebih dahulu agar hard-delete order
		// tidak melanggar FK fk_transaksi_order_baju.
		$this->db->where('order_baju_id', $order_id)->delete('transaksi');

		$this->db->where('id', $order_id)->delete('order_baju');

		$map = array(
			'master_jenis_baju' => array('jenis_baju_id', $order->jenis_baju_id),
			'master_ukuran' => array('ukuran_id', $order->ukuran_id),
			'master_warna' => array('warna_id', $order->warna_id),
		);

		foreach ($map as $table => $pair) {
			if ($table === $skip_table) {
				continue;
			}
			list($field, $value) = $pair;
			if (empty($value)) {
				continue;
			}
			$count = $this->db->where($field, $value)
				->where('id !=', $order_id)
				->count_all_results('order_baju');
			if ($count > 0) {
				continue;
			}
			$this->db->where('id', $value)->delete($table);
		}
	}
}