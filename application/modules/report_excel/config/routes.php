<?php defined('BASEPATH') || exit('No direct script access allowed');

// Route::context('reports') remaps admin/reports/report_excel → report_excel/reports
// Then BF_Router finds report_excel/controllers/Reports.php and calls method 'reports' → index()
$route[SITE_AREA . '/reports/report_excel']                    = 'report_excel/reports/index';
$route[SITE_AREA . '/reports/report_excel/filter']             = 'report_excel/reports/filter';
$route[SITE_AREA . '/reports/report_excel/excel']              = 'report_excel/reports/excel';
$route[SITE_AREA . '/reports/report_excel/download_excel/(:num)'] = 'report_excel/reports/download_excel/$1';
