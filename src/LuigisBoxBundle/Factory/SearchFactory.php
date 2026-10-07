<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Webmozart\Assert\Assert;

class SearchFactory
{
    private const ENDPOINT = '/search';

    public function __construct(private ConfigProvider $configProvider)
    {
    }

    public function prepareRequest(SearchUrlBuilder $searchUrlBuilder): Request
    {
        $urlQuery = $searchUrlBuilder->toUrlQuery();
        Assert::notEmpty($urlQuery);

        $uri = new Uri(
            sprintf(
                '%s?tracker_id=%s&%s',
                $this->configProvider->getHost() . self::ENDPOINT,
                $this->configProvider->getPublicKey(),
                $urlQuery
            )
        );

        $body = $searchUrlBuilder->toRequestBody();
        if (null === $body) {
            return new Request('GET', $uri, $this->configProvider->headers);
        }

        return new Request(
            'POST',
            $uri,
            array_merge(
                array_filter(
                    $this->configProvider->headers,
                    static fn(string $name): bool => 'content-type' !== strtolower($name),
                    ARRAY_FILTER_USE_KEY
                ),
                ['Content-Type' => 'application/json']
            ),
            json_encode($body, JSON_THROW_ON_ERROR)
        );
    }

    public function prepareRequestCacheHash(): string
    {
        $requestTtl = $this->configProvider->getSearchCacheTtl();
        if (0 === $requestTtl) {
            return (string) time();
        }

        return (string) intdiv(time(), $requestTtl);
    }
}
