<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Transaksi controller
 *
 * Memproses order yang sudah dibuat di Content.
 * Step 1 : user memasukkan kode order (create).
 * Step 2 : sistem menampilkan data order + user mengisi jumlah/harga (step2), lalu simpan.
 */
class Transaksi extends App_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('order_baju/order_baju_model');
		$this->lang->load('order_baju');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'transaksi/_sub_nav');
		Assets::add_module_js('order_baju', 'transaksi.js');
	}

	/**
	 * Daftar transaksi.
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set('toolbar_title', lang('order_baju_manage'));
		Template::render();
	}

	/**
	 * Step 1 — masukkan kode order.
	 *
	 * @return void
	 */
	public function create()
	{
		// Bila user membuka /create/{kode}: langsung proses sebagai kode order
		// supaya tidak tertahan di halaman Step 1.
		$seg5 = $this->uri->segment(5);
		if ($seg5 !== null && $seg5 !== false && $seg5 !== '') {
			$direct = trim(rawurldecode((string) $seg5));
			if ($direct !== '') {
				redirect(SITE_AREA . '/transaksi/order_baju/step2/' . rawurlencode($direct));
				return;
			}
		}

		if ($this->input->post('lanjut')) {
			$result = $this->validasi_kode();
			if ($result !== false && is_object($result)) {
				redirect(SITE_AREA . '/transaksi/order_baju/step2/' . (int) $result->id);
				return;
			}

			// Validasi gagal (kode kosong / tidak ditemukan / sudah selesai).
			// Kembali render Step 1; pesan error ditampilkan lewat Template::message()
			// pada layout. Tidak mencegah form ulang.
		}

		Template::set('toolbar_title', 'Transaksi Baru');
		Template::render();
	}

	/**
	 * Step 2 — tampilkan data order dari kode/id + form jumlah/harga.
	 *
	 * @return void
	 */
	public function step2()
	{
		$seg = $this->uri->segment(5);
		$param = $seg ? rawurldecode((string) $seg) : '';

		// Parameter terima id (angka) atau kode order (teks).
		if ($param !== '' && preg_match('/^\d+$/', $param)) {
			$order = $this->db->select('*')
				->from('order_baju')
				->where('id', (int) $param)
				->where('deleted', 0)
				->limit(1)
				->get()
				->row();
		} else {
			$order = $this->find_order_by_kode($param);
		}

		if (!$order) {
			Template::set_message('Kode order tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}

		if (strtolower($order->status_order) == 'diambil') {
			Template::set_message('Pesanan ini sudah diambil dan tidak dapat diproses kembali.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}

		if (!$this->order_master_aktif($order->id)) {
			Template::set_message('Order tidak dapat diproses karena master terkait berstatus Non Aktif.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}

		Template::set('detail', $this->detail_order($order));
		Template::set('order_id', $order->id);
		Template::set('kode', $order->kode_order);
		Template::set('order_status', $order->status_order);
		Template::set('jumlah_val', $order->jumlah);
		Template::set('harga_val', $order->harga);
		Template::set('toolbar_title', 'Proses Transaksi');
		Template::render();
	}

	/**
	 * Simpan transaksi — UPDATE order yang bersangkutan, bukan INSERT baru.
	 *
	 * @return void
	 */
	public function save()
	{
		$id = (int) $this->input->post('id');
		$kode = trim((string) $this->input->post('kode_order'));
		$jumlah = $this->input->post('jumlah');
		$harga = $this->input->post('harga');

		$order = $this->order_baju_model->find($id);
		if (!$order || $order->kode_order !== $kode) {
			Template::set_message('Order tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}
		if (strtolower($order->status_order) == 'diambil') {
			Template::set_message('Pesanan ini sudah diambil dan tidak dapat diproses kembali.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}

		if (!$this->order_master_aktif($order->id)) {
			Template::set_message('Order tidak dapat diproses karena master terkait berstatus Non Aktif.', 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/create');
			return;
		}

		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->post());
		$this->form_validation->set_rules('jumlah', 'Jumlah', 'required|integer|greater_than[0]');
		$this->form_validation->set_rules('harga', 'Harga', 'required|numeric|greater_than_equal_to[0]');

		if ($this->form_validation->run() === false) {
			Template::set_message(validation_errors(), 'error');
			redirect(SITE_AREA . '/transaksi/order_baju/step2/' . rawurlencode($kode));
			return;
		}

		// Total dihitung ulang di server
		$total = (float) $jumlah * (float) $harga;

		// Status mengikuti pilihan user (dengan nilai aman default).
		$status = trim((string) $this->input->post('status_order'));
		if ($status === '') {
			$status = 'Menunggu';
		}
		$allowed = array(
			'Draft', 'Menunggu', 'Diproses', 'Menunggu Selesai',
			'Selesai', 'Diambil',
		);
		if (!in_array($status, $allowed, true)) {
			$status = 'Menunggu';
		}

		// Update field transaksi (data parsial tidak divalidasi penuh oleh model)
		$ok = $this->db->where('id', $id)
			->update(
				'order_baju',
				array(
					'jumlah'       => (int) $jumlah,
					'harga'        => (float) $harga,
					'total_harga'  => (float) $total,
					'status_order' => $status,
				)
			);

		if ($ok) {
			log_activity($this->auth->user_id(), 'Order ' . $kode . ' diproses menjadi ' . $status, 'ordr_baju');
			Template::set_message('Transaksi berhasil disimpan.', 'success');
		} else {
			Template::set_message('Transaksi gagal disimpan: ' . $this->order_baju_model->error, 'error');
		}

		redirect(SITE_AREA . '/transaksi/order_baju');
	}

	/**
	 * Data JSON untuk DataTable daftar transaksi.
	 *
	 * @return void
	 */
	public function get_data()
	{
		$return = $this->order_baju_model->find_all();

		if (!empty($return['data']) && is_array($return['data'])) {
			$ukuran_n = $this->id_name_map('master_ukuran', 'nama_ukuran');
			$warna_n = $this->id_name_map('master_warna', 'nama_warna');
			$jenis_n = $this->id_name_map('master_jenis_baju', 'nama_jenis');

			// Pesanan "Diambil" tidak ditampilkan pada daftar transaksi aktif.
			$filtered = array();
			foreach ($return['data'] as $row) {
				if (isset($row->status_order) && strtolower($row->status_order) == 'diambil') {
					continue;
				}
				$row->ukuran_nama = isset($ukuran_n[$row->ukuran_id]) ? $ukuran_n[$row->ukuran_id] : '-';
				$row->warna_nama = isset($warna_n[$row->warna_id]) ? $warna_n[$row->warna_id] : '-';
				$row->jenis_nama = isset($jenis_n[$row->jenis_baju_id]) ? $jenis_n[$row->jenis_baju_id] : '-';
				$filtered[] = $row;
			}
			$return['data'] = $filtered;
			$return['recordsTotal'] = count($filtered);
			$return['recordsFiltered'] = count($filtered);
		}

		echo json_encode($return);
	}

	private function find_order_by_kode($kode)
	{
		return $this->db->select('*')
			->from('order_baju')
			->where('kode_order', $kode)
			->where('deleted', 0)
			->limit(1)
			->get()
			->row();
	}

	private function detail_order($order)
	{
		$detail = new stdClass();
		$detail->kode = $order->kode_order;
		$detail->nama_customer = $order->nama_customer;
		$detail->produk = $order->produk;
		$detail->tanggal_order = $order->tanggal_order;
		$detail->jenis_nama = $this->nama_master('master_jenis_baju', 'nama_jenis', $order->jenis_baju_id);
		$detail->ukuran_nama = $this->nama_master('master_ukuran', 'nama_ukuran', $order->ukuran_id);
		$detail->warna_nama = $this->nama_master('master_warna', 'nama_warna', $order->warna_id);
		return $detail;
	}

	private function nama_master($table, $field, $id)
	{
		if (empty($id)) {
			return '-';
		}
		$row = $this->db->select($field)->where('id', $id)->get($table)->row();
		return $row ? $row->{$field} : '-';
	}

	private function id_name_map($table, $name_field)
	{
		$map = array();
		$rows = $this->db->select('id, ' . $name_field)->get($table)->result();
		foreach ($rows as $row) {
			$map[$row->id] = $row->{$name_field};
		}
		return $map;
	}

	/**
	 * Validasi kode yang diisi di Step 1.
	 *
	 * @return object|bool Objek order bila valid, false bila gagal.
	 */
	private function validasi_kode()
	{
		$kode = trim((string) $this->input->post('kode_order'));

		if ($kode === '') {
			Template::set_message('Kode order tidak boleh kosong.', 'error');
			return false;
		}

		$order = $this->find_order_by_kode($kode);
		if (!$order) {
			Template::set_message('Kode order tidak ditemukan.', 'error');
			return false;
		}

		if (strtolower($order->status_order) == 'diambil') {
			Template::set_message('Pesanan ini sudah diambil dan tidak dapat diproses kembali.', 'error');
			return false;
		}

		if (!$this->order_master_aktif($order->id)) {
			Template::set_message('Order tidak dapat diproses karena master terkait berstatus Non Aktif.', 'error');
			return false;
		}

		return $order;
	}

	/**
	 * Cek semua master terkait pada order berada dalam kondisi Aktif
	 * (status = 1 dan deleted = 0). Master kosong dianggap aman.
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
			$m = $this->db->select('status, deleted')
				->where('id', $master_id)
				->get($table)
				->row();
			if (!$m || (int) $m->status !== 1 || (int) $m->deleted === 1) {
				return false;
			}
		}

		return true;
	}
}