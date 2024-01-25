<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

use DateTime;

class CancelCfdiResult
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $response)
    {
        $this->data = $response['data'];
    }

    public function date(): DateTime
    {
        return new DateTime($this->data['fecha_cancelado']);
    }

    /**
     * @return string|null
     */
    public function xml()
    {
        return $this->data['xml'];
    }

    public function peticionPrevia(): bool
    {
        return $this->data['peticion_previa'];
    }

    public function requiereAceptacion(): bool
    {
        return $this->data['requiere_aceptacion'];
    }

    public function esCancelable(): bool
    {
        return $this->data['es_cancelable'];
    }

    public function uuid(): string
    {
        return $this->data['uuid'];
    }
}
