<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Transaksi controller
 *
 * Transaksi bertugas MEMPROSES order yang sudah ada di Content (order_baju).
 *
 * Workflow otomatis:
 *   Master -> Content/order -> order_baju dibuat
 *   -> transaksi dibuat OTOMATIS (status Diproses)
 *   -> Transaksi (List + Edit) -> Selesai -> Diambil.
 *
 * Tidak ada lagi New/input kode order manual di Transaksi.
 * Edit hanya memperbarui record transaksi (jumlah/harga/status), data order
 * (kode/customer/produk/jenis/ukuran/warna) tetap dari order_baju.
 *
 * Permission memakai Order_Baju.Content.* (pola permission project).
 */
class Transaksi extends App_Controller
{
	protected $permissionCreate = 'Order_Baju.Content.Create';
	protected $permissionEdit   = 'Order_Baju.Content.Edit';
	protected $permissionView   = 'Order_Baju.Content.View';

	private $status_aktual = array(
		'Diproses',
		'Selesai',
		'Diambil',
	);

	private $dokumen_allowed = array('pdf', 'png', 'jpg', 'jpeg', 'jfif', 'gif', 'doc', 'docx', 'xls', 'xlsx');
	private $dokumen_max_size = 10485760; // 10MB

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permissionView);
		$this->load->model('transaksi/transaksi_model');
		$this->load->model('order_baju/order_baju_model');
		$this->lang->load('order_baju');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set_block('sub_nav', 'order_baju/_sub_nav');
		Assets::add_module_js('transaksi', 'transaksi.js');
	}

	/**
	 * Daftar transaksi + Proses Transaksi (satu halaman, tab/section).
	 *
	 * Tab default: daftar.
	 * Tab 'proses': form proses transaksi.
	 *
	 * @return void
	 */
	public function index()
	{
		$tab = (string) $this->input->get('tab');
		if (!in_array($tab, array('daftar', 'proses'), true)) {
			$tab = 'daftar';
		}

		// Handle form Proses Transaksi submission (search + save).
		$order = null;
		$kode_selected = '';

		if ($tab === 'proses') {
			if (isset($_POST['save'])) {
				$this->auth->restrict($this->permissionCreate);
				$this->save();
				return;
			}

			if ($this->input->post('cari')) {
				$kode = trim((string) $this->input->post('kode_order'));
				if ($kode === '') {
					Template::set_message('Kode order tidak boleh kosong.', 'error');
				} else {
					$order = $this->cari_order_untuk_transaksi($kode);
				}
			} elseif ($this->input->get('kode') !== null) {
				$kode = trim((string) $this->input->get('kode'));
				if ($kode !== '') {
					$order = $this->cari_order_untuk_transaksi($kode);
				}
			}

			if ($order !== null) {
				$kode_selected = $order->kode_order;
				Template::set('detail', $this->detail_order($order));
				Template::set('jumlah_val', $order->jumlah);
				Template::set('harga_val', $order->harga);
			} else {
				$kode_selected = trim((string) $this->input->post('kode_order'));
				if ($kode_selected === '') {
					$kode_selected = trim((string) $this->input->get('kode'));
				}
			}

			Template::set('order', $order);
			Template::set('kode_selected', $kode_selected);
			Template::set('status_options', $this->status_aktual);
		}

		Template::set('active_tab', $tab);
		Template::set_view('order_baju/index');
		Template::set('toolbar_title', 'Transaksi Order Baju');
		Template::render();
	}

	/**
	 * Proses Transaksi — pilih kode order dari Content, tampilkan detail,
	 * isi jumlah/harga/status, lalu simpan ke tabel transaksi.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->auth->restrict($this->permissionCreate);

		// Form transaksi (create.php) mengirim tombol "Simpan / Proses" ke URL
		// create ini; teruskan ke save() agar record transaksi benar-benar dibuat.
		if (isset($_POST['save'])) {
			$this->save();
			return;
		}

		$order = null;
		$direct = '';

		// Dukungan URL langsung: create/{kode}
		$seg5 = $this->uri->segment(5);
		if ($seg5 !== null && $seg5 !== false && $seg5 !== '') {
			$direct = trim(rawurldecode((string) $seg5));
			if ($direct !== '') {
				$order = $this->cari_order_untuk_transaksi($direct);
			}
		} elseif ($this->input->post('cari')) {
			$kode = trim((string) $this->input->post('kode_order'));
			if ($kode === '') {
				Template::set_message('Kode order tidak boleh kosong.', 'error');
			} else {
				$order = $this->cari_order_untuk_transaksi($kode);
			}
		}

		$kode_selected = '';
		if ($order !== null) {
			$kode_selected = $order->kode_order;
			Template::set('detail', $this->detail_order($order));
			Template::set('jumlah_val', $order->jumlah);
			Template::set('harga_val', $order->harga);
		} else {
			if ($direct !== '') {
				$kode_selected = $direct;
			} else {
				$kode_selected = trim((string) $this->input->post('kode_order'));
			}
		}

		Template::set('order', $order);
		Template::set('kode_selected', $kode_selected);
		Template::set('status_options', $this->status_aktual);
		Template::set_view('order_baju/create');
		Template::set('toolbar_title', 'Proses Transaksi');
		Template::render();
	}

	/**
	 * Save — buat record transaksi baru di tabel transaksi.
	 *
	 * @return void
	 */
	public function save()
	{
		$this->auth->restrict($this->permissionCreate);

		$kode   = trim((string) $this->input->post('kode_order'));
		$jumlah = $this->input->post('jumlah');
		$harga  = $this->input->post('harga');

		$order = $this->find_order($kode);
		if (!$order) {
			Template::set_message('Tidak ada order dengan kode tersebut.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=proses');
			return;
		}

		if ($this->is_order_finished($order)) {
			Template::set_message('Order ini sudah selesai atau sudah diambil.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=proses');
			return;
		}

		$status = trim((string) $this->input->post('status_transaksi'));

		$error = $this->validasi_transaksi($jumlah, $harga, $status);
		if ($error !== '') {
			Template::set_message($error, 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=proses&kode=' . rawurlencode($kode));
			return;
		}

		if ($this->transaksi_model->has_transaksi($order->id)) {
			Template::set_message('Order ini sudah diproses.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=proses');
			return;
		}

		$total = (float) $jumlah * (float) $harga;

		// ID transaksi belum ada saat CREATE — upload ke staging dulu,
		// setelah insert dapat ID, file dipindahkan ke public/assets/dokumen_transaksi/[id]/.
		$staging = $this->staging_dir();
		$dokumen_files = $this->upload_documents($staging);
		if ($dokumen_files === false) {
			redirect(SITE_AREA . '/transaksi/transaksi?tab=proses&kode=' . rawurlencode($kode));
			return;
		}

		$data = array(
			'order_baju_id'     => (int) $order->id,
			'jumlah'            => (int) $jumlah,
			'harga'             => (float) $harga,
			'total_harga'       => $total,
			'status_transaksi'  => $status,
			'dokumen'           => json_encode($dokumen_files),
		);

		$id = $this->transaksi_model->insert($data);

		if (is_numeric($id)) {
			// Wajib: buat folder transaksi SETELAH INSERT berhasil,
			// terlepas dari ada/tidaknya file upload. Trigger = transaksi tersimpan,
			// bukan status transaksi.
			$finalDir = $this->dokumen_dir($id);
			if ($finalDir === false) {
				// Folder gagal dibuat — rollback transaksi + file staging, JANGAN anggap sukses.
				$this->transaksi_model->delete($id);
				if (!empty($dokumen_files)) {
					$this->hapus_dokumen_baru($dokumen_files, $staging);
				}
				Template::set_message('Transaksi gagal disimpan: folder dokumen transaksi tidak dapat dibuat.', 'error');
				redirect(SITE_AREA . '/transaksi/transaksi?tab=proses&kode=' . rawurlencode($kode));
				return;
			}

			// Pindahkan file dari staging ke folder akhir [id]/ bila ada.
			if (!empty($dokumen_files) && !$this->pindah_dokumen($staging, $finalDir, $dokumen_files)) {
				// Rollback: hapus record transaksi yang baru saja dibuat + file staging.
				$this->transaksi_model->delete($id);
				$this->hapus_dokumen_baru($dokumen_files, $staging);
				Template::set_message('Transaksi gagal disimpan: file dokumen tidak dapat dipindahkan ke folder penyimpanan.', 'error');
				redirect(SITE_AREA . '/transaksi/transaksi?tab=proses&kode=' . rawurlencode($kode));
				return;
			}

			$this->db->where('id', $order->id)
				->update('order_baju', array(
					'jumlah'       => (int) $jumlah,
					'harga'        => (float) $harga,
					'total_harga'  => $total,
					'status_order' => $status,
				));
			log_activity($this->auth->user_id(), 'Transaksi dibuat untuk order ' . $kode, 'transaksi');
			Template::set_message('Transaksi berhasil disimpan.', 'success');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=daftar');
			return;
		}

		// Jika gagal save DB, hapus file yang sudah terupload dari staging.
		if (!empty($dokumen_files)) {
			$this->hapus_dokumen_baru($dokumen_files, $staging);
		}

		$db_error = $this->db->error();
		$detail = '';
		if (!empty($db_error['message'])) {
			$detail = $db_error['message'];
		} elseif (!empty($this->transaksi_model->error)) {
			$detail = $this->transaksi_model->error;
		}

		log_message('error', 'INSERT transaksi gagal (order ' . $kode . '): ' . $detail);

		Template::set_message(
			'Transaksi gagal disimpan' . ($detail !== '' ? ': ' . $detail : '. Silakan periksa kembali data.'), 'error'
		);
		redirect(SITE_AREA . '/transaksi/transaksi?tab=proses&kode=' . rawurlencode($kode));
	}

	/**
	 * Edit / proses transaksi — data dari tabel transaksi + JOIN order_baju.
	 *
	 * @return void
	 */
	public function edit()
	{
		$id = (int) $this->uri->segment(5);

		if (!$id) {
			Template::set_message('Transaksi tidak valid.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=daftar');
			return;
		}

		$transaksi = $this->transaksi_model->find($id);

		if (!$transaksi) {
			Template::set_message('Transaksi tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=daftar');
			return;
		}

		$order = $this->order_baju_model->find($transaksi->order_baju_id);
		if (!$order) {
			Template::set_message('Order terkait tidak ditemukan.', 'error');
			redirect(SITE_AREA . '/transaksi/transaksi?tab=daftar');
			return;
		}

		$dokumen_json = isset($transaksi->dokumen) ? $transaksi->dokumen : '[]';
		$existing_dokumen = json_decode($dokumen_json, true);
		if (!is_array($existing_dokumen)) {
			$existing_dokumen = array();
		}

		if (isset($_POST['save'])) {
			$this->auth->restrict($this->permissionEdit);

			$jumlah = $this->input->post('jumlah');
			$harga  = $this->input->post('harga');
			$status = trim((string) $this->input->post('status_transaksi'));

			$error = $this->validasi_transaksi($jumlah, $harga, $status);
			if ($error !== '') {
				Template::set_message($error, 'error');
			} else {
				$total = (float) $jumlah * (float) $harga;

				// 1) Upload file baru DULU sebelum apapun — langsung ke folder [id]/.
				$new_files = $this->upload_documents($this->dokumen_dir($id));
				if ($new_files === false) {
					Template::set_message('Gagal mengupload dokumen baru.', 'error');
				} else {
					// 2) Kumpulkan daftar file lama yang akan dihapus dari disk.
					$files_to_delete_from_disk = array();
					$dokumen_to_delete = $this->input->post('hapus_dokumen');
					if (is_array($dokumen_to_delete)) {
						foreach ($dokumen_to_delete as $fname) {
							$fname = basename((string) $fname);
							if ($fname !== '') {
								$key = array_search($fname, $existing_dokumen, true);
								if ($key !== false) {
									unset($existing_dokumen[$key]);
									$files_to_delete_from_disk[] = $fname;
								}
							}
						}
					}

					// 3) Gabungkan: sisa dokumen lama + file baru.
					$merged = array_values(array_merge($existing_dokumen, $new_files));

					$this->db->trans_start();

					$ok = $this->transaksi_model->update_partial(
						$id,
						array(
							'jumlah'           => (int) $jumlah,
							'harga'            => (float) $harga,
							'total_harga'      => $total,
							'status_transaksi' => $status,
							'dokumen'          => json_encode($merged),
						)
					);

					if ($ok) {
						// Sync status order terkait (termasuk Diambil) via order_baju_id.
						$this->db->where('id', $transaksi->order_baju_id)
							->update('order_baju', array(
								'jumlah'       => (int) $jumlah,
								'harga'        => (float) $harga,
								'total_harga'  => $total,
								'status_order' => $status,
							));
					}

					$this->db->trans_complete();

					if ($ok && $this->db->trans_status()) {
						// 4) DB sukses — baru hapus file lama dari disk (yang dicentang).
						if (!empty($files_to_delete_from_disk)) {
							$this->hapus_dokumen_lama($files_to_delete_from_disk, $id);
						}

						log_activity($this->auth->user_id(), 'Transaksi ' . $id . ' diproses menjadi ' . $status, 'transaksi');
						Template::set_message('Transaksi berhasil diperbarui.', 'success');
						redirect(SITE_AREA . '/transaksi/transaksi?tab=daftar');
						return;
					}

					// 5) DB gagal — rollback: hapus file baru yang sudah terupload.
					$this->hapus_dokumen_baru($new_files, $this->dokumen_dir($id));
					Template::set_message('Transaksi gagal diperbarui.', 'error');
				}
			}
		}

		Template::set('transaksi', $transaksi);
		Template::set('order', $order);
		Template::set('detail', $this->detail_order($order));
		Template::set('status_options', $this->status_aktual);
		Template::set('dokumen_files', $existing_dokumen);
		Template::set_view('order_baju/edit');
		Template::set('toolbar_title', 'Edit Transaksi');
		Template::render();
	}

	/**
	 * Data JSON untuk DataTable daftar transaksi.
	 *
	 * @return void
	 */
	public function get_data()
	{
		$this->auth->restrict($this->permissionView);

		$return = $this->transaksi_model->get_list_data();

		echo json_encode($return);
	}

	/**
	 * Alias lama — tetap download (backward compatible dengan link lama).
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file yang tersimpan.
	 *
	 * @return void
	 */
	public function dokumen($id = 0, $file = '')
	{
		$this->download_dokumen($id, $file);
	}

	/**
	 * Lihat dokumen inline (browser preview).
	 *
	 * Hanya untuk tipe yang bisa dipreview browser:
	 * jpg/jpeg/png/gif/webp (gambar) dan pdf (viewer). Format lain
	 * dialihkan ke download (tidak memaksa preview yang tidak didukung).
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file yang tersimpan.
	 *
	 * @return void
	 */
	public function view_dokumen($id = 0, $file = '')
	{
		$path = $this->resolve_dokumen_file($id, $file);
		if ($path === null) {
			show_404();
			return;
		}

		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$mime = array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jfif' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
			'webp' => 'image/webp',
			'pdf'  => 'application/pdf',
		);

		if (!isset($mime[$ext])) {
			// Format tidak didukung preview -> fallback ke download.
			$this->download_dokumen($id, $file);
			return;
		}

		header('Content-Type: ' . $mime[$ext]);
		header('Content-Disposition: inline; filename="' . basename($path) . '"');
		header('Cache-Control: public, max-age=3600');
		readfile($path);
		exit;
	}

	/**
	 * Download dokumen (attachment). Tidak membuat history / mengubah data.
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file yang tersimpan.
	 *
	 * @return void
	 */
	public function download_dokumen($id = 0, $file = '')
	{
		$this->auth->restrict($this->permissionView);

		$path = $this->resolve_dokumen_file($id, $file);
		if ($path === null) {
			show_404();
			return;
		}

		$this->load->helper('download');
		$data = file_get_contents($path);
		force_download(basename($path), $data);
	}

	/**
	 * JSON daftar dokumen + status eksistensi + ukuran untuk modal.
	 *
	 * @param int $id ID transaksi.
	 *
	 * @return void
	 */
	public function get_dokumen_list($id = 0)
	{
		$this->auth->restrict($this->permissionView);

		$id = (int) $id;
		if ($id <= 0) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('ok' => false, 'error' => 'Transaksi tidak valid.')));
			return;
		}

		$row = $this->db->select('transaksi.dokumen, order_baju.kode_order')
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'left')
			->where('transaksi.id', $id)
			->get()
			->row();

		if (!$row) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('ok' => false, 'error' => 'Transaksi tidak ditemukan.')));
			return;
		}

		$files = $this->build_dokumen_files($row->dokumen, $id);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array(
				'ok'    => true,
				'id'    => $id,
				'kode'  => isset($row->kode_order) ? $row->kode_order : '',
				'files' => $files,
				'count' => count($files),
			)));
	}

	/**
	 * Detail satu transaksi untuk modal (READ ONLY).
	 *
	 * Mengirim transaksi.id ke backend, lalu mengambil data dengan JOIN
	 * order_baju + master (reuse query get_detail pada model). Tidak ada
	 * INSERT/UPDATE/DELETE — murni SELECT.
	 *
	 * @param int $id ID transaksi.
	 *
	 * @return void
	 */
	public function detail($id = 0)
	{
		$this->auth->restrict($this->permissionView);

		$id = (int) $id;
		if ($id <= 0) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('ok' => false, 'error' => 'Transaksi tidak valid.')));
			return;
		}

		$row = $this->transaksi_model->get_detail($id);
		if (!$row) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('ok' => false, 'error' => 'Transaksi tidak ditemukan.')));
			return;
		}

		$files = $this->build_dokumen_files($row->dokumen, $id);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array(
				'ok'     => true,
				'id'     => $id,
				'detail' => array(
					'kode_order'       => $row->kode_order,
					'nama_customer'    => $row->nama_customer,
					'produk'           => $row->produk,
					'jenis_nama'       => $row->jenis_nama,
					'ukuran_nama'      => $row->ukuran_nama,
					'warna_nama'       => $row->warna_nama,
					'jumlah'           => (int) $row->jumlah,
					'harga'            => (float) $row->harga,
					'total_harga'      => (float) $row->total_harga,
					'status_transaksi' => $row->status_transaksi,
					'tanggal'          => $row->created_on,
					'dokumen'          => $files,
					'dokumen_count'    => count($files),
				),
			)));
	}

	//--------------------------------------------------------------------------
	// !PRIVATE
	//--------------------------------------------------------------------------

	/**
	 * Validasi transaksi + file, return path absolut bila valid, null bila tidak.
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file yang tersimpan.
	 *
	 * @return string|null
	 */
	private function resolve_dokumen_file($id, $file)
	{
		$this->auth->restrict($this->permissionView);

		$id = (int) $id;
		$file = basename(rawurldecode((string) $file));
		if ($id <= 0 || $file === '') {
			return null;
		}

		$row = $this->transaksi_model->find($id);
		if (!$row) {
			return null;
		}

		$files = array();
		if (isset($row->dokumen) && $row->dokumen !== '') {
			$decoded = json_decode($row->dokumen, true);
			if (is_array($decoded)) {
				foreach ($decoded as $item) {
					$item = basename((string) $item);
					if ($item !== '') {
						$files[] = $item;
					}
				}
			}
		}

		if (!in_array($file, $files, true)) {
			return null;
		}

		$path = $this->resolve_dokumen_path($id, $file);
		if ($path === null) {
			return null;
		}

		return $path;
	}

	/**
	 * Label tipe dokumen untuk ditampilkan di modal.
	 *
	 * @param string $ext
	 *
	 * @return string
	 */
	private function dokumen_tipe_label($ext)
	{
		$labels = array(
			'pdf'  => 'PDF',
			'jpg'  => 'Gambar JPG',
			'jpeg' => 'Gambar JPEG',
			'jfif' => 'Gambar JFIF',
			'png'  => 'Gambar PNG',
			'gif'  => 'Gambar GIF',
			'webp' => 'Gambar WEBP',
			'doc'  => 'Word DOC',
			'docx' => 'Word DOCX',
			'xls'  => 'Excel XLS',
			'xlsx' => 'Excel XLSX',
			'zip'  => 'Arsip ZIP',
		);

		return isset($labels[$ext]) ? $labels[$ext] : strtoupper($ext);
	}

	/**
	 * Bangun daftar file dokumen (nama/ext/tipe/ukuran/url) dari JSON dokumen
	 * suatu transaksi. Dipakai oleh get_dokumen_list() dan detail().
	 *
	 * @param string $dokumen_json Isi kolom dokumen (JSON).
	 * @param int    $id           ID transaksi.
	 *
	 * @return array
	 */
	private function build_dokumen_files($dokumen_json, $id)
	{
		$files = array();
		if (isset($dokumen_json) && $dokumen_json !== '') {
			$decoded = json_decode($dokumen_json, true);
			if (is_array($decoded)) {
				foreach ($decoded as $item) {
					$item = basename((string) $item);
					if ($item === '') {
						continue;
					}
					$path = $this->resolve_dokumen_path($id, $item);
					$ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
					$previewable = in_array($ext, array('jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp', 'pdf'), true);
					$files[] = array(
						'nama'         => $item,
						'ext'          => $ext,
						'tipe'         => $this->dokumen_tipe_label($ext),
						'ukuran'       => ($path !== null) ? (int) filesize($path) : 0,
						'exists'       => ($path !== null),
						'preview'      => $previewable,
						'view_url'     => site_url(SITE_AREA . '/transaksi/transaksi/view_dokumen/' . $id . '/' . rawurlencode($item)),
						'download_url' => site_url(SITE_AREA . '/transaksi/transaksi/download_dokumen/' . $id . '/' . rawurlencode($item)),
					);
				}
			}
		}

		return $files;
	}

	/**
	 * Direktori dokumen transaksi per ID, di FCPATH/assets/dokumen_transaksi/[id]/.
	 * Folder dibuat bila belum ada (kecuali $create = false).
	 * Pada kegagalan pembuatan folder, log error ditulis dan false dikembalikan.
	 *
	 * @param int  $id     ID transaksi.
	 * @param bool $create Buat folder bila belum ada.
	 *
	 * @return string|false Path folder, atau false bila pembuatan gagal.
	 */
	private function dokumen_dir($id, $create = true)
	{
		$dir = FCPATH . 'assets/dokumen_transaksi/' . (int) $id . '/';

		if ($create && !is_dir($dir)) {
			$ok = @mkdir($dir, 0755, true);
			if (!$ok && !is_dir($dir)) {
				log_message('error', 'Transaksi: gagal membuat folder dokumen transaksi — ' . $dir);
				return false;
			}
		}

		if ($create && !is_dir($dir)) {
			log_message('error', 'Transaksi: folder dokumen transaksi tidak tersedia — ' . $dir);
			return false;
		}

		return $dir;
	}

	/**
	 * Direktori staging untuk upload saat CREATE (ID transaksi belum ada).
	 *
	 * @return string
	 */
	private function staging_dir()
	{
		$dir = FCPATH . 'assets/dokumen_transaksi/_staging/';
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		return $dir;
	}

	/**
	 * Resolve path file dokumen dari storage baru (public/assets/dokumen_transaksi/[id]/)
	 * dengan fallback ke lokasi lama (uploads/transaksi/) untuk file legacy.
	 *
	 * @param int    $id   ID transaksi.
	 * @param string $file Nama file.
	 *
	 * @return string|null
	 */
	private function resolve_dokumen_path($id, $file)
	{
		$file = basename((string) $file);
		if ($file === '') {
			return null;
		}

		$path = $this->dokumen_dir($id, false) . $file;
		if (is_file($path)) {
			return $path;
		}

		$legacy = APPPATH . '../uploads/transaksi/' . $file;
		if (is_file($legacy)) {
			return $legacy;
		}

		return null;
	}

	/**
	 * Pindahkan file dari staging ke folder akhir [id]/.
	 *
	 * @param string $srcDir
	 * @param string $dstDir
	 * @param array  $files
	 *
	 * @return bool
	 */
	private function pindah_dokumen($srcDir, $dstDir, $files)
	{
		if (!is_dir($dstDir)) {
			mkdir($dstDir, 0755, true);
		}

		$allOk = true;
		foreach ($files as $f) {
			$f = basename((string) $f);
			if ($f === '') {
				continue;
			}
			$src = $srcDir . $f;
			$dst = $dstDir . $f;
			if (is_file($src)) {
				if (!rename($src, $dst)) {
					$allOk = false;
				}
			} else {
				$allOk = false;
			}
		}

		return $allOk;
	}

	/**
	 * Upload dokumen dari form, return array nama file yang berhasil diupload.
	 * Return FALSE jika ada error (pesan sudah diset via Template::set_message).
	 *
	 * @param string $target_dir Direktori absolut tujuan upload.
	 *
	 * @return array|false
	 */
	private function upload_documents($target_dir = null)
	{
		if (!isset($_FILES['dokumen']) || !is_array($_FILES['dokumen']['name'])) {
			return array();
		}

		if ($target_dir === null) {
			$target_dir = $this->staging_dir();
		}
		$upload_path = rtrim($target_dir, '/\\') . DIRECTORY_SEPARATOR;
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		$uploaded = array();
		$count = count($_FILES['dokumen']['name']);

		for ($i = 0; $i < $count; $i++) {
			$file_name = trim((string) $_FILES['dokumen']['name'][$i]);
			$file_size = (int) $_FILES['dokumen']['size'][$i];
			$file_error = (int) $_FILES['dokumen']['error'][$i];

			if ($file_name === '' || $file_error !== UPLOAD_ERR_OK) {
				continue;
			}

			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

			if (!in_array($ext, $this->dokumen_allowed, true)) {
				Template::set_message(
					'Ekstensi .' . $ext . ' tidak diizinkan. File harus: ' . implode(', ', $this->dokumen_allowed) . '.', 'error'
				);
				$this->hapus_dokumen_baru($uploaded, $upload_path);
				return false;
			}

			if ($file_size > $this->dokumen_max_size) {
				Template::set_message(
					'File "' . $file_name . '" melebihi ukuran maksimum (10MB).', 'error'
				);
				$this->hapus_dokumen_baru($uploaded, $upload_path);
				return false;
			}

			$new_name = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
			$dest = $upload_path . $new_name;

			if (!move_uploaded_file($_FILES['dokumen']['tmp_name'][$i], $dest)) {
				Template::set_message('Gagal menyimpan file "' . $file_name . '".', 'error');
				$this->hapus_dokumen_baru($uploaded, $upload_path);
				return false;
			}

			$uploaded[] = $new_name;
		}

		return $uploaded;
	}

	/**
	 * Hapus file-file yang baru saja diupload (rollback jika terjadi error).
	 *
	 * @param array  $files
	 * @param string $path
	 *
	 * @return void
	 */
	private function hapus_dokumen_baru($files, $path)
	{
		foreach ($files as $f) {
			$fp = rtrim($path, '/') . '/' . $f;
			if (is_file($fp)) {
				unlink($fp);
			}
		}
	}

	/**
	 * Hapus file dokumen lama dari disk (folder baru [id]/ lalu fallback legacy).
	 *
	 * @param array $files
	 * @param int   $id    ID transaksi (untuk folder baru).
	 *
	 * @return void
	 */
	private function hapus_dokumen_lama($files, $id = 0)
	{
		foreach ($files as $f) {
			$f = basename((string) $f);
			if ($f === '') {
				continue;
			}

			$path = null;
			if ($id > 0) {
				$p = $this->dokumen_dir($id, false) . $f;
				if (is_file($p)) {
					$path = $p;
				}
			}
			if ($path === null) {
				$p = APPPATH . '../uploads/transaksi/' . $f;
				if (is_file($p)) {
					$path = $p;
				}
			}

			if ($path !== null) {
				unlink($path);
			}
		}
	}

	/**
	 * Cari order untuk diproses menjadi transaksi.
	 *
	 * @param string $kode
	 *
	 * @return object|null
	 */
	private function cari_order_untuk_transaksi($kode)
	{
		$kode = trim($kode);
		if ($kode === '') {
			Template::set_message('Kode order tidak boleh kosong.', 'error');
			return null;
		}

		$order = $this->find_order($kode);
		if (!$order) {
			Template::set_message('Tidak ada order dengan kode tersebut.', 'error');
			return null;
		}

		if ($this->is_order_finished($order)) {
			Template::set_message('Order ini sudah selesai atau sudah diambil.', 'error');
			return null;
		}

		if ($this->transaksi_model->has_transaksi($order->id)) {
			Template::set_message('Order ini sudah diproses.', 'error');
			return null;
		}

		return $order;
	}

	/**
	 * Cari order di tabel order_baju (non-terhapus).
	 *
	 * @param string $kode
	 *
	 * @return object|null
	 */
	private function find_order($kode)
	{
		return $this->db->select('*')
			->from('order_baju')
			->where('kode_order', $kode)
			->limit(1)
			->get()
			->row();
	}

	/**
	 * Tentukan apakah order sudah tidak dapat diproses (terminal).
	 *
	 * @param object $order
	 *
	 * @return bool
	 */
	private function is_order_finished($order)
	{
		$status = strtolower(trim(
			isset($order->status_order) ? $order->status_order : ''
		));

		return in_array($status, array('selesai', 'diambil'), true);
	}

	/**
	 * Validasi data transaksi (jumlah/harga/status).
	 *
	 * @param mixed $jumlah
	 * @param mixed $harga
	 * @param string $status
	 *
	 * @return string Pesan error ('' bila valid).
	 */
	private function validasi_transaksi($jumlah, $harga, $status)
	{
		if ($jumlah === '' || !is_numeric($jumlah) || (int) $jumlah <= 0) {
			return 'Jumlah harus lebih dari 0.';
		}

		if ($harga === '' || !is_numeric($harga) || $harga < 0) {
			return 'Harga tidak valid.';
		}

		if (!in_array($status, $this->status_aktual, true)) {
			return 'Status transaksi tidak valid.';
		}

		return '';
	}

	/**
	 * Detail order + nama master untuk ditampilkan.
	 *
	 * @param object $order
	 *
	 * @return object
	 */
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

	/**
	 * Ambil nama master berdasarkan id.
	 *
	 * @param string $table
	 * @param string $field
	 * @param int    $id
	 *
	 * @return string
	 */
	private function nama_master($table, $field, $id)
	{
		if (empty($id)) {
			return '-';
		}
		$row = $this->db->select($field)->where('id', (int) $id)->get($table)->row();
		return $row ? $row->{$field} : '-';
	}
}
