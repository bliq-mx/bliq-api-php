<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

use DateTime;

class CreateCfdiResult
{
    /** @var string */
    private $uuid;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var string|null
     */
    private $xml;

    public function __construct(array $response)
    {
        $data = $response['data'];
        $this->uuid = $data['uuid'];
        $this->date = new DateTime($data['fecha_timbrado']);
        $this->xml = $data['xml'];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
