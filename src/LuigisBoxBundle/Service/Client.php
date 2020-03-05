<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function request(Request $request): ResponseInterface
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->requestTimeout,
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->connectionTimeout,
            RequestOptions::HTTP_ERRORS => false,
        ];

        $client = new \GuzzleHttp\Client($options);

        return $client->send($request);
    }
}
