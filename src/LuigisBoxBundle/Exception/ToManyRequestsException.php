<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use Psr\Http\Message\ResponseInterface;

class ToManyRequestsException extends \RuntimeException
{
    /**
     * @var int
     */
    private $retryAfterSeconds;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(int $retryAfterSeconds, ResponseInterface $response)
    {
        parent::__construct(
            'To many requests. Check $retryAfterSeconds field to see how many seconds must wait before retrying the request.'
        );

        $this->retryAfterSeconds = $retryAfterSeconds;
        $this->response = $response;
    }

    public function getRetryAfterSeconds(): int
    {
        return $this->retryAfterSeconds;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
