<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

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
 * Content context controller
 *
 * The controller which displays the homepage of the Content context in Bonfire site.
 *
 * @package    Bonfire
 * @subpackage Controllers
 * @category   Controllers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/file_helpers.html
 *
 */
class Content extends App_Controller
{

	/**
	 * Controller constructor sets the Title and Permissions
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		Template::set('toolbar_title', 'Content');

		$this->auth->restrict('Site.Content.View');
	} //end __construct()

	//--------------------------------------------------------------------

	/**
	 * Displays the initial page of the Content context
	 *
	 * @return void
	 */
	public function index()
	{
		Template::set_view('admin/content/index');
		Template::render();
	} //end index()

	//--------------------------------------------------------------------

	public function order_baju()
	{
		$query = $this->db->select('order_baju.*, master_jenis_baju.nama_jenis, master_ukuran.nama_ukuran, master_warna.nama_warna')
			->from('order_baju')
			->join('master_jenis_baju', 'master_jenis_baju.id = order_baju.jenis_baju_id', 'left')
			->join('master_ukuran', 'master_ukuran.id = order_baju.ukuran_id', 'left')
			->join('master_warna', 'master_warna.id = order_baju.warna_id', 'left')
			->order_by('order_baju.id', 'DESC')
			->get();

		Template::set('orders', $query->result());
		Template::set('toolbar_title', 'Order Baju');
		Template::set_view('admin/content/order_baju');
		Template::render();
	} //end order_baju()

	//--------------------------------------------------------------------

} //end class