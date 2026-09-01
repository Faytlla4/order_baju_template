<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Admin Reports controller
 *
 * The base controller which displays the homepage of the Admin Reports context in the Bonfire app.
 *
 * @package    Bonfire
 * @subpackage Controllers
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class Reports extends Admin_Controller
{


	/**
	 * Controller constructor sets the Title and Permissions
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		Template::set('toolbar_title', 'Reports');

		$this->auth->restrict('Site.Reports.View');
	}//end __construct()

	//--------------------------------------------------------------------

	/**
	 * Displays the Reports context homepage
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set_view('admin/reports/index');
		Template::render();
	}//end index()

	//--------------------------------------------------------------------

	public function report_pdf()
	{
		$this->load->database();

		$tgl_mulai = $this->input->get('tgl_mulai');
		$tgl_akhir = $this->input->get('tgl_akhir');

		$this->db->select('transaksi.*, order_baju.kode_order, order_baju.nama_customer, order_baju.produk, master_jenis_baju.nama_jenis, master_ukuran.nama_ukuran, master_warna.nama_warna')
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'left')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left');

		if ($tgl_mulai) {
			$this->db->where('transaksi.created_on >=', $tgl_mulai);
		}
		if ($tgl_akhir) {
			$this->db->where('transaksi.created_on <=', $tgl_akhir . ' 23:59:59');
		}

		$rows = $this->db->order_by('transaksi.id', 'DESC')->get()->result();

		Template::set('rows', $rows);
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('toolbar_title', 'Laporan Transaksi PDF');
		Template::set_view('admin/reports/report_pdf');
		Template::render();
	}//end report_pdf()

	//--------------------------------------------------------------------

	public function report_excel()
	{
		$this->load->database();

		$tgl_mulai = $this->input->get('tgl_mulai');
		$tgl_akhir = $this->input->get('tgl_akhir');

		$this->db->select('transaksi.*, order_baju.kode_order, order_baju.nama_customer, order_baju.produk, master_jenis_baju.nama_jenis, master_ukuran.nama_ukuran, master_warna.nama_warna')
			->from('transaksi')
			->join('order_baju', 'order_baju.id = transaksi.order_baju_id', 'left')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left');

		if ($tgl_mulai) {
			$this->db->where('transaksi.created_on >=', $tgl_mulai);
		}
		if ($tgl_akhir) {
			$this->db->where('transaksi.created_on <=', $tgl_akhir . ' 23:59:59');
		}

		$rows = $this->db->order_by('transaksi.id', 'DESC')->get()->result();

		Template::set('rows', $rows);
		Template::set('tgl_mulai', $tgl_mulai);
		Template::set('tgl_akhir', $tgl_akhir);
		Template::set('toolbar_title', 'Laporan Transaksi Excel');
		Template::set_view('admin/reports/report_excel');
		Template::render();
	}//end report_excel()

	//--------------------------------------------------------------------


}//end class