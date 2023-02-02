<?php

declare(strict_types=1);

namespace Bliq\Api\ValueObject;

class WhatsAppTemplate
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $language;

    /**
     * @var array
     */
    private $components;

    public function __construct(string $name, string $language, array $components = [])
    {
        $this->name = $name;
        $this->language = $language;
        $this->components = $components;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function components(): array
    {
        return $this->components;
    }
}
