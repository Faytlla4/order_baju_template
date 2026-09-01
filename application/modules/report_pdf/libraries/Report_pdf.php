<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Report_pdf — generator PDF (A4 landscape) untuk Laporan Transaksi.
 * Tanpa dependensi eksternal, kompatibel PHP 7.
 *
 * Fitur:
 * - Landscape A4 (842 x 595)
 * - Text wrapping otomatis (multi-line cells)
 * - Row height variabel
 * - Alignment per kolom (left/center/right)
 * - Header tabel berulang di setiap halaman
 * - Summary terpisah dari tabel
 */
class Report_pdf
{
	protected $page_w = 842;
	protected $page_h = 595;
	protected $margin_l = 20;
	protected $margin_r = 20;
	protected $margin_b = 50;
	protected $margin_top = 44;

	protected $title = '';
	protected $subtitle = '';
	protected $headers = array();
	protected $widths = array();
	protected $aligns = array();
	protected $rows = array();
	protected $footers = array();

	protected $out = '';
	protected $offsets = array();

	protected $font_size = 7;
	protected $line_height = 9.5;
	protected $cell_padding = 2;

	public function set_data($title, $subtitle, $headers, $widths, $rows, $footers = array(), $aligns = array())
	{
		$this->title = $title;
		$this->subtitle = $subtitle;
		$this->headers = $headers;
		$this->widths = $widths;
		$this->rows = $rows;
		$this->footers = $footers;
		$this->aligns = $aligns;
	}

	public function build()
	{
		$pages = $this->render_pages();

		$this->offsets = array();
		$this->out = "%PDF-1.4\n";

		$page_refs = '';
		$count = count($pages);
		for ($i = 0; $i < $count; $i++) {
			$page_refs .= ($i + 5) . ' 0 R ';
		}

		$this->obj("<< /Type /Catalog /Pages 2 0 R >>");
		$this->obj("<< /Type /Pages /Kids [{$page_refs}] /Count {$count} >>");
		$this->obj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
		$this->obj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");

		for ($i = 0; $i < $count; $i++) {
			$content_obj = $i + $count + 5;
			$this->obj("<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->page_w} {$this->page_h}] "
				. "/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents {$content_obj} 0 R >>");
		}
		foreach ($pages as $content) {
			$this->obj("<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream");
		}

		$xref = strlen($this->out);
		$this->out .= "xref\n0 " . (count($this->offsets) + 1) . "\n0000000000 65535 f \n";
		foreach ($this->offsets as $o) {
			$this->out .= sprintf("%010d 00000 n \n", $o);
		}
		$this->out .= "trailer\n<< /Size " . (count($this->offsets) + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";

		return $this->out;
	}

	protected function obj($body)
	{
		$this->offsets[] = strlen($this->out);
		$this->out .= count($this->offsets) . " 0 obj\n" . $body . "\nendobj\n";
	}

	protected function right_edge()
	{
		return $this->page_w - $this->margin_r;
	}

	protected function col_x()
	{
		$cx = array();
		$x = $this->margin_l;
		foreach ($this->widths as $w) {
			$cx[] = $x;
			$x += $w;
		}
		return $cx;
	}

	protected function render_pages()
	{
		$pages = array();
		$col_x = $this->col_x();
		$right = $this->right_edge();
		$fs = $this->font_size;
		$lh = $this->line_height;
		$row_h = 16;

		$content = '';
		$y = $this->page_h - $this->margin_top;

		// Title
		$content .= "BT /F2 13 Tf {$this->margin_l} {$y} Td (" . $this->clear($this->title) . ") Tj ET\n";
		$y -= 18;

		// Subtitle (Periode)
		$sub = $this->clear('Periode: ' . $this->subtitle);
		if ($sub !== '') {
			$content .= "BT /F1 9 Tf {$this->margin_l} {$y} Td ({$sub}) Tj ET\n";
		}

		// Dicetak timestamp (right-aligned)
		$wib = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
		$printed = 'Dicetak: ' . $wib->format('d-m-Y H:i');
		$content .= "BT /F1 8 Tf " . ($right - 130) . " {$y} Td (" . $this->clear($printed) . ") Tj ET\n";
		$y -= 20;

		// Top border of table
		$content .= $this->line($this->margin_l, $y, $right, $y);
		$y -= 2;

		if (empty($this->rows)) {
			$content .= "BT /F1 10 Tf {$this->margin_l} " . ($y - 18) . " Td (Tidak ada transaksi pada periode yang dipilih.) Tj ET\n";
			$pages[] = $content;
			return $pages;
		}

		// Header row
		$content = $this->render_header($content, $col_x, $y);
		$y -= $row_h;

		$n = 0;
		foreach ($this->rows as $row) {
			$n++;

			// Calculate row height for this row
			$row_lines = $this->calc_row_lines($row);
			$dyn_h = max($row_h, ($row_lines * $lh) + 4);

			// Page break check
			if ($y - $dyn_h < $this->margin_b) {
				$pages[] = $content;
				$content = '';
				$y = $this->page_h - $this->margin_top;

				$content .= "BT /F1 7 Tf " . ($right - 100) . " {$y} Td (Lanjut...) Tj ET\n";
				$y -= 14;
				$content .= $this->line($this->margin_l, $y, $right, $y);
				$y -= 2;
				$content = $this->render_header($content, $col_x, $y);
				$y -= $row_h;
			}

			$content = $this->render_row($content, $row, $n, $col_x, $y, $dyn_h);
			$y -= $dyn_h;
		}

		// Summary footer
		$y -= 6;
		$content .= $this->line($this->margin_l, $y, $right, $y);
		$y -= 16;
		foreach ($this->footers as $f) {
			$content .= "BT /F2 9 Tf {$this->margin_l} {$y} Td (" . $this->clear($f) . ") Tj ET\n";
			$y -= 14;
		}

		$pages[] = $content;
		return $pages;
	}

	protected function calc_row_lines($row)
	{
		$vals = array_merge(array(''), $row);
		$max_lines = 1;
		$fs = $this->font_size;

		foreach ($this->headers as $idx => $h) {
			$val = isset($vals[$idx]) ? (string) $vals[$idx] : '-';
			$lines = $this->wrap_text($val, $this->widths[$idx], $fs);
			if (count($lines) > $max_lines) {
				$max_lines = count($lines);
			}
		}
		return $max_lines;
	}

	protected function wrap_text($str, $width, $size)
	{
		$str = $this->clear(trim((string) $str));
		if ($str === '') {
			return array('-');
		}

		$char_w = 0.42 * $size;
		$max_chars = max(1, (int) floor(($width - $this->cell_padding * 2) / $char_w));

		if (strlen($str) <= $max_chars) {
			return array($str);
		}

		$words = explode(' ', $str);
		$lines = array();
		$current = '';

		foreach ($words as $word) {
			if ($current === '') {
				$current = $word;
			} elseif (strlen($current) + 1 + strlen($word) <= $max_chars) {
				$current .= ' ' . $word;
			} else {
				$lines[] = $current;
				$current = $word;
			}
		}
		if ($current !== '') {
			$lines[] = $current;
		}

		if (count($lines) > 4) {
			$lines = array_slice($lines, 0, 3);
			$lines[] = '...';
		}

		return $lines;
	}

	protected function align_x($text, $col_width, $col_x_pos, $align)
	{
		$char_w = 0.42 * $this->font_size;
		$text_w = strlen($text) * $char_w;
		$pad = $this->cell_padding;

		switch ($align) {
			case 'center':
				return $col_x_pos + ($col_width - $text_w) / 2;
			case 'right':
				return $col_x_pos + $col_width - $text_w - $pad;
			default:
				return $col_x_pos + $pad;
		}
	}

	protected function render_header($content, $col_x, $y)
	{
		$right = $this->right_edge();
		$fs = $this->font_size;

		$content .= $this->line($this->margin_l, $y, $right, $y);

		foreach ($this->headers as $i => $h) {
			$txt = $this->fit($h, $this->widths[$i], $fs);
			$align = isset($this->aligns[$i]) ? $this->aligns[$i] : 'left';
			$x = $this->align_x($txt, $this->widths[$i], $col_x[$i], $align);
			$content .= "BT /F2 {$fs} Tf 0 g " . sprintf("%.2f", $x) . " " . ($y - 11) . " Td (" . $txt . ") Tj ET\n";
		}

		$content .= $this->line($this->margin_l, $y - 16, $right, $y - 16);
		return $content;
	}

	protected function render_row($content, $row, $n, $col_x, $y, $dyn_h)
	{
		$right = $this->right_edge();
		$fs = $this->font_size;
		$lh = $this->line_height;

		$vals = array_merge(array($n), $row);

		$content .= $this->line($this->margin_l, $y - $dyn_h, $right, $y - $dyn_h);

		foreach ($this->headers as $idx => $h) {
			$val = isset($vals[$idx]) ? $vals[$idx] : '-';

			if ($idx === 7) {
				$val = number_format((float) $val, 0, ',', '.');
			} elseif ($idx === 8 || $idx === 9) {
				$val = 'Rp ' . number_format((float) $val, 0, ',', '.');
			}

			$align = isset($this->aligns[$idx]) ? $this->aligns[$idx] : 'left';
			$lines = $this->wrap_text((string) $val, $this->widths[$idx], $fs);

			$line_y = $y - 11;
			foreach ($lines as $line) {
				$x = $this->align_x($line, $this->widths[$idx], $col_x[$idx], $align);
				$content .= "BT /F1 {$fs} Tf 0 g " . sprintf("%.2f", $x) . " " . sprintf("%.2f", $line_y) . " Td (" . $line . ") Tj ET\n";
				$line_y -= $lh;
			}
		}

		return $content;
	}

	protected function fit($str, $width, $size)
	{
		$str = $this->clear($str);
		$max = max(1, (int) floor(($width - $this->cell_padding * 2) / (0.42 * $size)));
		if (strlen($str) > $max) {
			$str = substr($str, 0, $max - 1) . '~';
		}
		return $str;
	}

	protected function clear($str)
	{
		$str = (string) $str;
		$str = str_replace(array('\\', '(', ')'), array('\\\\', '\(', '\)'), $str);
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
		}
		return utf8_encode($str);
	}

	protected function line($x1, $y1, $x2, $y2)
	{
		return "0.40 w 0 G " . sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $y1, $x2, $y2);
	}
}
