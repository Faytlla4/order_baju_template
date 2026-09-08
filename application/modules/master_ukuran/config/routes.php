<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| MASTER UKURAN ROUTES
|--------------------------------------------------------------------------
|
| URL publik:
|
|   /admin/master_ukuran/master
|
| Controller:
|
|   application/modules/master_ukuran/controllers/Master.php
|
| Route module Bonfire akan otomatis menambahkan nama module
| "master_ukuran" di depan route ini.
|
*/


/*
|--------------------------------------------------------------------------
| MASTER UKURAN - LIST
|--------------------------------------------------------------------------
|
| GET:
|
|   /admin/master_ukuran/master
|
| Controller:
|
|   Master::index()
|
*/

$route['master'] = 'master/index';


/*
|--------------------------------------------------------------------------
| MASTER UKURAN - CREATE
|--------------------------------------------------------------------------
|
| GET/POST:
|
|   /admin/master_ukuran/master/create
|
| Controller:
|
|   Master::create()
|
*/

$route['master/create'] = 'master/create';


/*
|--------------------------------------------------------------------------
| MASTER UKURAN - EDIT
|--------------------------------------------------------------------------
|
| GET/POST:
|
|   /admin/master_ukuran/master/edit/{id}
|
| Controller:
|
|   Master::edit({id})
|
*/

$route['master/edit/(:num)'] = 'master/edit/$1';


/*
|--------------------------------------------------------------------------
| MASTER UKURAN - DATATABLE
|--------------------------------------------------------------------------
|
| AJAX:
|
|   /admin/master_ukuran/master/get_data
|
| Controller:
|
|   Master::get_data()
|
*/

$route['master/get_data'] = 'master/get_data';


/*
|--------------------------------------------------------------------------
| MASTER UKURAN - CUSTOMER LOOKUP
|--------------------------------------------------------------------------
|
| AJAX:
|
|   /admin/master_ukuran/master/lookup_customer
|
| Controller:
|
|   Master::lookup_customer()
|
*/

$route['master/lookup_customer'] = 'master/lookup_customer';