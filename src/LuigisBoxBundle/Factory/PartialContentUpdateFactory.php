<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

class PartialContentUpdateFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'PATCH';
    private const ENDPOINT = '/v1/content';

    protected function getHttpMethod(): string
    {
        return self::HTTP_METHOD;
    }

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
