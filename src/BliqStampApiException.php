<?php

declare(strict_types=1);

namespace Bliq\Stamp;

use DomainException;

class BliqStampApiException extends DomainException
{
    /** @var array */
    private $errors = [];

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
