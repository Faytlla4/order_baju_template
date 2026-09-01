<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Master controller
 */
class Master extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('master_jenis_baju/master_jenis_baju_model');
		$this->lang->load('master_jenis_baju');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'master/_sub_nav');
		Assets::add_module_js('master_jenis_baju', 'master_jenis_baju.js');
	}

	public function index()
	{
		Template::set('toolbar_title', lang('master_jenis_baju_manage'));
		Template::render();
	}

	public function create()
	{
		if (isset($_POST['save'])) {
			if ($insert_id = $this->save_master_jenis_baju()) {
				log_activity($this->auth->user_id(), lang('master_jenis_baju_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'master_jenis_baju');
				Template::set_message(lang('master_jenis_baju_create_success'), 'success');

				redirect(SITE_AREA . '/master/jenis_baju');
			}

			if (!empty($this->master_jenis_baju_model->error)) {
				Template::set_message(lang('master_jenis_baju_create_failure') . $this->master_jenis_baju_model->error, 'error');
			}
		}

		Template::set('toolbar_title', lang('master_jenis_baju_action_create'));
		Template::render();
	}

	public function edit()
	{
		$id = $this->uri->segment(5);
		if (empty($id)) {
			Template::set_message(lang('master_jenis_baju_invalid_id'), 'error');

			redirect(SITE_AREA . '/master/jenis_baju');
		}

		if (isset($_POST['save'])) {
			if ($this->save_master_jenis_baju('update', $id)) {
				log_activity($this->auth->user_id(), lang('master_jenis_baju_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'master_jenis_baju');
				Template::set_message(lang('master_jenis_baju_edit_success'), 'success');
				redirect(SITE_AREA . '/master/jenis_baju');
			}

			if (!empty($this->master_jenis_baju_model->error)) {
				Template::set_message(lang('master_jenis_baju_edit_failure') . $this->master_jenis_baju_model->error, 'error');
			}
		} elseif (isset($_POST['delete'])) {
			if ($this->hapus_master_cascade('master_jenis_baju', 'jenis_baju_id', $id)) {
				log_activity($this->auth->user_id(), lang('master_jenis_baju_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'master_jenis_baju');
				Template::set_message(lang('master_jenis_baju_delete_success'), 'success');

				redirect(SITE_AREA . '/master/jenis_baju');
			}

			Template::set_message(lang('master_jenis_baju_delete_failure') . $this->master_jenis_baju_model->error, 'error');
		}

		$master = $this->master_jenis_baju_model->find($id);
		Template::set('master_jenis_baju', $master);

		// Ambil order terkait untuk menampilkan Customer dan Produk.
		$order = $this->db->select('nama_customer, produk')
			->where('jenis_baju_id', $id)
			->order_by('id', 'asc')
			->limit(1)
			->get('order_baju')
			->row();
		Template::set('order_terkait', $order);

		Template::set('toolbar_title', lang('master_jenis_baju_edit_heading'));
		Template::render();
	}

	private function save_master_jenis_baju($type = 'insert', $id = 0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

		$this->form_validation->set_rules($this->master_jenis_baju_model->get_validation_rules());

		// Validasi Customer dan Produk (input pembentuk order awal) — insert & update.
		$this->form_validation->set_rules(array(
			array('field' => 'nama_customer', 'label' => 'Customer', 'rules' => 'required|max_length[100]'),
			array('field' => 'produk',        'label' => 'Produk',    'rules' => 'required|max_length[100]'),
		));

		if ($this->form_validation->run() === false) {
			return false;
		}

		$data = $this->master_jenis_baju_model->prep_data($this->input->post());

		if (!isset($data['status'])) {
			$data['status'] = 1;
		}

		// Ambil Customer dan Produk dari POST (bukan kolom master_jenis_baju).
		$nama_customer = $this->input->post('nama_customer');
		$produk        = $this->input->post('produk');

		// Urutan tidak boleh diisi/ubah oleh user.
		// Saat insert, sistem menentukan nomor berikutnya.
		// Saat update, urutan lama dipertahankan.
		if ($type == 'insert') {
			$data['urutan'] = $this->master_jenis_baju_model->get_next_urutan();
		} else {
			unset($data['urutan']);
		}

		$return = false;
		if ($type == 'insert') {
			$id = $this->master_jenis_baju_model->insert($data);

			if (is_numeric($id)) {
				$return = $id;

				// Auto-create Content/Order HANYA saat master Aktif.
				// Jika master Non-Aktif, tidak membuat order baru.
				if (!isset($data['status']) || (int) $data['status'] === 1) {
					$this->create_order_dari_master($id, $nama_customer, $produk);
				}
			}
		} elseif ($type == 'update') {
			$return = $this->master_jenis_baju_model->update($id, $data);

			// Update Customer dan Produk pada order terkait.
			$this->db->where('jenis_baju_id', $id)->update('order_baju', array(
				'nama_customer' => $nama_customer,
				'produk'        => $produk,
			));
		}

		return $return;
	}

	/**
	 * Membuat satu row order_baju setiap kali master jenis baju baru disimpan.
	 *
	 * @param int    $master_id     ID master jenis baju.
	 * @param string $nama_customer Nama customer dari input form.
	 * @param string $produk        Nama produk dari input form.
	 *
	 * @return void
	 */
	private function create_order_dari_master($master_id, $nama_customer, $produk)
	{
		$this->load->model('order_baju/order_baju_model');

		$kode = $this->order_baju_model->generate_kode_order();

		$this->order_baju_model->insert(array(
			'kode_order'    => $kode,
			'nama_customer' => $nama_customer,
			'produk'        => $produk,
			'jenis_baju_id' => $master_id,
			'jumlah'        => 1,
			'harga'         => 0,
			'total_harga'   => 0,
			'status_order'  => 'Diproses',
			'tanggal_order' => date('Y-m-d'),
		));
	}

	public function get_data()
	{
		$return = $this->master_jenis_baju_model->find_all();

		echo json_encode($return);
	}

	/**
	 * Hapus master secara permanen beserta order Content yang memakainya.
	 *
	 * @param string $master_table Tabel master (master_jenis_baju / master_ukuran / master_warna).
	 * @param string $fk_field     Kolom FK pada order_baju.
	 * @param int    $master_id    ID master yang dihapus.
	 *
	 * @return bool
	 */
	private function hapus_master_cascade($master_table, $fk_field, $master_id)
	{
		$master_id = (int) $master_id;
		if ($master_id <= 0) {
			$this->master_jenis_baju_model->error = 'Data master tidak ditemukan.';
			return false;
		}

		$this->db->trans_start();

		// Hapus semua order Content yang memakai master ini (refresh Content).
		$order_ids = $this->db->select('id')->where($fk_field, $master_id)->get('order_baju')->result();
		foreach ($order_ids as $oid) {
			$this->hapus_order_cascade($oid->id, $master_table, $fk_field);
		}

		// Hapus master permanen.
		$this->db->where('id', $master_id)->delete($master_table);

		// Rapikan urutan aktif master.
		$this->master_jenis_baju_model->reorder_aktif();

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$this->master_jenis_baju_model->error = 'Terjadi kesalahan saat menghapus data.';
			return false;
		}

		// ID kembali ke 1 bila tabel kosong setelah cascade delete.
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
	 * Hard-delete satu order Content dan bersihkan master (selain yang sedang
	 * dihapus) yang tidak lagi dipakai order lain.
	 *
	 * @param int $order_id
	 * @param string $skip_table Tabel master yang sedang diproses.
	 * @param string $skip_field FK master yang sedang diproses.
	 *
	 * @return void
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

		// Bersihkan master lain yang tak dipakai (kecuali master yang sedang dihapus)
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