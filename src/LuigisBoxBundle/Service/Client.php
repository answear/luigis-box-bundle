<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use GuzzleHttp\RequestOptions;

class Client extends AbstractClient
{
    protected function getGuzzleClient(): \GuzzleHttp\Client
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->getRequestTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->getConnectionTimeout(),
            RequestOptions::HTTP_ERRORS => false,
        ];

        return new \GuzzleHttp\Client($options);
    }
}
