<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

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
        return new Request(
            'GET',
            new Uri($this->configProvider->getHost() . self::ENDPOINT . '?' . $searchUrlBuilder->toUrlQuery())
        );
    }
}
