<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Reports controller — Laporan Transaksi Excel.
 *
 * Read-only: mengambil data dari tabel transaksi (JOIN order_baju + master),
 * filter periode (Semua/Hari Ini/Bulan Ini/Custom) dan export Excel.
 */
class Reports extends App_Controller
{
	protected $permissionView = 'Site.Reports.View';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permissionView);
		$this->load->model('report_excel/report_model');
		$this->load->library('report_excel/report_excel');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set('toolbar_title', 'Laporan Transaksi Excel');
	}

	/**
	 * Halaman laporan Excel + filter.
	 *
	 * @return void
	 */
	public function index()
	{
		$periode   = (string) $this->input->get('periode');
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get('status'));
		$filter_valid = true;

		if (!in_array($periode, array('all', 'today', 'month', 'custom', ''), true)) {
			$periode = 'all';
		}
		if ($periode === '') {
			$periode = 'all';
		}

		if ($periode === 'custom') {
			if ($tgl_mulai === '' && $tgl_akhir === '') {
				Template::set_message('Pilih Tanggal Mulai dan Tanggal Akhir.', 'error');
				$filter_valid = false;
			} elseif ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
				Template::set_message('Tanggal Mulai tidak boleh setelah Tanggal Akhir.', 'error');
				$filter_valid = false;
			}
		}

		if ($periode !== 'custom') {
			$tgl_mulai = '';
			$tgl_akhir = '';
		}

		$rows = $filter_valid ? $this->report_model->get_report($periode, $tgl_mulai, $tgl_akhir, $status) : array();

		Template::set('periode', $periode);
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('status', $status);
		Template::set('periode_label', $this->report_model->periode_label($periode, $tgl_mulai, $tgl_akhir));
		Template::set('rows', $rows);
		Template::set('grand_total', $this->report_model->grand_total($rows));

		// History (shared from report table)
		$this->load_history();

		Template::set_view('reports/index');
		Template::render();
	}

	/**
	 * AJAX filter — kembalikan HTML kartu data (tabel + summary) untuk periode
	 * yang dipilih tanpa reload halaman.
	 *
	 * @return void
	 */
	public function filter()
	{
		$this->auth->restrict($this->permissionView);

		$periode   = (string) $this->input->get('periode');
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get('status'));
		$filter_valid = true;
		$error = '';

		if (!in_array($periode, array('all', 'today', 'month', 'custom', ''), true)) {
			$periode = 'all';
		}
		if ($periode === '') {
			$periode = 'all';
		}

		if ($periode === 'custom') {
			if ($tgl_mulai === '' && $tgl_akhir === '') {
				$error = 'Pilih Tanggal Mulai dan Tanggal Akhir.';
				$filter_valid = false;
			} elseif ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir) {
				$error = 'Tanggal Mulai tidak boleh setelah Tanggal Akhir.';
				$filter_valid = false;
			}
		}

		if ($periode !== 'custom') {
			$tgl_mulai = '';
			$tgl_akhir = '';
		}

		if (!$filter_valid) {
			$this->output->set_content_type('application/json')
				->set_output(json_encode(array('ok' => false, 'error' => $error)));
			return;
		}

		$rows = $this->report_model->get_report($periode, $tgl_mulai, $tgl_akhir, $status);

		$html = $this->load->view('reports/_data', array(
			'periode_label' => $this->report_model->periode_label($periode, $tgl_mulai, $tgl_akhir),
			'rows'          => $rows,
			'grand_total'   => $this->report_model->grand_total($rows),
			'status'        => $status,
		), true);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('ok' => true, 'html' => $html)));
	}

	/**
	 * Export Excel laporan (mengikuti filter).
	 *
	 * @return void
	 */
	public function excel()
	{
		$periode   = (string) $this->input->get('periode');
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$status    = $this->normalize_status($this->input->get('status'));
		$filter_valid = true;

		if (!in_array($periode, array('all', 'today', 'month', 'custom'), true)) {
			$periode = 'all';
		}
		if ($periode !== 'custom') {
			$tgl_mulai = '';
			$tgl_akhir = '';
		} elseif (($tgl_mulai === '' && $tgl_akhir === '') || ($tgl_mulai !== '' && $tgl_akhir !== '' && $tgl_mulai > $tgl_akhir)) {
			$filter_valid = false;
		}

		if (!$filter_valid) {
			log_message('error', 'Report Excel: filter tidak valid (periode=' . $periode . ', mulai=' . $tgl_mulai . ', akhir=' . $tgl_akhir . ').');
			Template::set_message('Filter tanggal tidak valid. Periksa kembali Tanggal Mulai dan Tanggal Akhir.', 'error');
			redirect(SITE_AREA . '/reports/report_excel');
			return;
		}

		$existing = $this->find_recent_excel($periode, $tgl_mulai, $tgl_akhir, $status);
		if ($existing) {
			$path = APPPATH . '../' . $existing->path_file;
			if (is_file($path)) {
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="' . basename($existing->nama_file) . '"');
				header('Cache-Control: max-age=0');
				header('Pragma: public');
				header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
				readfile($path);
				exit;
			}
		}

		$rows = $this->report_model->get_report($periode, $tgl_mulai, $tgl_akhir, $status);

		$headers = array('No', 'Kode', 'Customer', 'Produk', 'Jenis', 'Ukuran', 'Warna', 'Jumlah', 'Harga', 'Total', 'Status', 'Tanggal');
		$widths  = array(25, 85, 90, 80, 70, 35, 50, 40, 75, 80, 55, 75);
		$aligns  = array('center', 'left', 'left', 'left', 'left', 'center', 'left', 'center', 'right', 'right', 'center', 'center');

		$data_rows = array();
		$no = 0;
		foreach ($rows as $r) {
			$no++;
			$data_rows[] = array(
				$no,
				$r->kode_order,
				$r->nama_customer,
				$r->produk,
				$r->jenis_nama,
				$r->ukuran_nama,
				$r->warna_nama,
				(int) $r->jumlah,
				(float) $r->harga,
				(float) $r->total_harga,
				$r->status_transaksi,
				$r->tanggal,
			);
		}

		$label = $this->report_model->periode_label($periode, $tgl_mulai, $tgl_akhir);

		$footers = array();
		if (!empty($rows)) {
			$footers[] = 'Total Transaksi: ' . count($rows);
			$footers[] = 'Total Nilai: Rp ' . number_format($this->report_model->grand_total($rows), 0, ',', '.');
		}

		$this->report_excel->set_data('LAPORAN TRANSAKSI ORDER BAJU', $label, $headers, $widths, $data_rows, $footers, $aligns);

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$nama_file = 'laporan_transaksi_' . $wib->format('Y-m-d_His') . '_' . substr(uniqid('', true), -5) . '.xlsx';

		$path_rel = 'uploads/report/' . $nama_file;
		$full = APPPATH . '../' . $path_rel;

		// Simpan file ke disk
		$saved = $this->report_excel->save($full);
		if (!$saved || !is_file($full) || filesize($full) <= 0) {
			log_message('error', 'Report Excel: gagal menyimpan file ke ' . $full);
			Template::set_message('Gagal membuat file Excel.', 'error');
			redirect(SITE_AREA . '/reports/report_excel');
			return;
		}

		// Verifikasi header XLSX
		$head = file_get_contents($full, false, null, 0, 2);
		if ($head === false || strncmp($head, 'PK', 2) !== 0) {
			@unlink($full);
			log_message('error', 'Report Excel: file hasil simpan tidak valid (' . $full . ').');
			Template::set_message('Gagal membuat file Excel: output tidak valid.', 'error');
			redirect(SITE_AREA . '/reports/report_excel');
			return;
		}

		// Simpan metadata ke tabel report (Excel)
		$meta = array(
			'tipe_report'      => 'excel',
			'periode'          => $periode,
			'tgl_mulai'        => ($tgl_mulai !== '') ? $tgl_mulai : null,
			'tgl_akhir'        => ($tgl_akhir !== '') ? $tgl_akhir : null,
			'jumlah_transaksi' => count($rows),
			'total_nilai'      => $this->report_model->grand_total($rows),
			'nama_file'        => $nama_file,
			'path_file'        => $path_rel,
		);

		$saved = $this->report_model->save_history($meta);
		if ($saved === false) {
			log_message('error', 'Report Excel: file berhasil dibuat tetapi metadata gagal disimpan.');
		}

		// Output ke browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $nama_file . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');
		header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
		readfile($full);
		exit;
	}

	/**
	 * Download Excel dari Riwayat Laporan.
	 *
	 * @param int $id ID histori report.
	 *
	 * @return void
	 */
	public function download_excel($id = 0)
	{
		$id = (int) $id;
		if ($id <= 0) {
			show_404();
			return;
		}

		$row = $this->report_model->find_history($id);
		if (!$row || empty($row->nama_file) || empty($row->path_file)) {
			show_404();
			return;
		}

		$file = basename($row->nama_file);
		$path = APPPATH . '../' . $row->path_file;
		if (!is_file($path)) {
			log_message('error', 'Report Excel: file histori tidak ditemukan: ' . $row->path_file);
			show_404();
			return;
		}

		$this->load->helper('download');
		$data = file_get_contents($path);
		force_download($file, $data);
	}

	/**
	 * Cari laporan Excel terbaru dengan parameter sama (dalam 5 menit terakhir).
	 * Hanya berlaku untuk filter status Semua (''); bila status spesifik dipilih,
	 * file selalu dibuat ulang agar isinya sesuai status tersebut.
	 *
	 * @param string $periode
	 * @param string $tgl_mulai
	 * @param string $tgl_akhir
	 * @param string $status
	 *
	 * @return object|null
	 */
	private function find_recent_excel($periode, $tgl_mulai, $tgl_akhir, $status = '')
	{
		if ($status !== '') {
			return null;
		}

		$this->db->select('id, nama_file, path_file')
			->from('report')
			->where('tipe_report', 'excel')
			->where('periode', $periode)
			->where('tgl_mulai', ($tgl_mulai !== '') ? $tgl_mulai : null)
			->where('tgl_akhir', ($tgl_akhir !== '') ? $tgl_akhir : null)
			->where('created_on >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
			->order_by('id', 'desc')
			->limit(1);

		return $this->db->get()->row();
	}

	/**
	 * Load history data for the view.
	 *
	 * @return void
	 */
	private function load_history()
	{
		$h_periode   = (string) $this->input->get('h_periode');
		$h_tgl_mulai = $this->normalize_tgl($this->input->get('h_tgl_mulai'));
		$h_tgl_akhir = $this->normalize_tgl($this->input->get('h_tgl_akhir'));

		if (!in_array($h_periode, array('all', 'today', 'month', 'custom', ''), true)) {
			$h_periode = 'all';
		}
		if ($h_periode === '') {
			$h_periode = 'all';
		}
		if ($h_periode === 'custom') {
			if ($h_tgl_mulai === '' && $h_tgl_akhir === '') {
				$h_periode = 'all';
			} elseif ($h_tgl_mulai !== '' && $h_tgl_akhir !== '' && $h_tgl_mulai > $h_tgl_akhir) {
				$h_periode = 'all';
			}
		}
		if ($h_periode !== 'custom') {
			$h_tgl_mulai = '';
			$h_tgl_akhir = '';
		}

		Template::set('history_list', $this->report_model->get_history_list(20, $h_periode, $h_tgl_mulai, $h_tgl_akhir, 'excel'));
		Template::set('h_periode', $h_periode);
		Template::set('h_tgl_mulai', $h_tgl_mulai);
		Template::set('h_tgl_akhir', $h_tgl_akhir);
	}

	/**
	 * Normalisasi filter status transaksi.
	 *
	 * @param mixed $status
	 *
	 * @return string 'Diproses'|'Diambil'|'Selesai' atau '' (Semua) bila tidak valid.
	 */
	private function normalize_status($status)
	{
		$status = (string) $status;
		return in_array($status, array('Diproses', 'Diambil', 'Selesai'), true) ? $status : '';
	}

	/**
	 * Normalisasi input tanggal: terima YYYY-MM-DD atau DD-MM-YYYY.
	 *
	 * @param mixed $tgl
	 *
	 * @return string YYYY-MM-DD atau '' bila tidak valid.
	 */
	private function normalize_tgl($tgl)
	{
		$tgl = trim((string) $tgl);
		if ($tgl === '') {
			return '';
		}

		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tgl, $m)) {
			$iso = $m[1] . '-' . $m[2] . '-' . $m[3];
			return (strtotime($iso) !== false) ? $iso : '';
		}

		if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $tgl, $m)) {
			$iso = $m[3] . '-' . $m[2] . '-' . $m[1];
			return (strtotime($iso) !== false) ? $iso : '';
		}

		return '';
	}
}
