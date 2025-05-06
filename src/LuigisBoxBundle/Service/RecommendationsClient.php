<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use GuzzleHttp\RequestOptions;

class RecommendationsClient extends AbstractClient
{
    protected function getGuzzleClient(): \GuzzleHttp\Client
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->getRecommendationsRequestTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->getRecommendationsConnectionTimeout(),
            RequestOptions::HTTP_ERRORS => false,
        ];

        return new \GuzzleHttp\Client($options);
    }
}
