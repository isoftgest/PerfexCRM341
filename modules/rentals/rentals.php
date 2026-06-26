<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Rentals
Description: Gestión de alquileres de pisos, habitaciones y garajes para Perfex CRM.
Version: 1.0.0
Requires at least: 3.4.1
*/

define('RENTALS_MODULE_NAME', 'rentals');

register_activation_hook(RENTALS_MODULE_NAME, 'rentals_activation_hook');
register_uninstall_hook(RENTALS_MODULE_NAME, 'rentals_uninstall_hook');
register_language_files(RENTALS_MODULE_NAME, ['rentals']);

$CI = &get_instance();
$CI->load->helper('rentals/rentals');

hooks()->add_action('admin_init', 'rentals_register_permissions');
hooks()->add_action('admin_init', 'rentals_init_menu_items');
// El guard queda registrado, pero solo bloquea si la opción rentals_license_enforced está activa.
hooks()->add_action('app_admin_head', 'rentals_license_route_guard');

function rentals_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

function rentals_uninstall_hook()
{
    require_once(__DIR__ . '/uninstall.php');
}

function rentals_register_permissions()
{
    register_staff_capabilities('rentals', ['capabilities'=>['view'=>_l('permission_view').' '._l('rentals'),'view_own'=>_l('permission_view_own').' '._l('rentals'),'create'=>_l('permission_create').' '._l('rentals'),'edit'=>_l('permission_edit').' '._l('rentals'),'delete'=>_l('permission_delete').' '._l('rentals')]], _l('rentals'));
    register_staff_capabilities('rentals_properties', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_properties'),'create'=>_l('permission_create').' '._l('rental_properties'),'edit'=>_l('permission_edit').' '._l('rental_properties'),'delete'=>_l('permission_delete').' '._l('rental_properties')]], _l('rental_properties'));
    register_staff_capabilities('rentals_units', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_units'),'create'=>_l('permission_create').' '._l('rental_units'),'edit'=>_l('permission_edit').' '._l('rental_units'),'delete'=>_l('permission_delete').' '._l('rental_units')]], _l('rental_units'));
    register_staff_capabilities('rentals_payments', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_payments'),'create'=>_l('permission_create').' '._l('rental_payments'),'edit'=>_l('permission_edit').' '._l('rental_payments'),'delete'=>_l('permission_delete').' '._l('rental_payments'),'mark_paid'=>_l('rental_permission_mark_paid')]], _l('rental_payments'));
    register_staff_capabilities('rentals_deposits', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_deposits'),'create'=>_l('permission_create').' '._l('rental_deposits'),'edit'=>_l('permission_edit').' '._l('rental_deposits'),'delete'=>_l('permission_delete').' '._l('rental_deposits'),'return'=>_l('rental_permission_return_deposit')]], _l('rental_deposits'));
    register_staff_capabilities('rentals_expenses', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_expenses'),'create'=>_l('permission_create').' '._l('rental_expenses'),'edit'=>_l('permission_edit').' '._l('rental_expenses'),'delete'=>_l('permission_delete').' '._l('rental_expenses')]], _l('rental_expenses'));
    register_staff_capabilities('rentals_reports', ['capabilities'=>['view'=>_l('permission_view').' '._l('rental_reports'),'export'=>_l('rental_permission_export_reports')]], _l('rental_reports'));
    register_staff_capabilities('rentals_settings', ['capabilities'=>['view'=>_l('permission_view').' '._l('settings'),'edit'=>_l('permission_edit').' '._l('settings')]], _l('rental_settings'));
    register_staff_capabilities('rentals_license', ['capabilities'=>['manage'=>_l('rental_permission_manage_license')]], _l('rentals_license'));
}

function rentals_init_menu_items()
{
    $CI = &get_instance();
    if (!rentals_user_can_any()) { return; }
    $CI->app_menu->add_sidebar_menu_item('rentals', ['name'=>_l('rentals'),'href'=>'#','icon'=>'fa fa-home','position'=>35]);
    $items = [
        ['rentals-main','rentals','rentals','view','rentals'],
        ['rental-properties','rental_properties','rentals_properties','view','rentals/properties'],
        ['rental-units','rental_units','rentals_units','view','rentals/units'],
        ['rental-payments','rental_payments','rentals_payments','view','rentals/payments'],
        ['rental-deposits','rental_deposits','rentals_deposits','view','rentals/deposits'],
        ['rental-expenses','rental_expenses','rentals_expenses','view','rentals/expenses'],
        ['rental-reports','rental_reports','rentals_reports','view','rentals/reports']
    ];
    foreach ($items as $item) {
        if (is_admin() || has_permission($item[2], '', $item[3]) || ($item[2]==='rentals' && has_permission('rentals','','view_own'))) {
            $CI->app_menu->add_sidebar_children_item('rentals', ['slug'=>$item[0],'name'=>_l($item[1]),'href'=>admin_url($item[4]),'position'=>10]);
        }
    }
    if (is_admin() || has_permission('rentals_license', '', 'manage')) {
        $CI->app_menu->add_sidebar_children_item('rentals', ['slug'=>'rentals-license','name'=>_l('rentals_license'),'href'=>admin_url('rentals/license'),'position'=>90]);
    }
}

function rentals_license_route_guard()
{
    // Licencia desactivada temporalmente: no redirige hasta que se active rentals_license_enforced.
    if (!rentals_license_is_enforced()) { return; }

    $CI=&get_instance();
    $segment = $CI->uri->segment(2);
    $routes = ['rentals','rental_properties','rental_units','rental_payments','rental_deposits','rental_expenses','rental_reports'];
    if (in_array($segment, $routes, true) && !rentals_check_license()) {
        set_alert('danger', _l('rentals_license_required'));
        redirect(admin_url('rentals_license'));
    }
}
