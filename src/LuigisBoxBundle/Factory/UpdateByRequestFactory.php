<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;

class UpdateByRequestFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'PATCH';
    private const ENDPOINT = '/' . ConfigProvider::API_VERSION . '/update_by_query';

    protected function getHttpMethod(): string
    {
        return self::HTTP_METHOD;
    }

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
