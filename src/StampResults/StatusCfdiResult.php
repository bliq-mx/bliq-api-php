<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

class StatusCfdiResult
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $response)
    {
        $this->data = $response['data'];
    }

    public function codigoEstatus(): string
    {
        return $this->data['codigo_estatus'];
    }

    public function estado(): string
    {
        return $this->data['estado'];
    }

    public function esCancelable(): string
    {
        return $this->data['es_cancelable'];
    }

    public function estatusCancelacion(): string
    {
        return $this->data['estatus_cancelacion'];
    }
}
