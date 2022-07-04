<?php

declare(strict_types=1);

namespace Bliq\Stamp\Result;

class CrearPdfResult
{
    /** @var string */
    private $data;

    public function __construct(array $response)
    {
        $this->data = base64_decode($response['data']);
    }

    /**
     * @return string
     */
    public function data()
    {
        return $this->data;
    }
}
