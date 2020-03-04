<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DTO;

class ContentRemoval
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
