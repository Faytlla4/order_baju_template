<?php
defined('BASEPATH') || exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;

// Authentication
Route::any(LOGIN_URL, 'users/login', array('as' => 'login'));
Route::any(REGISTER_URL, 'users/register', array('as' => 'register'));
Route::block('users/login');
Route::block('users/register');

Route::any('logout', 'users/logout');
Route::any('forgot_password', 'users/forgot_password');
Route::any('reset_password/(:any)/(:any)', 'users/reset_password/$1/$2');

// Activation
Route::any('activate', 'users/activate');
Route::any('activate/(:any)', 'users/activate/$1');
Route::any('resend_activation', 'users/resend_activation');

Route::prefix(SITE_AREA, function(){
    // Block old transaksi/order_baju route
    Route::block('transaksi/order_baju', 'transaksi/order_baju/(:any)', 'transaksi/order_baju/(:any)/(:any)');

    // --- Content / Order Baju ---
    Route::any('content', 'order_baju/content/index');
    Route::any('content/order_baju', 'order_baju/content/index');
    Route::any('content/order_baju/create', 'order_baju/content/create');
    Route::any('content/order_baju/edit/(:num)', 'order_baju/content/edit/$1');
    Route::any('content/order_baju/get_data', 'order_baju/content/get_data');

    // --- Master Jenis Baju ---
    Route::any('master', 'master_jenis_baju/master/index');
    Route::any('master/jenis_baju', 'master_jenis_baju/master/index');
    Route::any('master/jenis_baju/create', 'master_jenis_baju/master/create');
    Route::any('master/jenis_baju/edit/(:num)', 'master_jenis_baju/master/edit/$1');
    Route::any('master/jenis_baju/get_data', 'master_jenis_baju/master/get_data');

    // --- Master Ukuran ---
    Route::any('master/ukuran', 'master_ukuran/master/index');
    Route::any('master/ukuran/create', 'master_ukuran/master/create');
    Route::any('master/ukuran/edit/(:num)', 'master_ukuran/master/edit/$1');
    Route::any('master/ukuran/get_data', 'master_ukuran/master/get_data');
    Route::any('master/ukuran/lookup_customer', 'master_ukuran/master/lookup_customer');

    // --- Master Warna ---
    Route::any('master/warna', 'master_warna/master/index');
    Route::any('master/warna/create', 'master_warna/master/create');
    Route::any('master/warna/edit/(:num)', 'master_warna/master/edit/$1');
    Route::any('master/warna/get_data', 'master_warna/master/get_data');
    Route::any('master/warna/lookup_customer', 'master_warna/master/lookup_customer');

    // --- Transaksi ---
    Route::any('transaksi', 'transaksi/transaksi/index');
    Route::any('transaksi/transaksi', 'transaksi/transaksi/index');
    Route::any('transaksi/transaksi/create', 'transaksi/transaksi/create');
    Route::any('transaksi/transaksi/create/(:any)', 'transaksi/transaksi/create/$1');
    Route::any('transaksi/transaksi/save', 'transaksi/transaksi/save');
    Route::any('transaksi/transaksi/edit/(:num)', 'transaksi/transaksi/edit/$1');
    Route::any('transaksi/transaksi/detail/(:num)', 'transaksi/transaksi/detail/$1');
    Route::any('transaksi/transaksi/get_data', 'transaksi/transaksi/get_data');
    Route::any('transaksi/transaksi/dokumen/(:num)/(:any)', 'transaksi/transaksi/dokumen/$1/$2');
    Route::any('transaksi/transaksi/view_dokumen/(:num)/(:any)', 'transaksi/transaksi/view_dokumen/$1/$2');
    Route::any('transaksi/transaksi/download_dokumen/(:num)/(:any)', 'transaksi/transaksi/download_dokumen/$1/$2');
    Route::any('transaksi/transaksi/get_dokumen_list/(:num)', 'transaksi/transaksi/get_dokumen_list/$1');

    // --- Reports PDF ---
    Route::any('reports', 'report_pdf/reports/index');
    Route::any('reports/report_pdf', 'report_pdf/reports/index');
    Route::any('reports/report_pdf/filter', 'report_pdf/reports/filter');
    Route::any('reports/report_pdf/pdf', 'report_pdf/reports/pdf');
    Route::any('reports/report_pdf/view/(:num)', 'report_pdf/reports/view/$1');
    Route::any('reports/report_pdf/download/(:num)', 'report_pdf/reports/download/$1');

    // --- Reports Excel ---
    Route::any('reports/report_excel', 'report_excel/reports/index');
    Route::any('reports/report_excel/filter', 'report_excel/reports/filter');
    Route::any('reports/report_excel/excel', 'report_excel/reports/excel');
    Route::any('reports/report_excel/download_excel/(:num)', 'report_excel/reports/download_excel/$1');

    // --- Backup ---
    Route::any('backup', 'backup/backup/index');
    Route::any('backup/filter', 'backup/backup/filter');
    Route::any('backup/document', 'backup/backup/document');
    Route::any('backup/database', 'backup/backup/database_page');
    Route::any('backup/database/run', 'backup/backup/database');
    Route::any('backup/download/doc/(:num)', 'backup/backup/download/doc/$1');
    Route::any('backup/download/db/(:num)', 'backup/backup/download/db/$1');

    Route::context('developer');
    Route::context('settings');
});

$route = Route::map($route);
