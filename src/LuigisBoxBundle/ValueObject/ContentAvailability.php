<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

class ContentAvailability
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $available;

    public function __construct(string $url, bool $available)
    {
        $this->url = $url;
        $this->available = $available;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }
}
