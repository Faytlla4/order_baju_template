<?php defined('BASEPATH') || exit('No direct script access allowed');

class Reports extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('backup_report/backup_report_model');
		$this->load->library('report_pdf/report_pdf');
	}

	// ==================== LAPORAN DOKUMEN ====================

	public function laporan_dokumen_pdf()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		Template::set('toolbar_title', 'Laporan Dokumen PDF');
		Template::set('page_type', 'pdf');
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('rows', $this->backup_report_model->get_document_history($tgl_mulai, $tgl_akhir));
		Template::set_view('reports/laporan_dokumen');
		Template::render();
	}

	public function laporan_dokumen_excel()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		Template::set('toolbar_title', 'Laporan Dokumen Excel');
		Template::set('page_type', 'excel');
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('rows', $this->backup_report_model->get_document_history($tgl_mulai, $tgl_akhir));
		Template::set_view('reports/laporan_dokumen');
		Template::render();
	}

	public function filter_dokumen()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		$rows = $this->backup_report_model->get_document_history($tgl_mulai, $tgl_akhir);

		$html = $this->load->view('reports/_data_dokumen', array(
			'rows'      => $rows,
			'tgl_mulai' => $tgl_mulai,
			'tgl_akhir' => $tgl_akhir,
		), true);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('ok' => true, 'html' => $html)));
	}

	public function cetak_pdf_dokumen()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$rows = $this->backup_report_model->get_document_history($tgl_mulai, $tgl_akhir);

		$label    = $this->_periode_label($tgl_mulai, $tgl_akhir);
		$headers  = array('No', 'Tanggal/Waktu', 'Nama File', 'Jumlah Dokumen', 'Ukuran', 'Filter');
		$widths   = array(25, 100, 180, 60, 70, 150);
		$aligns   = array('center', 'center', 'left', 'center', 'right', 'left');

		$data_rows = array();
		$i = 1;
		foreach ($rows as $r) {
			$data_rows[] = array(
				$i++,
				$r->created_on_str,
				$r->file_name,
				$r->jumlah_dokumen,
				$this->_format_size($r->file_size),
				$r->filter_used,
			);
		}

		$footers = array();
		if (!empty($rows)) {
			$footers[] = 'Total Backup: ' . count($rows) . ' kali';
		}

		$this->report_pdf->set_data('LAPORAN DOKUMEN', $label, $headers, $widths, $data_rows, $footers, $aligns);
		$pdf = $this->report_pdf->build();

		if (!is_string($pdf) || $pdf === '' || strncmp($pdf, '%PDF', 4) !== 0) {
			Template::set_message('Gagal membuat PDF.', 'error');
			redirect(SITE_AREA . '/laporan-dokumen');
			return;
		}

		$this->_log_laporan('Dokumen', 'PDF', 'laporan_dokumen.pdf', $tgl_mulai, $tgl_akhir, count($rows), strlen($pdf));

		$this->output->set_header('Content-Type: application/pdf');
		$this->output->set_header('Content-Disposition: inline; filename="laporan_dokumen.pdf"');
		$this->output->set_output($pdf);
	}

	public function cetak_excel_dokumen()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$rows = $this->backup_report_model->get_document_history($tgl_mulai, $tgl_akhir);
		$label = $this->_periode_label($tgl_mulai, $tgl_akhir);

		require_once FCPATH . '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php';

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:F1');
		$sheet->setCellValue('A1', 'LAPORAN DOKUMEN');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:F2');
		$sheet->setCellValue('A2', $label);
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$headers = array('No', 'Tanggal/Waktu', 'Nama File', 'Jumlah Dokumen', 'Ukuran (MB)', 'Filter');
		$col = 'A';
		foreach ($headers as $h) {
			$sheet->setCellValue($col . '4', $h);
			$sheet->getStyle($col . '4')->getFont()->setBold(true);
			$sheet->getStyle($col . '4')->getFill()->applyFromArray(array('fillType' => 'solid', 'startColor' => array('rgb' => '4E73DF')));
			$sheet->getStyle($col . '4')->getFont()->getColor()->setRGB('FFFFFF');
			$col++;
		}

		$rowNum = 5;
		$i = 1;
		foreach ($rows as $r) {
			$sheet->setCellValue('A' . $rowNum, $i++);
			$sheet->setCellValue('B' . $rowNum, $r->created_on_str);
			$sheet->setCellValue('C' . $rowNum, $r->file_name);
			$sheet->setCellValue('D' . $rowNum, $r->jumlah_dokumen);
			$sheet->setCellValue('E' . $rowNum, round($r->file_size / 1048576, 2));
			$sheet->setCellValue('F' . $rowNum, $r->filter_used);
			$rowNum++;
		}

		foreach (range('A', 'F') as $c) {
			$sheet->getColumnDimension($c)->setAutoSize(true);
		}

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$filename = FCPATH . 'laporan_dokumen.xlsx';
		$writer->save($filename);
		$fileSize = filesize($filename);

		$this->_log_laporan('Dokumen', 'Excel', 'laporan_dokumen.xlsx', $tgl_mulai, $tgl_akhir, count($rows), $fileSize);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="laporan_dokumen.xlsx"');
		header('Content-Length: ' . $fileSize);
		readfile($filename);
		unlink($filename);
		exit;
	}

	// ==================== LAPORAN DATABASE ====================

	public function laporan_database_pdf()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		Template::set('toolbar_title', 'Laporan Database PDF');
		Template::set('page_type', 'pdf');
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('rows', $this->backup_report_model->get_database_history($tgl_mulai, $tgl_akhir));
		Template::set_view('reports/laporan_database');
		Template::render();
	}

	public function laporan_database_excel()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		Template::set('toolbar_title', 'Laporan Database Excel');
		Template::set('page_type', 'excel');
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('rows', $this->backup_report_model->get_database_history($tgl_mulai, $tgl_akhir));
		Template::set_view('reports/laporan_database');
		Template::render();
	}

	public function filter_database()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));

		$rows = $this->backup_report_model->get_database_history($tgl_mulai, $tgl_akhir);

		$html = $this->load->view('reports/_data_database', array(
			'rows'      => $rows,
			'tgl_mulai' => $tgl_mulai,
			'tgl_akhir' => $tgl_akhir,
		), true);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('ok' => true, 'html' => $html)));
	}

	public function cetak_pdf_database()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$rows = $this->backup_report_model->get_database_history($tgl_mulai, $tgl_akhir);

		$label    = $this->_periode_label($tgl_mulai, $tgl_akhir);
		$headers  = array('No', 'Tanggal/Waktu', 'Nama File', 'Status', 'Ukuran');
		$widths   = array(25, 120, 220, 80, 100);
		$aligns   = array('center', 'center', 'left', 'center', 'right');

		$data_rows = array();
		$i = 1;
		foreach ($rows as $r) {
			$data_rows[] = array(
				$i++,
				$r->created_on_str,
				$r->file_name,
				$r->status,
				$this->_format_size($r->file_size),
			);
		}

		$footers = array();
		if (!empty($rows)) {
			$footers[] = 'Total Backup: ' . count($rows) . ' kali';
		}

		$this->report_pdf->set_data('LAPORAN DATABASE', $label, $headers, $widths, $data_rows, $footers, $aligns);
		$pdf = $this->report_pdf->build();

		if (!is_string($pdf) || $pdf === '' || strncmp($pdf, '%PDF', 4) !== 0) {
			Template::set_message('Gagal membuat PDF.', 'error');
			redirect(SITE_AREA . '/laporan-database');
			return;
		}

		$this->_log_laporan('Database', 'PDF', 'laporan_database.pdf', $tgl_mulai, $tgl_akhir, count($rows), strlen($pdf));

		$this->output->set_header('Content-Type: application/pdf');
		$this->output->set_header('Content-Disposition: inline; filename="laporan_database.pdf"');
		$this->output->set_output($pdf);
	}

	public function cetak_excel_database()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$rows = $this->backup_report_model->get_database_history($tgl_mulai, $tgl_akhir);
		$label = $this->_periode_label($tgl_mulai, $tgl_akhir);

		require_once FCPATH . '../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php';

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:E1');
		$sheet->setCellValue('A1', 'LAPORAN DATABASE');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$sheet->mergeCells('A2:E2');
		$sheet->setCellValue('A2', $label);
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		$headers = array('No', 'Tanggal/Waktu', 'Nama File', 'Status', 'Ukuran (MB)');
		$col = 'A';
		foreach ($headers as $h) {
			$sheet->setCellValue($col . '4', $h);
			$sheet->getStyle($col . '4')->getFont()->setBold(true);
			$sheet->getStyle($col . '4')->getFill()->applyFromArray(array('fillType' => 'solid', 'startColor' => array('rgb' => '4E73DF')));
			$sheet->getStyle($col . '4')->getFont()->getColor()->setRGB('FFFFFF');
			$col++;
		}

		$rowNum = 5;
		$i = 1;
		foreach ($rows as $r) {
			$sheet->setCellValue('A' . $rowNum, $i++);
			$sheet->setCellValue('B' . $rowNum, $r->created_on_str);
			$sheet->setCellValue('C' . $rowNum, $r->file_name);
			$sheet->setCellValue('D' . $rowNum, $r->status);
			$sheet->setCellValue('E' . $rowNum, round($r->file_size / 1048576, 2));
			$rowNum++;
		}

		foreach (range('A', 'E') as $c) {
			$sheet->getColumnDimension($c)->setAutoSize(true);
		}

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$filename = FCPATH . 'laporan_database.xlsx';
		$writer->save($filename);
		$fileSize = filesize($filename);

		$this->_log_laporan('Database', 'Excel', 'laporan_database.xlsx', $tgl_mulai, $tgl_akhir, count($rows), $fileSize);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="laporan_database.xlsx"');
		header('Content-Length: ' . $fileSize);
		readfile($filename);
		unlink($filename);
		exit;
	}

	// ==================== RIWAYAT CETAK LAPORAN ====================

	public function riwayat_laporan()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$jenis     = $this->input->get('jenis');

		Template::set('toolbar_title', 'Riwayat Cetak Laporan');
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('jenis', $jenis);
		Template::set('rows', $this->backup_report_model->get_laporan_history($tgl_mulai, $tgl_akhir, $jenis));
		Template::set_view('reports/riwayat_laporan');
		Template::render();
	}

	public function filter_riwayat()
	{
		$tgl_mulai = $this->normalize_tgl($this->input->get('tgl_mulai'));
		$tgl_akhir = $this->normalize_tgl($this->input->get('tgl_akhir'));
		$jenis     = $this->input->get('jenis');

		$rows = $this->backup_report_model->get_laporan_history($tgl_mulai, $tgl_akhir, $jenis);

		$html = $this->load->view('reports/_data_history', array(
			'rows'      => $rows,
			'tgl_mulai' => $tgl_mulai,
			'tgl_akhir' => $tgl_akhir,
			'jenis'     => $jenis,
		), true);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('ok' => true, 'html' => $html)));
	}

	// ==================== HELPERS ====================

	private function _log_laporan($report_type, $export_type, $filename, $filter_mulai, $filter_akhir, $record_count, $file_size)
	{
		$user_id = null;
		if (isset($this->auth_lib) && method_exists($this->auth_lib, 'user_id')) {
			$user_id = $this->auth_lib->user_id();
		}

		$this->db->insert('laporan_history', array(
			'report_type'  => $report_type,
			'export_type'  => $export_type,
			'filename'     => $filename,
			'filter_mulai' => ($filter_mulai !== '') ? $filter_mulai : null,
			'filter_akhir' => ($filter_akhir !== '') ? $filter_akhir : null,
			'record_count' => $record_count,
			'file_size'    => $file_size,
			'created_by'   => $user_id,
			'created_on'   => date('Y-m-d H:i:s'),
		));
	}

	private function _periode_label($tgl_mulai, $tgl_akhir)
	{
		if ($tgl_mulai === '' && $tgl_akhir === '') {
			return 'Semua Periode';
		}
		$from = $tgl_mulai !== '' ? date('d-m-Y', strtotime($tgl_mulai)) : '...';
		$to   = $tgl_akhir !== '' ? date('d-m-Y', strtotime($tgl_akhir)) : '...';
		return $from . ' s/d ' . $to;
	}

	private function _format_size($bytes)
	{
		if ($bytes >= 1048576) {
			return round($bytes / 1048576, 2) . ' MB';
		}
		if ($bytes >= 1024) {
			return round($bytes / 1024, 1) . ' KB';
		}
		return $bytes . ' B';
	}

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
