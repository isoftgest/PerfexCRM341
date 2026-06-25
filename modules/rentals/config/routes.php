<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Rutas amigables requeridas por el módulo para evitar 404 en /admin/rental_*.
$route['admin/rentals'] = 'rentals/rentals';
$route['admin/rentals/(:any)'] = 'rentals/rentals/$1';
$route['admin/rentals/(:any)/(:any)'] = 'rentals/rentals/$1/$2';

$route['admin/rental_properties'] = 'rentals/rental_properties';
$route['admin/rental_properties/(:any)'] = 'rentals/rental_properties/$1';
$route['admin/rental_properties/(:any)/(:any)'] = 'rentals/rental_properties/$1/$2';

$route['admin/rental_units'] = 'rentals/rental_units';
$route['admin/rental_units/(:any)'] = 'rentals/rental_units/$1';
$route['admin/rental_units/(:any)/(:any)'] = 'rentals/rental_units/$1/$2';

$route['admin/rental_payments'] = 'rentals/rental_payments';
$route['admin/rental_payments/(:any)'] = 'rentals/rental_payments/$1';
$route['admin/rental_payments/(:any)/(:any)'] = 'rentals/rental_payments/$1/$2';

$route['admin/rental_deposits'] = 'rentals/rental_deposits';
$route['admin/rental_deposits/(:any)'] = 'rentals/rental_deposits/$1';
$route['admin/rental_deposits/(:any)/(:any)'] = 'rentals/rental_deposits/$1/$2';

$route['admin/rental_expenses'] = 'rentals/rental_expenses';
$route['admin/rental_expenses/(:any)'] = 'rentals/rental_expenses/$1';
$route['admin/rental_expenses/(:any)/(:any)'] = 'rentals/rental_expenses/$1/$2';

$route['admin/rental_reports'] = 'rentals/rental_reports';
$route['admin/rental_reports/(:any)'] = 'rentals/rental_reports/$1';
$route['admin/rental_reports/(:any)/(:any)'] = 'rentals/rental_reports/$1/$2';

$route['admin/rentals_license'] = 'rentals/rentals_license';
$route['admin/rentals_license/(:any)'] = 'rentals/rentals_license/$1';
