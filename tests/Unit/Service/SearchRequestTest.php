<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Response\Search;
use Answear\LuigisBoxBundle\Service\SearchClient;
use Answear\LuigisBoxBundle\Service\SearchRequest;
use Answear\LuigisBoxBundle\Service\SearchRequestInterface;
use Answear\LuigisBoxBundle\ValueObject\Search\Context;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class SearchRequestTest extends TestCase
{
    private const CACHE_TTL = 'cache-ttl';

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\SearchDataProvider::provideSuccessObjects()
     */
    public function searchWithSuccess(SearchUrlBuilder $urlBuilder, array $arrayRawResponse): void
    {
        $requestService = $this->getRequestService($urlBuilder, $arrayRawResponse);
        $response = $requestService->search($urlBuilder);

        $this->assertStringContainsString(self::CACHE_TTL, $response->getSearchUrl());
        $this->assertSame($urlBuilder->toUrlQuery(), strstr($response->getSearchUrl(), '&v=', true));
        $rawResults = $arrayRawResponse['results'];
        $this->assertSame($rawResults['query'], $response->getQuery());
        $this->assertSame($rawResults['corrected_query'], $response->getCorrectedQuery());
        $this->assertFiltersSame($rawResults['filters'], $response->getFilters());
        $this->assertHitsSame($rawResults['hits'], $response->getHits());
        $this->assertHitsSame($rawResults['quicksearch_hits'], $response->getQuickSearchHits());
        $this->assertFacetsSame($rawResults['facets'], $response->getFacets());
        $this->assertSame($rawResults['total_hits'], $response->getTotalHits());
    }

    /**
     * @test
     */
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
        int $responseStatus = 200
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

            $this->assertSame($rawHit['url'], $searchHit->getUrl());
            $this->assertSame($rawHit['attributes'], $searchHit->getAttributes());
            $this->assertSame($rawHit['nested'], $searchHit->getNested());
            $this->assertSame($rawHit['type'], $searchHit->getType());
            $this->assertSame($rawHit['exact'], $searchHit->isExact());
            $this->assertSame($rawHit['alternative'], $searchHit->isAlternative());
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

            $this->assertSame($rawFacet['name'], $searchFacet->getName());
            $this->assertSame('string', $searchFacet->getType());
            $this->assertSame([], $searchFacet->getValues());
        }
    }
}
