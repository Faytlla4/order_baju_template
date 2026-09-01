<?php defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class Report_excel
{
	private $spreadsheet;

	public function __construct()
	{
		$this->spreadsheet = new Spreadsheet();
	}

	/**
	 * Build Excel dari data laporan.
	 *
	 * @param string $title
	 * @param string $subtitle   Periode label.
	 * @param array  $headers    Header kolom.
	 * @param array  $widths     Lebar kolom.
	 * @param array  $data_rows  Data baris.
	 * @param array  $footers    Footer summary.
	 * @param array  $aligns     Alignment per kolom ('left','center','right').
	 *
	 * @return Spreadsheet
	 */
	public function set_data($title, $subtitle, $headers, $widths, $data_rows, $footers = array(), $aligns = array())
	{
		$sheet = $this->spreadsheet->getActiveSheet();
		$sheet->setTitle('Laporan Transaksi');

		$colCount = count($headers);
		$lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

		// === Title ===
		$sheet->mergeCells("A1:{$lastColLetter}1");
		$sheet->setCellValue('A1', $title);
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		// === Subtitle (periode) ===
		$sheet->mergeCells("A2:{$lastColLetter}2");
		$sheet->setCellValue('A2', 'Periode: ' . $subtitle);
		$sheet->getStyle('A2')->getFont()->setSize(11);
		$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

		// === Dicetak timestamp ===
		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$sheet->mergeCells("A3:{$lastColLetter}3");
		$sheet->setCellValue('A3', 'Dicetak: ' . $wib->format('d-m-Y H:i'));
		$sheet->getStyle('A3')->getFont()->setSize(9)->setItalic(true);
		$sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

		// === Header row ===
		$headerRow = 5;
		for ($i = 0; $i < $colCount; $i++) {
			$cell = $sheet->getCellByColumnAndRow($i + 1, $headerRow);
			$cell->setValue($headers[$i]);
			$cell->getStyle()->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFF'));
			$cell->getStyle()->getFill()->setFillType('solid')->setStartColor(new Color('4472C4'));
			$cell->getStyle()->getAlignment()->setHorizontal('center');
			$cell->getStyle()->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
			$cell->getStyle()->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
			$cell->getStyle()->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
			$cell->getStyle()->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
		}

		// === Data rows ===
		$dataRow = $headerRow + 1;
		foreach ($data_rows as $rowIdx => $row) {
			$isEven = ($rowIdx % 2 === 0);
			for ($i = 0; $i < $colCount; $i++) {
				$cell = $sheet->getCellByColumnAndRow($i + 1, $dataRow);
				$cell->setValue($row[$i]);
				$align = isset($aligns[$i]) ? $aligns[$i] : 'left';
				$cell->getStyle()->getAlignment()->setHorizontal($align);
				$cell->getStyle()->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
				$cell->getStyle()->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$cell->getStyle()->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

				if ($isEven) {
					$cell->getStyle()->getFill()->setFillType('solid')->setStartColor(new Color('D9E2F3'));
				}

				// Format Rupiah for Harga (index 8) and Total (index 9)
				if ($i === 8 || $i === 9) {
					$cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
				}
			}
			$dataRow++;
		}

		// === Footer / Summary ===
		if (!empty($footers)) {
			$dataRow++; // blank row
			foreach ($footers as $footerText) {
				$sheet->mergeCells("A{$dataRow}:{$lastColLetter}{$dataRow}");
				$sheet->setCellValue("A{$dataRow}", $footerText);
				$sheet->getStyle("A{$dataRow}")->getFont()->setBold(true)->setSize(11);
				$sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal('right');
				$dataRow++;
			}
		}

		// === Auto-width columns ===
		for ($i = 0; $i < $colCount; $i++) {
			$colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
			if (isset($widths[$i]) && $widths[$i] > 0) {
				$sheet->getColumnDimension($colLetter)->setWidth($widths[$i] / 5);
			} else {
				$sheet->getColumnDimension($colLetter)->setAutoSize(true);
			}
		}

		// === Freeze header row ===
		$sheet->freezePane("A" . ($headerRow + 1));

		return $this->spreadsheet;
	}

	/**
	 * Simpan Excel ke file.
	 *
	 * @param string $fullPath Path lengkap termasuk nama file.
	 *
	 * @return bool
	 */
	public function save($fullPath)
	{
		$writer = new Xlsx($this->spreadsheet);
		$dir = dirname($fullPath);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		$writer->save($fullPath);
		return is_file($fullPath) && filesize($fullPath) > 0;
	}

	/**
	 * Output Excel ke browser (inline/download).
	 *
	 * @param string $filename Nama file tanpa ekstensi.
	 *
	 * @return void
	 */
	public function output($filename = 'laporan')
	{
		$writer = new Xlsx($this->spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');
		header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

		$writer->save('php://output');
	}
}
