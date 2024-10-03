<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use Psr\Http\Message\ResponseInterface;

class TooManyRequestsException extends \RuntimeException
{
    public function __construct(
        public readonly int $retryAfterSeconds,
        public readonly ResponseInterface $response,
    ) {
        parent::__construct(
            'To many requests. Check $retryAfterSeconds field to see how many seconds must wait before retrying the request.'
        );
    }
}
