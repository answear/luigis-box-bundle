<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

//TODO make this class more usable
class ApiResponse
{
    /**
     * @var array
     */
    private $response;

    private function __construct(array $response)
    {
        $this->response = $response;
    }

    public static function fromArray(array $response): self
    {
        return new self($response);
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
