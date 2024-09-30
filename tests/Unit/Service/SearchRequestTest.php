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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SearchRequestTest extends TestCase
{
    private const CACHE_TTL = 'cache-ttl';

    #[Test]
    #[DataProvider('provideSuccessObjects')]
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

    public static function provideSuccessObjects(): iterable
    {
        $urlBuilder = new SearchUrlBuilder(2);
        $urlBuilder->addFilter('type', 'product');
        $urlBuilder->setQuicksearchTypes(['category']);

        yield [
            $urlBuilder,
            [
                'results' => [
                    'query' => '',
                    'corrected_query' => null,
                    'filters' => [
                        'type:product',
                    ],
                    'hits' => [
                        [
                            'url' => '/p/cardio-bunny-stroj-kapielowy-sahara-swimsuit-77',
                            'attributes' => [
                                'id' => [
                                    0 => 77,
                                ],
                                'attributes' => [
                                    0 => 'Jeans',
                                    1 => 'Krótki',
                                ],
                                'price' => 23,
                                'price_amount' => 23.3,
                                'category' => [
                                    0 => 'On',
                                    1 => 'Odzież',
                                    2 => 'Spodnie',
                                ],
                                'id_count' => 1,
                                'attributes_count' => 2,
                                'category_count' => 3,
                                'boosted_via' => [],
                                'title' => 'Cardio Bunny - Strój kąpielowy Sahara Swimsuit',
                                'boosted_via_count' => 0,
                                'original_url' => '/p/cardio-bunny-stroj-kapielowy-sahara-swimsuit-77',
                                'boost' => 0,
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'highlight' => [],
                            'exact' => true,
                            'alternative' => false,
                        ],
                        [
                            'url' => '/p/cardio-bunny-top-sportowy-rio-17',
                            'attributes' => [
                                'id' => [
                                    0 => 17,
                                ],
                                'category' => [
                                    0 => 'On',
                                    1 => 'Odzież',
                                    2 => 'Top',
                                ],
                                'id_count' => 1,
                                'category_count' => 3,
                                'boosted_via' => [
                                    0 => 'item',
                                ],
                                'title' => 'Cardio Bunny - Top sportowy Rio',
                                'original_url' => '/p/cardio-bunny-top-sportowy-rio-17',
                                'boost' => 1,
                                'boosted_via_count' => 1,
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'highlight' => [],
                            'exact' => true,
                            'alternative' => false,
                        ],
                        [
                            'url' => '/p/medicine-fugit-72',
                            'attributes' => [
                                'id' => [
                                    0 => 72,
                                ],
                                'id_count' => 1,
                                'brand' => [
                                    0 => 'Changed Name',
                                ],
                                'brand_count' => 1,
                                'boosted_via' => [],
                                'title' => 'Fugit',
                                'original_url' => '/p/medicine-fugit-72',
                                'boost' => 0,
                                'boosted_via_count' => 0,
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'highlight' => [],
                            'exact' => true,
                            'alternative' => false,
                        ],
                        [
                            'url' => '/p/medicine-sit-73',
                            'attributes' => [
                                'id' => [
                                    0 => 73,
                                ],
                                'id_count' => 1,
                                'availability' => 0,
                                'brand' => [
                                    0 => 'Changed Name',
                                ],
                                'brand_count' => 1,
                                'boosted_via' => [],
                                'title' => 'Sit',
                                'original_url' => '/p/medicine-sit-73',
                                'boost' => 0,
                                'boosted_via_count' => 0,
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'highlight' => [],
                            'exact' => true,
                            'alternative' => false,
                        ],
                    ],
                    'quicksearch_hits' => [
                        [
                            'url' => '/c/fila-mikina-1407',
                            'attributes' => [
                                'test' => ['test'],
                                'test_count' => 1,
                                'boosted_via' => [],
                                'title' => 'Kategoria - Fila - Mikina',
                                'original_url' => 'https://beta-sk.softwear.co/c/fila-mikina-1407',
                                'boost' => 0,
                                'boosted_via_count' => 0,
                            ],
                            'nested' => [],
                            'type' => 'category',
                            'highlight' => null,
                            'exact' => true,
                            'alternative' => false,
                        ],
                    ],
                    'facets' => [],
                    'total_hits' => 17,
                    'offset' => '4',
                ],
                'next_page' => 'https://live.luigisbox.com/search?tracker_id=111111-222222&f[]=type:product&quicksearch_types=category&page=2',
            ],
        ];

        $urlBuilder = new SearchUrlBuilder();
        $urlBuilder->setQuery('fila');

        yield [
            $urlBuilder,
            [
                'results' => [
                    'query' => 'fila',
                    'corrected_query' => null,
                    'filters' => [],
                    'hits' => [
                        [
                            'url' => '/c/fila-mikina-1407',
                            'attributes' => [
                                'test' => [
                                    0 => 'test',
                                ],
                                'test_count' => 1,
                                'boosted_via' => [
                                ],
                                'title' => 'Kategoria - Fila - Mikina',
                                'original_url' => 'https://beta-sk.softwear.co/c/fila-mikina-1407',
                                'boost' => 0,
                                'boosted_via_count' => 0,
                            ],
                            'nested' => [],
                            'type' => 'category',
                            'highlight' => [],
                            'exact' => true,
                            'alternative' => false,
                        ],
                    ],
                    'quicksearch_hits' => [],
                    'facets' => [],
                    'total_hits' => 1,
                ],
                'next_page' => null,
            ],
        ];

        $urlBuilder = new SearchUrlBuilder();
        $urlBuilder->setQuery('fila');
        $urlBuilder->addFilter('price', '5|2');

        yield [
            $urlBuilder,
            [
                'results' => [
                    'query' => 'fila',
                    'corrected_query' => null,
                    'filters' => [
                        'price:5|2',
                    ],
                    'hits' => [],
                    'quicksearch_hits' => [],
                    'facets' => [],
                    'total_hits' => 0,
                ],
                'next_page' => null,
            ],
        ];
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
