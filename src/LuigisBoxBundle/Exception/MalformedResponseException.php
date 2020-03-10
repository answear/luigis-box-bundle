<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;

class MalformedResponseException extends \RuntimeException
{
    /**
     * @var string|null
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    public function __construct(string $message, ?string $response, Request $request, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->response = $response;
        $this->request = $request;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
