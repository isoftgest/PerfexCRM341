# Configuración del servidor de licencias para Rentals


> Estado actual: la licencia está temporalmente desactivada en el módulo. La opción `rentals_license_enforced` se instala con valor `0`, por lo que el módulo funciona libremente hasta que prepares la API. Cuando quieras volver a exigir licencia, cambia esa opción a `1`.

Este módulo **no valida la licencia contra Perfex CRM**, sino contra un servidor externo propio. Por eso, en la opción `rentals_license_endpoint` debes poner la URL HTTPS real de tu API de licencias, por ejemplo:

```text
https://licencias.tudominio.com/api/validate
```

No debes poner una URL inventada como `https://tuservidor.com/api/licenses/validate` salvo que realmente exista y devuelva la respuesta esperada.

## Qué debe hacer esa URL

La URL debe aceptar una petición `POST` con JSON y validar:

- `product`: debe ser `rentals`.
- `license_key`: código de licencia enviado desde Perfex.
- `installation_uuid`: identificador único de la instalación.
- `domain`: URL base/dominio de Perfex.
- `server_fingerprint`: huella técnica del servidor.
- `perfex_version`: versión de Perfex.
- `php_version`: versión de PHP.

Si la licencia es válida, el endpoint debe devolver JSON con `success: true`, `status: active`, el mismo `installation_uuid`, el mismo `server_fingerprint`, un `payload` firmado y una `signature` en base64.

## Respuesta válida esperada

```json
{
  "success": true,
  "status": "active",
  "license_key": "XXXX-XXXX-XXXX",
  "installation_uuid": "uuid-instalacion",
  "domain": "https://crm.tudominio.com/",
  "server_fingerprint": "hash-servidor",
  "activated_at": "2026-01-01 10:00:00",
  "expires_at": null,
  "payload": "{...json firmado...}",
  "signature": "firma-base64"
}
```

## Ejemplo mínimo de endpoint PHP

> Este ejemplo es orientativo. En producción debes guardar licencias y activaciones en una base de datos, usar HTTPS, limitar reintentos y proteger la clave privada fuera del directorio público.

```php
<?php

header('Content-Type: application/json');

$privateKeyPath = __DIR__ . '/../secure/rentals_private_key.pem';
$validLicenses = [
    'DEMO-1234-5678-ABCD' => [
        'status' => 'active',
        'expires_at' => null,
    ],
];

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input) || ($input['product'] ?? '') !== 'rentals') {
    http_response_code(400);
    echo json_encode(['success' => false, 'status' => 'invalid', 'message' => 'Invalid product']);
    exit;
}

$licenseKey = $input['license_key'] ?? '';

if (!isset($validLicenses[$licenseKey]) || $validLicenses[$licenseKey]['status'] !== 'active') {
    echo json_encode(['success' => false, 'status' => 'invalid', 'message' => 'Invalid license']);
    exit;
}

$payloadData = [
    'product' => 'rentals',
    'license_key' => $licenseKey,
    'installation_uuid' => $input['installation_uuid'],
    'domain' => $input['domain'],
    'server_fingerprint' => $input['server_fingerprint'],
    'activated_at' => date('Y-m-d H:i:s'),
    'expires_at' => $validLicenses[$licenseKey]['expires_at'],
];

$payload = json_encode($payloadData, JSON_UNESCAPED_SLASHES);
$privateKey = file_get_contents($privateKeyPath);
openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256);

echo json_encode([
    'success' => true,
    'status' => 'active',
    'license_key' => $licenseKey,
    'installation_uuid' => $payloadData['installation_uuid'],
    'domain' => $payloadData['domain'],
    'server_fingerprint' => $payloadData['server_fingerprint'],
    'activated_at' => $payloadData['activated_at'],
    'expires_at' => $payloadData['expires_at'],
    'payload' => $payload,
    'signature' => base64_encode($signature),
]);
```

## Claves RSA necesarias

Genera una clave privada y una pública:

```bash
openssl genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out rentals_private_key.pem
openssl rsa -pubout -in rentals_private_key.pem -out rentals_public_key.pem
```

- La **clave privada** va únicamente en tu servidor de licencias.
- La **clave pública** debe sustituir la clave de ejemplo en `modules/rentals/helpers/rentals_helper.php`, función `rentals_public_key()`.

## Configurar el endpoint en Perfex

Actualiza la opción del módulo:

```sql
UPDATE tbloptions
SET value = 'https://licencias.tudominio.com/api/validate'
WHERE name = 'rentals_license_endpoint';

-- Solo cuando quieras volver a exigir licencia:
UPDATE tbloptions
SET value = '1'
WHERE name = 'rentals_license_enforced';
```

Después entra en Perfex:

```text
Admin > Alquileres > Licencia
```

Introduce una licencia existente en tu servidor, por ejemplo:

```text
DEMO-1234-5678-ABCD
```
