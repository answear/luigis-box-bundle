<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use PHPUnit\Framework\TestCase;

class SearchFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function prepareRequestSuccessfully(): void
    {
        $builderUrl = $this->getBuilderUrl();
        $factory = $this->getFactory();

        $request = $factory->prepareRequest($builderUrl);

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('host/search', $request->getUri()->getPath());
        $this->assertSame('size=10&tracker_id=tracker-id&page=34&q=query-string', $request->getUri()->getQuery());

        $this->assertSame('', $request->getBody()->getContents());
    }

    private function getFactory(): SearchFactory
    {
        $configProvider = new ConfigProvider('host', 'key', 'key', 1, 1);

        return new SearchFactory($configProvider);
    }

    private function getBuilderUrl(): SearchUrlBuilder
    {
        $builder = new SearchUrlBuilder('tracker-id', 34);
        $builder->setQuery('query-string');

        return $builder;
    }
}
