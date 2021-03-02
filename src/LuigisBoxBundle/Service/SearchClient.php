<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use GuzzleHttp\RequestOptions;

class SearchClient extends AbstractClient
{
    protected function getGuzzleClient(): \GuzzleHttp\Client
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->getSearchTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->getConnectionTimeout(),
            RequestOptions::HTTP_ERRORS => false,
        ];

        return new \GuzzleHttp\Client($options);
    }
}
