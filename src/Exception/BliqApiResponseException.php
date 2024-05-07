<?php

declare(strict_types=1);

namespace Bliq\Api\Exception;

use DomainException;
use Throwable;

class BliqApiResponseException extends BliqApiException
{
    /** @var array */
    private $responseBody;

    public function __construct(
        string $message,
        int $httpCode,
        array $responseBody,
        Throwable $previous = null
    ) {
        $this->responseBody = $responseBody;
        parent::__construct($message, $httpCode, $previous);
    }

    public function httpCode(): int
    {
        return $this->getCode();
    }

    public function responseBody(): array
    {
        return $this->responseBody;
    }
}
