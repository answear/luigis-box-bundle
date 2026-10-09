<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SearchFactoryTest extends TestCase
{
    #[Test]
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

    #[Test]
    public function prepareRequestWithBodyFiltersAsPost(): void
    {
        $builderUrl = $this->getBuilderUrl();
        $builderUrl->addFilter('type', 'product');
        $builderUrl->addBodyFilterGroup('product', ['attributes.id' => [1016, 41411]]);
        $builderUrl->addBodyFilterGroup('product', ['attributes.id' => [1476]]);

        $request = $this->getFactory()->prepareRequest($builderUrl);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('host/search', $request->getUri()->getPath());
        $this->assertSame(
            'tracker_id=public-key&size=10&page=34&q=query-string&f%5B%5D=type%3Aproduct',
            $request->getUri()->getQuery()
        );
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame(
            '{"filters":{"product":{"and":[{"or":[{"filter":"attributes.id:1016"},{"filter":"attributes.id:41411"}]},{"or":[{"filter":"attributes.id:1476"}]}]}}}',
            $request->getBody()->getContents()
        );
    }

    #[Test]
    public function postRequestOverridesConfiguredContentTypeHeader(): void
    {
        $configProvider = ExampleConfiguration::provideDefaultConfig();
        $configProvider->setHeader('content-type', 'text/plain');
        $configProvider->setHeader('X-Custom', 'custom-value');

        $builderUrl = $this->getBuilderUrl();
        $builderUrl->addBodyFilterGroup('product', ['attributes.id' => [1016]]);

        $request = (new SearchFactory($configProvider))->prepareRequest($builderUrl);

        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame('custom-value', $request->getHeaderLine('X-Custom'));
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
