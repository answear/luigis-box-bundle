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
        $this->assertSame('tracker_id=key&size=10&page=34&q=query-string', $request->getUri()->getQuery());

        $this->assertSame('', $request->getBody()->getContents());
    }

    private function getFactory(): SearchFactory
    {
        $configProvider = new ConfigProvider(
            'config_name',
            [
                'config_name' => [
                    'host' => 'host',
                    'publicKey' => 'key',
                    'privateKey' => 'key',
                    'connectionTimeout' => 5.0,
                    'requestTimeout' => 5.0,
                    'searchTimeout' => 2.0,
                ],
            ]
        );

        return new SearchFactory($configProvider);
    }

    private function getBuilderUrl(): SearchUrlBuilder
    {
        $builder = new SearchUrlBuilder(34);
        $builder->setQuery('query-string');

        return $builder;
    }
}
