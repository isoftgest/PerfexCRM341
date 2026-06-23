<?php

defined('BASEPATH') or exit('No direct script access allowed');
// Por seguridad no se eliminan tablas ni datos operativos al desinstalar.
if (defined('RENTALS_FORCE_DROP_DATA') && RENTALS_FORCE_DROP_DATA) {
    $CI = &get_instance();
    foreach (['rental_expenses','rental_deposits','rental_payments','rental_price_history','rentals','rental_units','rental_properties','rentals_license_logs','rentals_license'] as $table) {
        $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . $table . '`');
    }
}
