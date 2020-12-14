<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class Client
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct(ConfigProvider $configProvider, ?\GuzzleHttp\Client $client = null)
    {
        $this->configProvider = $configProvider;
        $this->client = $client ?? $this->getGuzzleClient();
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     */
    public function request(Request $request): ResponseInterface
    {
        try {
            $response = $this->client->send($request);

            $this->throwOnResponseErrors($request, $response);
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    private function throwOnResponseErrors(Request $request, ResponseInterface $response): void
    {
        if (Response::HTTP_BAD_REQUEST === $response->getStatusCode()) {
            throw new BadRequestException($response, $request);
        }
        if (Response::HTTP_TOO_MANY_REQUESTS === $response->getStatusCode()) {
            $retryAfter = $response->getHeader('Retry-After');
            $retryAfter = reset($retryAfter);
            throw new TooManyRequestsException((int) $retryAfter, $response);
        }
        if (Response::HTTP_REQUEST_ENTITY_TOO_LARGE === $response->getStatusCode()) {
            throw new TooManyItemsException(null, null, $response);
        }
        if (Response::HTTP_BAD_REQUEST <= $response->getStatusCode()) {
            throw RequestException::create($request, $response);
        }
    }

    private function getGuzzleClient(): \GuzzleHttp\Client
    {
        $options = [
            RequestOptions::TIMEOUT => $this->configProvider->getRequestTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $this->configProvider->getConnectionTimeout(),
            RequestOptions::HTTP_ERRORS => false,
        ];

        return new \GuzzleHttp\Client($options);
    }
}
