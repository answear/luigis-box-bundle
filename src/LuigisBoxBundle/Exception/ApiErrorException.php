<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;

class ApiErrorException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly Request $request)
    {
        parent::__construct($message);
    }
}
