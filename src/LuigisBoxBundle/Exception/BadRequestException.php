<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class BadRequestException extends \RuntimeException
{
    public function __construct(
        public readonly ResponseInterface $response,
        public readonly Request $request,
    ) {
        parent::__construct('Bad request.');
    }
}
