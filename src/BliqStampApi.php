<?php

declare(strict_types=1);

namespace Bliq\Api;

use Bliq\Api\Exception\BliqApiException;
use Bliq\Api\StampResults\CancelCfdiResult;
use Bliq\Api\StampResults\CancelCfdiResultV2;
use Bliq\Api\StampResults\CancelCfdiResultV3;
use Bliq\Api\StampResults\CreateCfdiResult;
use Bliq\Api\StampResults\CreatePdfResult;
use Bliq\Api\StampResults\FetchCfdiResult;
use Bliq\Api\StampResults\StatusCfdiResult;
use Bliq\Api\ValueObject\Certificado;

class BliqStampApi
{
    /**
     * @var string URL de conexión a la API.
     */
    const API_BASE_URL = 'https://api.reeply.mx/timbrado/';

    /** @var string */
    private $token;

    /** @var string */
    private $mode;

    /**
     * @param string $token Token de acceso a la API
     * @param bool $devMode ¿Realizar peticiones en modo desarrollo?
     */
    public function __construct(string $token, bool $devMode = false)
    {
        $this->mode = $devMode ? 'dev' : 'prod';
        $this->token = $token;

        if (empty($this->token)) {
            throw new BliqApiException('El token no ha sido establecido.', 10);
        }
    }

    /**
     * Crea y obtiene el XML de un CFDI
     * @throws BliqApiException
     */
    public function crearXml40(array $data): array
    {
        return $this->post('crear_xml', $data);
    }

    /**
     * Crea y obtiene el XML de un CFDI utilizando los datos del certificado.
     * @throws BliqApiException
     */
    public function createXmlWithCertFiles(array $comprobante, Certificado $certificado): array
    {
        $params = [
            'Comprobante' => $comprobante,
            'cer_data' => $certificado->cer(),
            'key_data' => $certificado->key(),
            'key_passphrase' => $certificado->passphrase(),
        ];
        return $this->post('crear_xml', $params);
    }

    /**
     * Crea y obtiene el XML de un CFDI utilizando el rfc y número de certificado.
     * @throws BliqApiException
     */
    public function createXmlWithCertNumber(array $comprobante, string $certNumber, string $rfc): array
    {
        $params = [
            'Comprobante' => $comprobante,
            'certificado' => [
                'numero' => $certNumber,
                'rfc' => $rfc,
            ],
        ];
        return $this->post('crear_xml', $params);
    }

    /**
     * Realiza la creación y el timbrado de un CFDI utilizando los datos del certificado.
     * @throws BliqApiException
     */
    public function createCfdi40WithCertFiles(array $comprobante, Certificado $certificado): CreateCfdiResult
    {
        $params = [
            'Comprobante' => $comprobante,
            'cer_data' => $certificado->cer(),
            'key_data' => $certificado->key(),
            'key_passphrase' => $certificado->passphrase(),
        ];
        $response = $this->post('crear_cfdi', $params);
        return new CreateCfdiResult($response);
    }

    /**
     * Realiza la creación y el timbrado de un CFDI utilizando el rfc y número de certificado.
     * @throws BliqApiException
     */
    public function createCfdi40WithCertNumber(array $comprobante, string $certNumber, string $rfc): CreateCfdiResult
    {
        $params = [
            'Comprobante' => $comprobante,
            'certificado' => [
                'numero' => $certNumber,
                'rfc' => $rfc,
            ],
        ];
        $response = $this->post('crear_cfdi', $params);
        return new CreateCfdiResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante el UUID
     * @throws BliqApiException
     */
    public function createPdfByUUID(string $uuid, array $params = []): CreatePdfResult
    {
        $params['uuid'] = $uuid;
        $response = $this->post('crear_pdf', $params);
        return new CreatePdfResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante el XML
     * @throws BliqApiException
     */
    public function createPdfByXML(string $xml, array $params = []): CreatePdfResult
    {
        $params['xml'] = $xml;
        $response = $this->post('crear_pdf', $params);
        return new CreatePdfResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante los datos para crear un CFDI
     * @throws BliqApiException
     */
    public function createPdfByData(array $comprobante, array $params = []): CreatePdfResult
    {
        $params['Comprobante'] = $comprobante;
        $response = $this->post('crear_pdf', $params);
        return new CreatePdfResult($response);
    }

    /**
     * Realiza la recuperación de un CFDI
     * @throws BliqApiException
     */
    public function getXmlByUUID(string $uuid): FetchCfdiResult
    {
        $data = ['uuid' => $uuid];
        $response = $this->post('recuperar_cfdi', $data);
        return new FetchCfdiResult($response);
    }

    /**
     * Realiza la cancelación de un CFDI
     * @throws BliqApiException
     */
    public function cancelCfdi(string $uuid, string $motivo, string $folioSustitucion, Certificado $certData): CancelCfdiResult
    {
        $data = [
            'uuid' => $uuid,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'cer_data' => $certData->cer(),
            'key_data' => $certData->key(),
            'key_passphrase' => $certData->passphrase(),
        ];
        $response = $this->post('cancelar_cfdi', $data);
        return new CancelCfdiResult($response);
    }

    /**
     * Realiza la cancelación de un CFDI utilizando el número de certificado y el RFC
     * @throws BliqApiException
     */
    public function cancelCfdiWithCertNumber(string $uuid, string $motivo, string $folioSustitucion, string $certNumber, string $rfc): CancelCfdiResult
    {
        $data = [
            'uuid' => $uuid,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'certificado' => [
                'numero' => $certNumber,
                'rfc' => $rfc,
            ],
        ];
        $response = $this->post('cancelar_cfdi', $data);
        return new CancelCfdiResult($response);
    }

    /**
     * Realiza la cancelación de un CFDI
     * @throws BliqApiException
     */
    public function cancelCfdiV2(string $uuid, string $motivo, string $folioSustitucion, Certificado $certData): CancelCfdiResultV2
    {
        $data = [
            'uuid' => $uuid,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'certificado' => [
                'cer' => $certData->cer(),
                'key' => $certData->key(),
                'pwd' => $certData->passphrase(),
            ],
        ];
        $response = $this->post('cancelar_cfdi_v2', $data);
        return new CancelCfdiResultV2($response);
    }

    /**
     * Realiza la cancelación de un CFDI utilizando el número de certificado y el RFC
     * @throws BliqApiException
     */
    public function cancelCfdiWithCertNumberV2(string $uuid, string $motivo, string $folioSustitucion, string $certNumber, string $rfc): CancelCfdiResultV2
    {
        $data = [
            'uuid' => $uuid,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'certificado' => [
                'numero' => $certNumber,
                'rfc' => $rfc,
            ],
        ];
        $response = $this->post('cancelar_cfdi_v2', $data);
        return new CancelCfdiResultV2($response);
    }
    /**
     * Realiza la cancelación de un CFDI
     * @throws BliqApiException
     */
    public function cancelCfdiV3(
        string $uuid,
        string $rfcReceptor,
        string $total,
        string $motivo,
        string $folioSustitucion,
        Certificado $certData
    ): CancelCfdiResultV3 {
        $data = [
            'uuid' => $uuid,
            'rfc_receptor' => $rfcReceptor,
            'total' => $total,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'certificado' => [
                'cer' => $certData->cer(),
                'key' => $certData->key(),
                'pwd' => $certData->passphrase(),
            ],
        ];
        $response = $this->post('cancelar_cfdi_v3', $data);
        return new CancelCfdiResultV3($response);
    }

    /**
     * Realiza la cancelación de un CFDI utilizando el número de certificado y el RFC
     * @throws BliqApiException
     */
    public function cancelCfdiWithCertNumberV3(
        string $uuid,
        string $rfcReceptor,
        string $total,
        string $motivo,
        string $folioSustitucion,
        string $certNumber,
        string $rfc
    ): CancelCfdiResultV3 {
        $data = [
            'uuid' => $uuid,
            'rfc_receptor' => $rfcReceptor,
            'total' => $total,
            'motivo' => $motivo,
            'folio_sustitucion' => $folioSustitucion,
            'certificado' => [
                'numero' => $certNumber,
                'rfc' => $rfc,
            ],
        ];
        $response = $this->post('cancelar_cfdi_v3', $data);
        return new CancelCfdiResultV3($response);
    }



    /**
     * Realiza la petición de estatus de un CFDI
     * @throws BliqApiException
     */
    public function statusCfdi(string $uuid, string $rfcEmisor, string $rfcReceptor, string $total): StatusCfdiResult
    {
        $data = [
            'uuid' => $uuid,
            'rfc_emisor' => $rfcEmisor,
            'rfc_receptor' => $rfcReceptor,
            'total' => $total,
        ];
        $response = $this->post('estatus_cfdi', $data);
        return new StatusCfdiResult($response);
    }

    /**
     * Realiza la firma del manifiesto
     * @throws BliqApiException
     */
    public function signManifiest(Certificado $certificado)
    {
        $data = [
            'cer_data' => base64_encode($certificado->cer()),
            'key_data' => base64_encode($certificado->key()),
            'key_passphrase' => $certificado->passphrase(),
        ];
        $resultData = $this->post('firmar_manifiesto', $data);

        if (empty($resultData['success'])) {
            throw new BliqApiException($resultData['message'] ?? 'Error no especificado');
        }
    }

    /**
     * Registra un certificado para utilizarlo posteriormente
     * @throws BliqApiException
     */
    public function registerCertificate(Certificado $certificado)
    {
        $data = [
            'cer_data' => base64_encode($certificado->cer()),
            'key_data' => base64_encode($certificado->key()),
            'key_passphrase' => $certificado->passphrase(),
        ];
        $resultData = $this->post('certificado_registrar', $data);

        if (empty($resultData['success'])) {
            throw new BliqApiException($resultData['message'] ?? 'Error no especificado');
        }
    }

    /**
     * Realiza la petición de registro de un RFC para timbrado
     * @throws BliqApiException
     */
    public function registerRfc(string $rfc)
    {
        $params = [
            'rfc' => $rfc,
        ];
        $resultData = $this->post('registrar_rfc', $params);

        if (empty($resultData['success'])) {
            throw new BliqApiException($resultData['message'] ?? 'Error no especificado');
        }
    }

    /**
     * Realiza petición por método GET
     * @param string $endpoint Punto al que se hará la petición
     * @param array|null $params Parámetros a incluir en la petición
     * @return array Respuesta del servidor
     */
    public function get(string $endpoint, array $params = null): array
    {
        $finalUrl = self::API_BASE_URL . $endpoint;
        $finalUrl .= '?mode=' . $this->mode;

        if (!empty($params)) {
            $finalUrl .= '&' . http_build_query($params);
        }

        $ch = curl_init($finalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        return $this->executeRequest($ch);
    }

    /**
     * Realiza petición por método POST
     * @param string $endpoint Punto al que se hará la petición
     * @param array|null $data Datos a incluir en la petición
     * @return array Respuesta del servidor
     */
    public function post(string $endpoint, array $data = null): array
    {
        $finalUrl = self::API_BASE_URL . $endpoint;
        $finalUrl .= '?mode=' . $this->mode;

        $jsonDataStr = json_encode($data);

        $ch = curl_init($finalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonDataStr)
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataStr);

        return $this->executeRequest($ch);
    }

    private function executeRequest($ch): array
    {
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if (!empty($err)) {
            throw new BliqApiException('Respuesta no válida: ' . $err, 30);
        }

        $json = self::parseResponse($response);
        $this->assertIsSuccessResponse($json);

        return $json;
    }

    private static function parseResponse($response): array
    {
        if (null === $response || '' === $response) {
            throw new BliqApiException('Respuesta no válida: vacía', 31);
        }

        $json = json_decode($response, true);

        if (null === $json) {
            throw new BliqApiException('Respuesta no válida: ' . $response, 32);
        }

        return $json;
    }

    private function assertIsSuccessResponse(array $response)
    {
        if (true !== $response['success']) {
            // $errMsg = $response['error_message'] ?? 'Solicitud no exitosa';
            $errMsg = $response['error_message'] ?? $response['errors'][0]['description'] ?? 'Solicitud no exitosa';
            $exception = new BliqApiException($errMsg, 20);
            $exception->setErrors($response['errors'] ?? []);
            throw $exception;
        }
    }
}
