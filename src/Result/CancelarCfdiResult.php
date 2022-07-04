<?php

declare(strict_types=1);

namespace Bliq\Stamp\Result;

use DateTime;

class CancelarCfdiResult
{
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
        $this->date = new DateTime($data['fecha_cancelado']);
        $this->xml = $data['xml'] ?? null;
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function xml()
    {
        return $this->xml;
    }
}
