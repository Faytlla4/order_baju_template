<?php defined('BASEPATH') || exit('No direct script access allowed');

$route[SITE_AREA . '/laporan-dokumen']              = 'backup_report/reports/laporan_dokumen_pdf';
$route[SITE_AREA . '/laporan-dokumen/pdf']           = 'backup_report/reports/laporan_dokumen_pdf';
$route[SITE_AREA . '/laporan-dokumen/excel']         = 'backup_report/reports/laporan_dokumen_excel';
$route[SITE_AREA . '/laporan-dokumen/filter']        = 'backup_report/reports/filter_dokumen';
$route[SITE_AREA . '/laporan-dokumen/cetak-pdf']     = 'backup_report/reports/cetak_pdf_dokumen';
$route[SITE_AREA . '/laporan-dokumen/cetak-excel']   = 'backup_report/reports/cetak_excel_dokumen';
$route[SITE_AREA . '/laporan-database']              = 'backup_report/reports/laporan_database_excel';
$route[SITE_AREA . '/laporan-database/pdf']          = 'backup_report/reports/laporan_database_pdf';
$route[SITE_AREA . '/laporan-database/excel']        = 'backup_report/reports/laporan_database_excel';
$route[SITE_AREA . '/laporan-database/filter']       = 'backup_report/reports/filter_database';
$route[SITE_AREA . '/laporan-database/cetak-pdf']    = 'backup_report/reports/cetak_pdf_database';
$route[SITE_AREA . '/laporan-database/cetak-excel']  = 'backup_report/reports/cetak_excel_database';
$route[SITE_AREA . '/laporan-history']               = 'backup_report/reports/riwayat_laporan';
$route[SITE_AREA . '/laporan-history/filter']        = 'backup_report/reports/filter_riwayat';
