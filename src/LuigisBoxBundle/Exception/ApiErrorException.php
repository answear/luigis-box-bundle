<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;

class ApiErrorException extends \RuntimeException
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(string $message, Request $request)
    {
        parent::__construct($message);

        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
