<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

class ContentRemovalFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'DELETE';
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
