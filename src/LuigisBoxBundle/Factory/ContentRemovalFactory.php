<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;

class ContentRemovalFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'DELETE';
    private const ENDPOINT = '/' . ConfigProvider::API_VERSION . '/content';

    protected function getHttpMethod(): string
    {
        return self::HTTP_METHOD;
    }

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
