<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

use DateTime;

class CancelCfdiResultV2
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
        return new DateTime($this->data['fecha']);
    }

    /**
     * @return string|null
     */
    public function xml()
    {
        return $this->data['xml'];
    }

    public function uuid(): string
    {
        return $this->data['uuid'];
    }

    public function estatusUuid(): string
    {
        return $this->data['estatus_uuid'];
    }

    public function estatusCancelacion(): string
    {
        return $this->data['estatus_cancelacion'];
    }
}
