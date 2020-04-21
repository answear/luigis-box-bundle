<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class BadRequestException extends \RuntimeException
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    public function __construct(ResponseInterface $response, Request $request)
    {
        parent::__construct('Bad request.');

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
