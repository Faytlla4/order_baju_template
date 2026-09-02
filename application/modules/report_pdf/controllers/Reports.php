<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Reports controller — Laporan Transaksi PDF.
 *
 * Read-only: mengambil data dari tabel transaksi (JOIN order_baju + master),
 * filter periode (Semua/Hari Ini/Bulan Ini/Custom) dan cetak PDF.
 */
class Reports extends App_Controller
{
	protected $permissionView = 'Site.Reports.View';

	public function __construct()
	{
		parent::__construct();

		$this->auth->restrict($this->permissionView);
		$this->load->model('report_pdf/report_model');
		$this->load->library('report_pdf/report_pdf');
		$this->form_validation->set_error_delimiters("<span class='error'>", "</span>");

		Template::set('toolbar_title', 'Laporan Transaksi PDF');
	}

	/**
	 * Halaman laporan PDF + filter.
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
	 * Cetak PDF laporan (mengikuti filter).
	 *
	 * @return void
	 */
	public function pdf()
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
			log_message('error', 'Report PDF: filter tidak valid (periode=' . $periode . ', mulai=' . $tgl_mulai . ', akhir=' . $tgl_akhir . ').');
			Template::set_message('Filter tanggal tidak valid. Periksa kembali Tanggal Mulai dan Tanggal Akhir.', 'error');
			redirect(SITE_AREA . '/reports/report_pdf');
			return;
		}

		$rows = $this->report_model->get_report($periode, $tgl_mulai, $tgl_akhir, $status);

		$headers = array('No', 'Kode', 'Customer', 'Produk', 'Jenis', 'Ukuran', 'Warna', 'Jumlah', 'Harga', 'Total', 'Status', 'Tanggal');
		$widths  = array(25, 85, 90, 80, 70, 35, 50, 40, 75, 80, 55, 75);
		$aligns  = array('center', 'left', 'left', 'left', 'left', 'center', 'left', 'center', 'right', 'right', 'center', 'center');

		$data_rows = array();
		foreach ($rows as $r) {
			$data_rows[] = array(
				$r->kode_order,
				$r->nama_customer,
				$r->produk,
				$r->jenis_nama,
				$r->ukuran_nama,
				$r->warna_nama,
				$r->jumlah,
				$r->harga,
				$r->total_harga,
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

		$this->report_pdf->set_data('LAPORAN TRANSAKSI ORDER BAJU', $label, $headers, $widths, $data_rows, $footers, $aligns);
		$pdf = $this->report_pdf->build();

		if (!is_string($pdf) || $pdf === '' || strncmp($pdf, '%PDF', 4) !== 0) {
			log_message('error', 'Report PDF: output bukan PDF yang valid (len=' . strlen((string) $pdf) . ').');
			Template::set_message('Gagal membuat PDF: output tidak valid.', 'error');
			redirect(SITE_AREA . '/reports/report_pdf');
			return;
		}

		$nama_file = $this->simpan_pdf($pdf);

		if ($nama_file === false) {
			log_message('error', 'Report PDF: gagal menyimpan file PDF ke assets/dokumen/report/.');
			Template::set_message('Gagal menyimpan file PDF di server.', 'error');
			redirect(SITE_AREA . '/reports/report_pdf');
			return;
		}

		$path_rel = 'public/assets/dokumen/report/' . $nama_file;

		$meta = array(
			'tipe_report'      => 'pdf',
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
			log_message('error', 'Report PDF: PDF berhasil dibuat tetapi metadata gagal disimpan ke tabel report.');
		}

		// Redirect ke view/{id} (READ ONLY) supaya buka/download PDF yang
		// dirender oleh browser viewer tidak membuat history baru.
		if ($saved !== false) {
			redirect(SITE_AREA . '/reports/report_pdf/view/' . (int) $saved);
			return;
		}

		// Fallback: history gagal disimpan, tetap tampilkan PDF inline.
		$this->output->set_header('Content-Type: application/pdf');
		$this->output->set_header('Content-Disposition: inline; filename="' . $nama_file . '"');
		$this->output->set_output($pdf);
	}

	/**
	 * Download / buka PDF dari Riwayat Laporan.
	 *
	 * @param int $id ID histori report.
	 *
	 * @return void
	 */
	public function download($id = 0)
	{
		$id = (int) $id;
		if ($id <= 0) {
			show_404();
			return;
		}

		$row = $this->report_model->find_history($id);
		if (!$row || $row->nama_file === '' || $row->path_file === '') {
			show_404();
			return;
		}

		$file = basename($row->nama_file);
		$path = APPPATH . '../' . $row->path_file;
		if (!is_file($path)) {
			log_message('error', 'Report PDF: file histori tidak ditemukan: ' . $row->path_file);
			show_404();
			return;
		}

		$this->load->helper('download');
		$data = file_get_contents($path);
		force_download($file, $data);
	}

	/**
	 * Lihat PDF dari riwayat tanpa membuat history baru.
	 *
	 * @param int $id ID histori report.
	 *
	 * @return void
	 */
	public function view($id = 0)
	{
		$id = (int) $id;
		if ($id <= 0) {
			show_404();
			return;
		}

		$row = $this->report_model->find_history($id);
		if (!$row || $row->nama_file === '' || $row->path_file === '') {
			show_404();
			return;
		}

		$path = APPPATH . '../' . $row->path_file;
		if (!is_file($path)) {
			log_message('error', 'Report PDF view: file tidak ditemukan: ' . $row->path_file);
			show_404();
			return;
		}

		$pdf = file_get_contents($path);
		$this->output->set_header('Content-Type: application/pdf');
		$this->output->set_header('Content-Disposition: inline; filename="' . basename($row->nama_file) . '"');
		$this->output->set_output($pdf);
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

		Template::set('history_list', $this->report_model->get_history_list(20, $h_periode, $h_tgl_mulai, $h_tgl_akhir, 'pdf'));
		Template::set('h_periode', $h_periode);
		Template::set('h_tgl_mulai', $h_tgl_mulai);
		Template::set('h_tgl_akhir', $h_tgl_akhir);
	}

	/**
	 * Simpan binary PDF ke public/assets/dokumen/report/ dengan nama unik.
	 * PDF dan Excel report TIDAK dipisah ke sub-folder terpisah.
	 *
	 * @param string $pdf Binary PDF.
	 *
	 * @return string|false Nama file unik, atau false bila gagal.
	 */
	private function simpan_pdf($pdf)
	{
		$dir = FCPATH . 'assets/dokumen/report/';
		if (!is_dir($dir)) {
			if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
				log_message('error', 'Report PDF: tidak dapat membuat direktori ' . $dir);
				return false;
			}
		}

		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$nama_file = 'report_transaksi_' . $wib->format('Ymd_His') . '_' . substr(uniqid('', true), -5) . '.pdf';
		$full = $dir . $nama_file;

		$written = @file_put_contents($full, $pdf);
		if ($written === false || $written <= 0 || !is_file($full)) {
			log_message('error', 'Report PDF: file_put_contents gagal untuk ' . $full);
			return false;
		}

		$head = file_get_contents($full, false, null, 0, 4);
		if ($head === false || strncmp($head, '%PDF', 4) !== 0) {
			@unlink($full);
			log_message('error', 'Report PDF: file hasil simpan tidak valid (' . $full . ').');
			return false;
		}

		return $nama_file;
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
