<?php

declare(strict_types=1);

namespace Bliq\Api\MessagingResults;

class SendResult
{
    /** @var string */
    private $id;

    /** @var string */
    private $message;

    /** @var string */
    private $recipient;

    public function __construct(array $response)
    {
        $data = $response['data'];
        $this->id = (string) $data['id'];
        $this->message = $data['message'];
        $this->recipient = $data['recipient'];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function recipient(): string
    {
        return $this->recipient;
    }
}
