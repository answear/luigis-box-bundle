<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class MalformedResponseException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly ResponseInterface $response,
        public readonly Request $request,
        ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
