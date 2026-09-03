<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->auth->restrict();
	}

	public function index()
	{
		// Summary cards
		$total_order   = $this->db->count_all('order_baju');
		$total_customer = $this->db->select('COUNT(DISTINCT nama_customer) as total')->get('order_baju')->row()->total;
		$total_transaksi = $this->db->count_all('transaksi');

		$status_diproses = $this->db->where('status_order', 'Diproses')->count_all_results('order_baju');
		$status_diambil  = $this->db->where('status_order', 'Diambil')->count_all_results('order_baju');
		$status_selesai  = $this->db->where('status_order', 'Selesai')->count_all_results('order_baju');

		$total_pendapatan = $this->db->select('COALESCE(SUM(total_harga), 0) as total')->get('order_baju')->row()->total;

		Template::set('total_order', $total_order);
		Template::set('total_customer', $total_customer);
		Template::set('total_transaksi', $total_transaksi);
		Template::set('status_diproses', $status_diproses);
		Template::set('status_diambil', $status_diambil);
		Template::set('status_selesai', $status_selesai);
		Template::set('total_pendapatan', $total_pendapatan);

		// Recent orders (last 5)
		$recent_orders = $this->db->select('kode_order, nama_customer, total_harga, status_order, tanggal_order')
			->order_by('id', 'desc')
			->limit(5)
			->get('order_baju')
			->result();
		Template::set('recent_orders', $recent_orders);

		// Customer data (top 10 by order count)
		$customers = $this->db->select('nama_customer, COUNT(*) as order_count, SUM(total_harga) as total_spend')
			->group_by('nama_customer')
			->order_by('order_count', 'desc')
			->limit(10)
			->get('order_baju')
			->result();
		Template::set('customers', $customers);

		Template::set('toolbar_title', 'Dashboard');
		Template::render();
	}
}
