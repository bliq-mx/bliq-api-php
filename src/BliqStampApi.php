<?php

declare(strict_types=1);

namespace Bliq\Stamp;

use Bliq\Stamp\Result\CancelarCfdiResult;
use Bliq\Stamp\Result\CrearCfdiResult;
use Bliq\Stamp\Result\CrearPdfResult;
use Bliq\Stamp\Result\FirmaManifiestoResult;
use Bliq\Stamp\Result\RecuperarCfdiResult;
use Bliq\Stamp\ValueObject\Certificado;

class BliqStampApi
{
    /**
     * @var string URL de conexión a la API.
     */
    const API_BASE_URL = 'https://api.bliq.mx/timbrado/';

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
            throw new BliqStampApiException('El token no ha sido establecido.', 10);
        }
    }

    /**
     * Crea y obtiene el XML de un CFDI
     * @throws BliqStampApiException
     */
    public function crearXml40(array $data): array
    {
        return $this->post('crear_xml', $data);
    }

    /**
     * Realiza la creación y el timbrado de un CFDI utilizando los datos del certificado.
     * @throws BliqStampApiException
     */
    public function createCfdi40WithCertFiles(array $comprobante, Certificado $certificado): CrearCfdiResult
    {
        $params = [
            'Comprobante' => $comprobante,
            'cer_data' => $certificado->cer(),
            'key_data' => $certificado->key(),
            'key_passphrase' => $certificado->passphrase(),
        ];
        $response = $this->post('crear_cfdi', $params);
        return new CrearCfdiResult($response);
    }

    /**
     * Realiza la creación y el timbrado de un CFDI utilizando el número de certificado.
     * @throws BliqStampApiException
     */
    public function createCfdi40WithCertNumber(array $comprobante, string $certNumber, string $rfc): CrearCfdiResult
    {
        $params = [
            'Comprobante' => $comprobante,
            'numero' => $certNumber,
            'rfc' => $rfc,
        ];
        $response = $this->post('crear_cfdi', $params);
        return new CrearCfdiResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante el UUID
     * @throws BliqStampApiException
     */
    public function createPdfByUUID(string $uuid, array $params = []): CrearPdfResult
    {
        $params['uuid'] = $uuid;
        $response = $this->post('crear_pdf', $params);
        return new CrearPdfResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante el XML
     * @throws BliqStampApiException
     */
    public function createPdfByXML(string $xml, array $params = []): CrearPdfResult
    {
        $params['xml'] = $xml;
        $response = $this->post('crear_pdf', $params);
        return new CrearPdfResult($response);
    }

    /**
     * Obtiene el PDF de un XML mediante los datos para crear un CFDI
     * @throws BliqStampApiException
     */
    public function createPdfByData(array $comprobante, array $params = []): CrearPdfResult
    {
        $params['Comprobante'] = $comprobante;
        $response = $this->post('crear_pdf', $params);
        return new CrearPdfResult($response);
    }

    /**
     * Realiza la recuperación de un CFDI
     * @throws BliqStampApiException
     */
    public function getXmlByUUID(string $uuid): RecuperarCfdiResult
    {
        $data = ['uuid' => $uuid];
        $response = $this->post('recuperar_cfdi', $data);
        return new RecuperarCfdiResult($response);
    }

    /**
     * Realiza la cancelación de un CFDI
     * @throws BliqStampApiException
     */
    public function cancelCfdi(string $uuid, string $motivo, string $folioSustitucion, Certificado $certData): CancelarCfdiResult
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
        return new CancelarCfdiResult($response);
    }

    /**
     * Realiza la firma del manifiesto
     * @throws BliqStampApiException
     */
    public function firmarManifiesto(Certificado $certificado)
    {
        $data = [
            'cer_data' => base64_encode($certificado->cer()),
            'key_data' => base64_encode($certificado->key()),
            'key_passphrase' => $certificado->passphrase(),
        ];
        $resultData = $this->post('firmar_manifiesto', $data);

        if (empty($resultData['success'])) {
            throw new BliqStampApiException($resultData['message'] ?? 'Error no especificado');
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
            throw new BliqStampApiException('Respuesta no válida: ' . $err, 30);
        }

        $json = self::parseResponse($response);
        $this->assertIsSuccessResponse($json);

        return $json;
    }

    private static function parseResponse($response): array
    {
        if (null === $response || '' === $response) {
            throw new BliqStampApiException('Respuesta no válida: vacía', 31);
        }

        $json = json_decode($response, true);

        if (null === $json) {
            throw new BliqStampApiException('Respuesta no válida: ' . $response, 32);
        }

        return $json;
    }

    private function assertIsSuccessResponse(array $response)
    {
        if (true !== $response['success']) {
            // $errMsg = $response['error_message'] ?? 'Solicitud no exitosa';
            $errMsg = $response['error_message'] ?? $response['errors'][0]['description'] ?? 'Solicitud no exitosa';
            $exception = new BliqStampApiException($errMsg, 20);
            $exception->setErrors($response['errors'] ?? []);
            throw $exception;
        }
    }
}
