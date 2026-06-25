<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Helper central del módulo Rentals. Mantiene la lógica compartida optimizada y reutilizable.

define('RENTALS_LICENSE_GRACE_DAYS', 7);
define('RENTALS_LICENSE_ENDPOINT_OPTION', 'rentals_license_endpoint');
define('RENTALS_DEFAULT_LICENSE_ENDPOINT', 'https://licencias.tudominio.com/api/validate');
define('RENTALS_LICENSE_ENFORCED_OPTION', 'rentals_license_enforced');


function rentals_license_is_enforced()
{
    // Licencia temporalmente desactivada por configuración: el módulo queda libre hasta preparar la API externa.
    if (!function_exists('get_option')) {
        return false;
    }

    return (string) get_option(RENTALS_LICENSE_ENFORCED_OPTION) === '1';
}

function rentals_table($name)
{
    return db_prefix() . $name;
}

function rentals_now()
{
    return date('Y-m-d H:i:s');
}

function rentals_current_staff_id()
{
    return function_exists('get_staff_user_id') ? get_staff_user_id() : null;
}

function rentals_is_admin_or_can($feature, $capability)
{
    return is_admin() || has_permission($feature, '', $capability);
}

function rentals_user_can_any()
{
    if (is_admin()) { return true; }
    $permissions = [
        'rentals'=>['view','view_own','create','edit','delete'],
        'rentals_properties'=>['view','create','edit','delete'],
        'rentals_units'=>['view','create','edit','delete'],
        'rentals_payments'=>['view','create','edit','delete','mark_paid'],
        'rentals_deposits'=>['view','create','edit','delete','return'],
        'rentals_expenses'=>['view','create','edit','delete'],
        'rentals_reports'=>['view','export'],
        'rentals_license'=>['manage'],
    ];
    foreach ($permissions as $feature=>$caps) foreach ($caps as $cap) if (has_permission($feature,'',$cap)) return true;
    return false;
}

function rentals_clean($value)
{
    $CI=&get_instance();
    if (is_array($value)) return array_map('rentals_clean',$value);
    return $CI->security->xss_clean(trim((string)$value));
}

function rentals_valid_date($date)
{
    if ($date === '' || $date === null) return true;
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function rentals_valid_month($month)
{
    return (bool) preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', (string)$month);
}

function rentals_decimal($value)
{
    return is_numeric($value) ? number_format((float)$value, 2, '.', '') : false;
}

function rentals_get_server_fingerprint()
{
    $parts = [
        function_exists('base_url') ? base_url() : '',
        $_SERVER['SERVER_NAME'] ?? '',
        $_SERVER['DOCUMENT_ROOT'] ?? '',
        function_exists('php_uname') ? php_uname() : '',
        PHP_OS,
        FCPATH,
        $_SERVER['SERVER_ADDR'] ?? '',
    ];
    return hash('sha256', implode('|', $parts));
}

function rentals_public_key()
{
    // Clave pública de ejemplo; debe sustituirse por la clave pública real del servidor de licencias.
    return "-----BEGIN PUBLIC KEY-----\nMFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBALx3o2dQ8VqW1ZcM0lYlKk8A9xM3u0uZ\nGmH5B2q3Pqj7xE3f3x7OVm8pP2Lxj3qk8n4s+e+f2dbQIDAQAB\n-----END PUBLIC KEY-----";
}

function rentals_verify_license_signature($payload, $signature)
{
    if ($payload === '' || $signature === '') return false;
    if (!function_exists('openssl_verify')) return false;
    return openssl_verify($payload, base64_decode($signature), rentals_public_key(), OPENSSL_ALGO_SHA256) === 1;
}

function rentals_get_license_row()
{
    $CI=&get_instance();
    if (!$CI->db->table_exists(rentals_table('rentals_license'))) return null;
    return $CI->db->order_by('id','DESC')->get(rentals_table('rentals_license'),1)->row();
}

function rentals_license_log($event, $license_key = null, $message = null)
{
    $CI=&get_instance();
    if (!$CI->db->table_exists(rentals_table('rentals_license_logs'))) return;
    $CI->db->insert(rentals_table('rentals_license_logs'), [
        'event'=>$event,'license_key'=>$license_key,'message'=>$message,
        'ip_address'=>$CI->input->ip_address(),'created_at'=>rentals_now(),'created_by'=>rentals_current_staff_id(),
    ]);
}

function rentals_validate_license_remote($license_key, $installation_uuid = null)
{
    $CI=&get_instance();
    $endpoint = get_option(RENTALS_LICENSE_ENDPOINT_OPTION) ?: RENTALS_DEFAULT_LICENSE_ENDPOINT;
    $payload = [
        'product'=>'rentals','license_key'=>$license_key,
        'installation_uuid'=>$installation_uuid ?: rentals_get_or_create_installation_uuid(),
        'domain'=>function_exists('base_url') ? base_url() : ($_SERVER['SERVER_NAME'] ?? ''),
        'server_fingerprint'=>rentals_get_server_fingerprint(),
        'perfex_version'=>defined('APP_VERSION') ? APP_VERSION : '3.4.1',
        'php_version'=>PHP_VERSION,
    ];
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_POSTFIELDS=>json_encode($payload),CURLOPT_TIMEOUT=>20,CURLOPT_SSL_VERIFYPEER=>true]);
    $response = curl_exec($ch); $error = curl_error($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($response === false || $code < 200 || $code >= 300) return ['success'=>false,'message'=>$error ?: 'HTTP '.$code];
    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : ['success'=>false,'message'=>'Invalid JSON'];
}

function rentals_get_or_create_installation_uuid()
{
    $CI=&get_instance();
    $row = rentals_get_license_row();
    if ($row && !empty($row->installation_uuid)) return $row->installation_uuid;
    return (function_exists('app_generate_hash') ? app_generate_hash() : bin2hex(random_bytes(16))) . '-' . time();
}

function rentals_check_license($attempt_remote = true)
{
    // Mientras `rentals_license_enforced` no sea `1`, no se bloquea ninguna funcionalidad.
    // Para reactivar la licencia basta con poner esa opción a `1` cuando la API esté lista.
    if (!rentals_license_is_enforced()) {
        return true;
    }

    $row = rentals_get_license_row();
    if (!$row || $row->license_status !== 'active') return false;
    if ($row->server_fingerprint !== rentals_get_server_fingerprint()) { rentals_license_log('fingerprint_mismatch',$row->license_key,_l('rentals_license_installation_mismatch')); return false; }
    if (!rentals_verify_license_signature((string)$row->license_payload, (string)$row->license_signature)) { rentals_license_log('signature_invalid',$row->license_key); return false; }
    if (!empty($row->expires_at) && strtotime($row->expires_at) < time()) { rentals_license_log('license_expired',$row->license_key); return false; }
    $last = !empty($row->last_check) ? strtotime($row->last_check) : 0;
    if ($attempt_remote && $last && (time()-$last) > RENTALS_LICENSE_GRACE_DAYS*86400) {
        $remote = rentals_validate_license_remote($row->license_key, $row->installation_uuid);
        if (!empty($remote['success']) && ($remote['status'] ?? '') === 'active') return rentals_store_license_response($remote, $row->license_key);
        rentals_license_log('remote_validation_failed',$row->license_key,$remote['message'] ?? ($remote['status'] ?? ''));
        return false;
    }
    return true;
}

function rentals_store_license_response($response, $license_key)
{
    $CI=&get_instance();
    $status = $response['status'] ?? 'invalid';
    $payload = (string)($response['payload'] ?? '');
    $signature = (string)($response['signature'] ?? '');
    if ($status !== 'active' || !rentals_verify_license_signature($payload, $signature)) return false;
    $data = ['license_key'=>$license_key,'installation_uuid'=>$response['installation_uuid'] ?? rentals_get_or_create_installation_uuid(),'domain'=>$response['domain'] ?? base_url(),'server_fingerprint'=>$response['server_fingerprint'] ?? rentals_get_server_fingerprint(),'license_status'=>$status,'license_payload'=>$payload,'license_signature'=>$signature,'last_check'=>rentals_now(),'activated_at'=>$response['activated_at'] ?? rentals_now(),'expires_at'=>$response['expires_at'] ?? null,'updated_at'=>rentals_now()];
    $row = rentals_get_license_row();
    if ($row) { $CI->db->where('id',$row->id)->update(rentals_table('rentals_license'),$data); } else { $data['created_at']=rentals_now(); $CI->db->insert(rentals_table('rentals_license'),$data); }
    rentals_license_log('activation_success',$license_key);
    return true;
}
