<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

class ContentUpdateFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'POST';
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
