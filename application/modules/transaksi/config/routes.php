<?php defined('BASEPATH') || exit('No direct script access allowed');

$route[SITE_AREA . '/transaksi/transaksi']               = 'transaksi/transaksi/index';
$route[SITE_AREA . '/transaksi/transaksi/create']        = 'transaksi/transaksi/create';
$route[SITE_AREA . '/transaksi/transaksi/create/(:any)'] = 'transaksi/transaksi/create/$1';
$route[SITE_AREA . '/transaksi/transaksi/save']          = 'transaksi/transaksi/save';
$route[SITE_AREA . '/transaksi/transaksi/delete']        = 'transaksi/transaksi/delete';
$route[SITE_AREA . '/transaksi/transaksi/edit/(:num)']   = 'transaksi/transaksi/edit/$1';
$route[SITE_AREA . '/transaksi/transaksi/detail/(:num)'] = 'transaksi/transaksi/detail/$1';
$route[SITE_AREA . '/transaksi/transaksi/get_data']      = 'transaksi/transaksi/get_data';