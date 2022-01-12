<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Tests\DataProvider\Faker\ExampleConfiguration;
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
        $this->assertSame('tracker_id=public-key&size=10&page=34&q=query-string', $request->getUri()->getQuery());

        $this->assertSame('', $request->getBody()->getContents());
    }

    private function getFactory(): SearchFactory
    {
        return new SearchFactory(ExampleConfiguration::provideDefaultConfig());
    }

    private function getBuilderUrl(): SearchUrlBuilder
    {
        $builder = new SearchUrlBuilder(34);
        $builder->setQuery('query-string');

        return $builder;
    }
}
