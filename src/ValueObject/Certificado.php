<?php

declare(strict_types=1);

namespace Bliq\Stamp\ValueObject;

class Certificado
{
    /**
     * @var string
     */
    private $cer;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string|null
     */
    private $passphrase;

    public function __construct(string $cer, string $key, string $passphrase = null)
    {
        $this->cer = $cer;
        $this->key = $key;
        $this->passphrase = $passphrase;
    }

    public function cer(): string
    {
        return $this->cer;
    }

    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return string|null
     */
    public function passphrase()
    {
        return $this->passphrase;
    }
}
