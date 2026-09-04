<?php defined('BASEPATH') || exit('No direct script access allowed');

$route[SITE_AREA . '/backup'] = 'backup/backup/index';
$route[SITE_AREA . '/backup/filter'] = 'backup/backup/filter';
$route[SITE_AREA . '/backup/document'] = 'backup/backup/document';
$route[SITE_AREA . '/backup/database-page'] = 'backup/backup/database_page';
$route[SITE_AREA . '/backup/database'] = 'backup/backup/database_page';
$route[SITE_AREA . '/backup/database/run'] = 'backup/backup/database';
$route[SITE_AREA . '/backup/per_id'] = 'backup/backup/dokumen_per_id';
$route[SITE_AREA . '/backup/per_id/process'] = 'backup/backup/dokumen_per_id_process';
$route[SITE_AREA . '/backup/per_folder'] = 'backup/backup/dokumen_per_folder';
$route[SITE_AREA . '/backup/per_folder/process'] = 'backup/backup/dokumen_per_folder_process';
$route[SITE_AREA . '/backup/download/doc/(:num)'] = 'backup/backup/download/doc/$1';
$route[SITE_AREA . '/backup/download/db/(:num)'] = 'backup/backup/download/db/$1';
