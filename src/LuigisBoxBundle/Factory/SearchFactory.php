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

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
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
            )
        );
    }
}
