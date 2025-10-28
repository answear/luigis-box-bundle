<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

class SearchDataProvider
{
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
        $urlBuilder->setQuery('用户充值记录用');

        yield [
            $urlBuilder,
            [
                'results' => [
                    'query' => null,
                    'corrected_query' => null,
                    'filters' => [],
                    'filters_negative' => [],
                    'accepted_filters' => [],
                    'hits' => [],
                    'quicksearch_hits' => [],
                    'facets' => [],
                    'suggested_facet' => 'null',
                    'total_hits' => 0,
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
}
