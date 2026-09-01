<?php defined('BASEPATH') || exit('No direct script access allowed');

// Route::context('reports') remaps admin/reports/report_pdf → report_pdf/reports
// Then BF_Router finds report_pdf/controllers/Reports.php and calls method 'reports' → index()
$route[SITE_AREA . '/reports/report_pdf']                 = 'report_pdf/reports/index';
$route[SITE_AREA . '/reports/report_pdf/filter']          = 'report_pdf/reports/filter';
$route[SITE_AREA . '/reports/report_pdf/pdf']             = 'report_pdf/reports/pdf';
$route[SITE_AREA . '/reports/report_pdf/view/(:num)']     = 'report_pdf/reports/view/$1';
$route[SITE_AREA . '/reports/report_pdf/download/(:num)'] = 'report_pdf/reports/download/$1';
