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

        return new Request(
            'GET',
            new Uri(
                sprintf(
                    '%s?tracker_id=%s&%s',
                    $this->configProvider->getHost() . self::ENDPOINT,
                    $this->configProvider->getPublicKey(),
                    $urlQuery
                )
            ),
            $this->configProvider->headers
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
