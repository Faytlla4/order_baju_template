<?php defined('BASEPATH') || exit('No direct script access allowed');

$config['module_config'] = array(
	'description'	=> 'Modul Transaksi Order Baju',
	'name'		    => 'Transaksi',
	'version'		=> '0.0.1',
	'author'		=> 'admin',
);

// Modul transaksi memakai permission yang sama dengan Content (Order_Baju.Content.*)
// karena keduanya mengelola tabel order_baju. Tidak ada permission terpisah.
$config['permissions'] = array(
	'Order_Baju.Content.View',
	'Order_Baju.Content.Create',
	'Order_Baju.Content.Edit',
);