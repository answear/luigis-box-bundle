<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

class ContentRemoval
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $url, string $type)
    {
        $this->url = $url;
        $this->type = $type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
