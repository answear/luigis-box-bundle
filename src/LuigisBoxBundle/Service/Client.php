<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use GuzzleHttp\Exception\GuzzleException;
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

    /**
     * @throws GuzzleException
     */
    public function request(Request $request): ResponseInterface
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->getRequestTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->getConnectionTimeout(),
            RequestOptions::HTTP_ERRORS => false,
        ];

        $client = new \GuzzleHttp\Client($options);

        return $client->send($request);
    }
}
