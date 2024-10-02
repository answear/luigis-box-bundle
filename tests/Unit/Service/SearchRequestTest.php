<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Response\Search;
use Answear\LuigisBoxBundle\Service\SearchClient;
use Answear\LuigisBoxBundle\Service\SearchRequest;
use Answear\LuigisBoxBundle\Service\SearchRequestInterface;
use Answear\LuigisBoxBundle\Tests\DataProvider\SearchDataProvider;
use Answear\LuigisBoxBundle\ValueObject\Search\Context;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SearchRequestTest extends TestCase
{
    private const CACHE_TTL = 'cache-ttl';

    #[Test]
    #[DataProviderExternal(SearchDataProvider::class, 'provideSuccessObjects')]
    public function searchWithSuccess(SearchUrlBuilder $urlBuilder, array $arrayRawResponse): void
    {
        $requestService = $this->getRequestService($urlBuilder, $arrayRawResponse);
        $response = $requestService->search($urlBuilder);

        $this->assertStringContainsString(self::CACHE_TTL, $response->searchUrl);
        $this->assertSame($urlBuilder->toUrlQuery(), strstr($response->searchUrl, '&v=', true));
        $rawResults = $arrayRawResponse['results'];
        $this->assertSame($rawResults['query'], $response->query);
        $this->assertSame($rawResults['corrected_query'], $response->correctedQuery);
        $this->assertFiltersSame($rawResults['filters'], $response->filters);
        $this->assertHitsSame($rawResults['hits'], $response->hits);
        $this->assertHitsSame($rawResults['quicksearch_hits'], $response->quickSearchHits);
        $this->assertFacetsSame($rawResults['facets'], $response->facets);
        $this->assertSame($rawResults['total_hits'], $response->totalHits);
    }

    #[Test]
    public function searchWithErrors(): void
    {
        $this->expectException(BadRequestException::class);

        $urlBuilder = new SearchUrlBuilder(2);
        $context = new Context();
        $context->setGeoLocationField('geo_location');
        $urlBuilder->setContext($context);

        $requestService = $this->getRequestService(
            $urlBuilder,
            [
                'error' => 'Nonexisting attribute used: geo_location',
            ],
            400
        );

        $requestService->search($urlBuilder);
    }

    private function getRequestService(
        SearchUrlBuilder $searchUrlBuilder,
        array $apiResponse,
        int $responseStatus = 200,
    ): SearchRequestInterface {
        $guzzleRequest = new \GuzzleHttp\Psr7\Request(
            'POST',
            new Uri('some.url'),
            [],
            'ss'
        );

        $client = $this->createMock(SearchClient::class);
        $client->expects($this->once())
            ->method('request')
            ->with($guzzleRequest)
            ->willReturn(
                new Response(
                    $responseStatus,
                    [],
                    json_encode($apiResponse, JSON_THROW_ON_ERROR, 512)
                )
            );

        $searchFactory = $this->createMock(SearchFactory::class);
        $searchFactory->expects($this->once())
            ->method('prepareRequest')
            ->with($searchUrlBuilder)->willReturn($guzzleRequest);
        $searchFactory->expects($this->once())
            ->method('prepareRequestCacheHash')
            ->willReturn(self::CACHE_TTL);

        return new SearchRequest($client, $searchFactory);
    }

    private function assertFiltersSame(array $rawFilters, array $filters): void
    {
        foreach ($rawFilters as $rawFilter) {
            [$key, $value] = explode(':', $rawFilter);

            $this->assertNotNull($filters[$key]);
            if (\is_array($filters[$key])) {
                $this->assertContains($value, $filters[$key]);
            } else {
                $this->assertSame($value, $filters[$key]);
            }
        }
    }

    /**
     * @param Search\Hit[] $hits
     */
    private function assertHitsSame(array $rawHits, array $hits): void
    {
        $this->assertCount(\count($rawHits), $hits);

        foreach ($rawHits as $key => $rawHit) {
            $searchHit = $hits[$key];

            $this->assertSame($rawHit['url'], $searchHit->url);
            $this->assertSame($rawHit['attributes'], $searchHit->attributes);
            $this->assertSame($rawHit['nested'], $searchHit->nested);
            $this->assertSame($rawHit['type'], $searchHit->type);
            $this->assertSame($rawHit['exact'], $searchHit->exact);
            $this->assertSame($rawHit['alternative'], $searchHit->alternative);
        }
    }

    /**
     * @param Search\Facet[] $facets
     */
    private function assertFacetsSame(array $rawFacets, array $facets): void
    {
        $this->assertCount(\count($rawFacets), $facets);

        foreach ($rawFacets as $key => $rawFacet) {
            $searchFacet = $facets[$key];

            $this->assertSame($rawFacet['name'], $searchFacet->name);
            $this->assertSame('string', $searchFacet->type);
            $this->assertSame([], $searchFacet->values);
        }
    }
}
