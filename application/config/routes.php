<?php
defined('BASEPATH') || exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

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

// Contexts
//
// Blokir access ke module Transaksi lama (order_baju/Transaksi) sehingga
// New/Save selalu memakai module transaksi yang baru (transaksi/transaksi).
// Module / file lama tetap ada tetapi tidak lagi dapat dijangkau lewat URL.
Route::prefix(SITE_AREA, function(){
    Route::block('transaksi/order_baju', 'transaksi/order_baju/(:any)', 'transaksi/order_baju/(:any)/(:any)');
    Route::any('transaksi/transaksi/dokumen/(:num)/(:any)', 'transaksi/transaksi/dokumen/$1/$2');
    Route::any('transaksi/transaksi/view_dokumen/(:num)/(:any)', 'transaksi/transaksi/view_dokumen/$1/$2');
    Route::any('transaksi/transaksi/download_dokumen/(:num)/(:any)', 'transaksi/transaksi/download_dokumen/$1/$2');
    Route::any('transaksi/transaksi/get_dokumen_list/(:num)', 'transaksi/transaksi/get_dokumen_list/$1');

    // Content context
    Route::any('content', 'content/content/index');
    Route::any('content/order_baju', 'content/content/order_baju');
    Route::any('content/order_baju_save', 'content/content/order_baju_save');
    Route::any('content/order_baju_delete/(:num)', 'content/content/order_baju_delete/$1');

    // Master context
    Route::any('master', 'master/master/index');
    Route::any('master/jenis_baju', 'master/master/jenis_baju');
    Route::any('master/jenis_baju_save', 'master/master/jenis_baju_save');
    Route::any('master/jenis_baju_delete/(:num)', 'master/master/jenis_baju_delete/$1');
    Route::any('master/ukuran', 'master/master/ukuran');
    Route::any('master/ukuran_save', 'master/master/ukuran_save');
    Route::any('master/ukuran_delete/(:num)', 'master/master/ukuran_delete/$1');
    Route::any('master/warna', 'master/master/warna');
    Route::any('master/warna_save', 'master/master/warna_save');
    Route::any('master/warna_delete/(:num)', 'master/master/warna_delete/$1');

    Route::context('transaksi', array('home' => SITE_AREA .'/transaksi/index'));

    // Reports context (module)
    Route::any('reports', 'reports/reports/index');
    Route::any('reports/report_pdf', 'reports/reports/report_pdf');
    Route::any('reports/report_excel', 'reports/reports/report_excel');
    Route::any('reports/download_pdf', 'reports/reports/download_pdf');
    Route::any('reports/download_excel', 'reports/reports/download_excel');

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
