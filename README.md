# bliq-mx/bliq-api-php

Biblioteca PHP para conectar con la API de timbrado de Bliq (uso interno).


## Instalación

Utilizando [composer](https://getcomposer.org/):

```shell
composer require bliq-mx/bliq-api-php
```


## Ejemplo de uso

```php
<?php

declare(strict_types=1);

use Bliq\Stamp\BliqApiException;
use Bliq\Stamp\Exception\BliqStampApi;
use Bliq\Stamp\ValueObject\Certificado;

$apiToken = 'token';
$devMode = true;

$api = new BliqStampApi($apiToken, $devMode);

try {
    $certificado = new Certificado('cer', 'key', 'passphrase');

    $api->firmarManifiesto($certificado);

    echo 'OK';
} catch (BliqApiException $e) {
    echo $e->getMessage();
}
```


## Compatibilidad

Compatible con PHP 7.0 y superior.
