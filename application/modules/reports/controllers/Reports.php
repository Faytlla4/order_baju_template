<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Reports Controller
 *
 * Menampilkan laporan berdasarkan data di tabel order_baju dan report.
 * Export ke Excel menggunakan output CSV/HTML sederhana (tanpa library eksternal).
 */
class Reports extends App_Controller
{
    protected $permissionView   = 'Site.Reports.View';
    protected $permissionExport = 'Site.Reports.Export';

    protected $allowedStatus = array(
        'Draft', 'Diproses', 'Selesai', 'Dibatalkan'
    );

    public function __construct()
    {
        parent::__construct();
        // $this->auth->restrict($this->permissionView);
    }

    /**
     * Ambil data order_baju berdasarkan filter
     */
    private function filtered($from = '', $to = '', $status = '')
    {
        $query = $this->db
            ->select('o.*, j.nama_jenis, u.nama_ukuran, w.nama_warna', false)
            ->from('order_baju o')
            ->join('master_jenis_baju j', 'j.id = o.jenis_baju_id', 'left')
            ->join('master_ukuran u', 'u.id = o.ukuran_id', 'left')
            ->join('master_warna w', 'w.id = o.warna_id', 'left')
            ->order_by('o.tanggal_order', 'DESC')
            ->order_by('o.id', 'DESC');

        if ($from !== '') {
            $query->where('o.tanggal_order >=', $from);
        }

        if ($to !== '') {
            $query->where('o.tanggal_order <=', $to);
        }

        if ($status !== '') {
            $query->where('o.status_order', $status);
        }

        return $query->get()->result_array();
    }

    public function index()
    {
        $from   = (string) ($this->input->get('date_from') ?: '');
        $to     = (string) ($this->input->get('date_to') ?: '');
        $status = (string) ($this->input->get('status') ?: '');

        $rows = $this->filtered($from, $to, $status);

        $totalTransaksi = count($rows);
        $totalNilai = 0;
        foreach ($rows as $row) {
            $totalNilai += (float) $row['total_harga'];
        }

        // Ambil riwayat report yang sudah di-generate
        $reports = array();
        if ($this->db->table_exists('report')) {
            $reports = $this->db->order_by('id', 'DESC')->limit(20)->get('report')->result_array();
        }

        Template::set('preview_rows', $rows);
        Template::set('reports', $reports);
        Template::set('total_transaksi', $totalTransaksi);
        Template::set('total_nilai', $totalNilai);
        Template::set('filter_from', $from);
        Template::set('filter_to', $to);
        Template::set('filter_status', $status);
        Template::set('status_list', $this->allowedStatus);
        Template::set('toolbar_title', 'Laporan Order Baju');
        Template::render();
    }

    /**
     * Export ke Excel (CSV UTF-8 yang dapat dibuka di Excel)
     */
    public function exportExcel()
    {
        $from   = $this->input->get('date_from') ?: $this->input->post('date_from');
        $to     = $this->input->get('date_to')   ?: $this->input->post('date_to');
        $status = $this->input->get('status')    ?: $this->input->post('status');

        $rows = $this->filtered($from, $to, $status);

        $filename = 'laporan_order_baju_' . date('Ymd_His') . '.csv';

        // Simpan riwayat ke tabel report
        $periode = ($from ?: '-') . ' sd ' . ($to ?: '-');
        $this->db->insert('report', array(
            'periode'          => $periode,
            'tgl_mulai'        => $from ?: null,
            'tgl_akhir'        => $to ?: null,
            'jumlah_transaksi' => count($rows),
            'total_nilai'      => array_sum(array_column($rows, 'total_harga')),
            'created_on'       => date('Y-m-d H:i:s'),
            'tipe_report'      => 'excel',
            'nama_file'        => $filename,
            'path_file'        => '',
        ));

        // Buat output CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // BOM UTF-8 agar Excel tidak salah encoding
        fwrite($out, "\xEF\xBB\xBF");

        // Header kolom
        fputcsv($out, array(
            'No', 'Kode Order', 'Nama Customer', 'Produk',
            'Jenis Baju', 'Ukuran', 'Warna',
            'Jumlah', 'Harga Satuan', 'Total Harga',
            'Status Order', 'Tanggal Order'
        ));

        $no = 1;
        foreach ($rows as $r) {
            fputcsv($out, array(
                $no++,
                $r['kode_order'],
                $r['nama_customer'],
                $r['produk'],
                $r['nama_jenis'] ?? '-',
                $r['nama_ukuran'] ?? '-',
                $r['nama_warna'] ?? '-',
                $r['jumlah'],
                $r['harga'],
                $r['total_harga'],
                $r['status_order'],
                $r['tanggal_order'],
            ));
        }

        fclose($out);
        exit;
    }

    /**
     * Print view untuk laporan (dapat disimpan sebagai PDF dari browser)
     */
    public function printView()
    {
        $from   = $this->input->get('date_from');
        $to     = $this->input->get('date_to');
        $status = $this->input->get('status');

        $rows = $this->filtered($from, $to, $status);

        $totalNilai = array_sum(array_column($rows, 'total_harga'));

        $html = '<!doctype html><html><head><meta charset="utf-8">'
            . '<title>Laporan Order Baju</title>'
            . '<style>'
            . 'body{font-family:Arial,sans-serif;margin:30px;color:#222}'
            . 'h1{font-size:20px;margin-bottom:6px}'
            . '.meta{margin-bottom:14px;font-size:13px;color:#555}'
            . 'table{width:100%;border-collapse:collapse;margin-top:12px;font-size:12px}'
            . 'th,td{border:1px solid #aaa;padding:6px;text-align:left}'
            . 'th{background:#e9ecef}'
            . '.text-right{text-align:right}'
            . '.btn-print{margin-bottom:14px;padding:8px 14px;cursor:pointer}'
            . '@media print{.btn-print{display:none}}'
            . '</style></head><body>'
            . '<button class="btn-print" onclick="window.print()">&#128438; Print / Simpan PDF</button>'
            . '<h1>Laporan Order Baju</h1>'
            . '<div class="meta">'
            . 'Periode: <strong>' . ($from ?: '-') . '</strong> s/d <strong>' . ($to ?: '-') . '</strong>'
            . ' &nbsp;|&nbsp; Status: <strong>' . ($status ?: 'Semua') . '</strong>'
            . ' &nbsp;|&nbsp; Dicetak: <strong>' . date('d-m-Y H:i') . '</strong>'
            . '</div>'
            . '<p>Total Order: <strong>' . count($rows) . '</strong>&nbsp;&nbsp;'
            . 'Total Nilai: <strong>Rp ' . number_format($totalNilai, 0, ',', '.') . '</strong></p>'
            . '<table><thead><tr>'
            . '<th>No</th><th>Kode Order</th><th>Customer</th><th>Produk</th>'
            . '<th>Jenis</th><th>Ukuran</th><th>Warna</th>'
            . '<th>Qty</th><th class="text-right">Harga</th><th class="text-right">Total</th>'
            . '<th>Status</th><th>Tanggal</th>'
            . '</tr></thead><tbody>';

        $no = 1;
        foreach ($rows as $r) {
            $html .= '<tr>'
                . '<td>' . $no++ . '</td>'
                . '<td>' . html_escape($r['kode_order']) . '</td>'
                . '<td>' . html_escape($r['nama_customer']) . '</td>'
                . '<td>' . html_escape($r['produk']) . '</td>'
                . '<td>' . html_escape($r['nama_jenis'] ?? '-') . '</td>'
                . '<td>' . html_escape($r['nama_ukuran'] ?? '-') . '</td>'
                . '<td>' . html_escape($r['nama_warna'] ?? '-') . '</td>'
                . '<td class="text-right">' . (int)$r['jumlah'] . '</td>'
                . '<td class="text-right">Rp' . number_format($r['harga'], 0, ',', '.') . '</td>'
                . '<td class="text-right">Rp' . number_format($r['total_harga'], 0, ',', '.') . '</td>'
                . '<td>' . html_escape($r['status_order']) . '</td>'
                . '<td>' . date('d/m/Y', strtotime($r['tanggal_order'])) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        echo $html;
        exit;
    }
}
