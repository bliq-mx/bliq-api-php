<?php

declare(strict_types=1);

namespace Bliq\Api\StampResults;

class FetchCfdiResult
{
    /**
     * @var string|null
     */
    private $xml;

    public function __construct(array $response)
    {
        $this->xml = $response['data'];
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
