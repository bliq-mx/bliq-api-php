<?php

declare(strict_types=1);

namespace Bliq\Api;

use Bliq\Api\Exception\BliqApiException;
use Bliq\Api\MessagingResults\SendResult;

class BliqMessagingApi
{
    /**
     * @var string URL de conexión a la API.
     */
    const API_BASE_URL = 'https://api.bliq.mx/messaging/';

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
     * Envía un SMS
     * @throws BliqApiException
     */
    public function sendSMS(string $number, string $message): SendResult
    {
        return $this->sendMessage([
            'type' => 'SMS',
            'number' => $number,
            'message' => $message,
            'token' => $this->token,
        ]);
    }

    /**
     * Envía un mensaje a WhatsApp
     * @throws BliqApiException
     */
    public function sendWhatsAppMessage(string $number, string $message): SendResult
    {
        return $this->sendMessage([
            'type' => 'WhatsApp',
            'number' => $number,
            'message' => $message,
            'token' => $this->token,
        ]);
    }

    /**
     * Envía un mensaje
     * @throws BliqApiException
     */
    public function sendMessage(array $data): SendResult
    {
        $response = $this->post('send', $data);
        return new SendResult($response);
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
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if (!empty($err)) {
            throw new BliqApiException('Respuesta no válida: ' . $err, 30);
        }

        $json = self::parseResponse($response);

        $this->assertIsSuccessResponse($httpCode, $json);

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

    private function assertIsSuccessResponse(int $httpCode, array $response)
    {
        if ($httpCode < 200 || $httpCode >= 300) {
            $errMsg = $response['error']['message'] ?? 'Solicitud no exitosa';
            throw new BliqApiException($errMsg, 20);
        }
    }
}
