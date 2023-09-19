<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class MalformedResponseException extends \RuntimeException
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    public function __construct(string $message, ResponseInterface $response, Request $request, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->response = $response;
        $this->request = $request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
