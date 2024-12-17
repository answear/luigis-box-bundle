<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\Recommendation;
use Answear\LuigisBoxBundle\ValueObject\RecommendationsCollection;

class RequestDataProvider
{
    public static function forContentUpdate(): iterable
    {
        yield [
            'POST',
            new ContentUpdateCollection([new ContentUpdate('title', 'product/1', 'products', [])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $collection = new ContentUpdateCollection(
            [new ContentUpdate('title', 'product/1', 'products', ['availability' => 1])]
        );
        yield [
            'POST',
            $collection,
            '{"objects":[{"url":"product\/1","type":"products","fields":{"availability":1,"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $contentUpdate1 = new ContentUpdate('title', 'product/2', 'products', ['availability' => 1]);
        $contentUpdate1->setActiveTo('2019-12-12 00:01:02');
        $contentUpdate1->setAutocompleteType(
            [
                'categories',
                'other',
            ]
        );
        $contentUpdate2 = new ContentUpdate('title', 'product/1', 'products', ['availability' => 0]);
        $contentUpdate2->setGeneration('one');
        $contentUpdate2->setNested([$contentUpdate1]);
        $collection = new ContentUpdateCollection(
            [$contentUpdate1, $contentUpdate2]
        );
        yield [
            'POST',
            $collection,
            '{"objects":[{"autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"availability":1,"title":"title"}},{"generation":"one","nested":[{"autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"availability":1,"title":"title"}}],"url":"product\/1","type":"products","fields":{"availability":0,"title":"title"}}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    public static function forPartialContentUpdate(): iterable
    {
        yield [
            'PATCH',
            new ContentUpdateCollection([new PartialContentUpdate('product/1', 'products', ['brand' => 'brand name'])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"brand":"brand name"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        yield [
            'PATCH',
            new ContentUpdateCollection([new PartialContentUpdate('product/1', 'products', ['title' => 'title'])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $collection = new ContentUpdateCollection(
            [new PartialContentUpdate('product/1', 'products', ['title' => 'title', 'availability' => 1])]
        );
        yield [
            'PATCH',
            $collection,
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title","availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $contentUpdate1 = new PartialContentUpdate('product/2', 'products', ['title' => 'title', 'availability' => 1]);
        $contentUpdate1->setActiveTo('2019-12-12 00:01:02');
        $contentUpdate2 = new PartialContentUpdate('product/1', 'products', ['title' => 'title', 'availability' => 0]);
        $contentUpdate2->setGeneration('one');
        $contentUpdate2->setNested([$contentUpdate1]);
        $collection = new ContentUpdateCollection(
            [$contentUpdate1, $contentUpdate2]
        );
        yield [
            'PATCH',
            $collection,
            '{"objects":[{"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"title":"title","availability":1}},{"generation":"one","nested":[{"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"title":"title","availability":1}}],"url":"product\/1","type":"products","fields":{"title":"title","availability":0}}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    public static function forContentRemoval(): iterable
    {
        yield [
            'DELETE',
            new ContentRemovalCollection([new ContentRemoval('product/1', 'product')]),
            '{"objects":[{"url":"product\/1","type":"product"}]}',
            [
                'ok_count' => 1,
            ],
        ];
    }

    public static function forChangeAvailability(): iterable
    {
        yield [
            'PATCH',
            new ContentAvailabilityCollection([new ContentAvailability('product/1', true)]),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        yield [
            'PATCH',
            new ContentAvailabilityCollection(
                [new ContentAvailability('product/1', true), new ContentAvailability('product/2', false)]
            ),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}},{"url":"product\/2","fields":{"availability":0}}]}',
            [
                'ok_count' => 2,
            ],
        ];

        yield [
            'PATCH',
            new ContentAvailability('product/1', true),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];
    }

    public static function forRecommendationsRequest(): iterable
    {
        yield [
            'POST',
            new RecommendationsCollection([new Recommendation('user_conversion_based', '1234')]),
            '[{"recommendation_type":"user_conversion_based","user_id":"1234"}]',
            [
                [
                    'generated_at' => '2024-12-16T15:18:36.434588',
                    'hits' => [
                        [
                            'attributes' => [
                                'title' => 'Title',
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'url' => '/p/title-id',
                        ],
                    ],
                    'model_version' => null,
                    'recommendation_id' => '111',
                    'recommendation_type' => 'user_conversion_based',
                    'recommender' => 'user_conversion_based',
                    'recommender_client_identifier' => 'user_conversion_based',
                    'recommender_version' => '111',
                    'user_id' => '1234',
                ],
            ],
        ];

        yield [
            'POST',
            new RecommendationsCollection([new Recommendation('user_conversion_based', '1234', hitFields: ['url', 'title'])]),
            '[{"recommendation_type":"user_conversion_based","user_id":"1234","hit_fields":["url","title"]}]',
            [
                [
                    'generated_at' => '2024-12-16T15:18:36.434588',
                    'hits' => [
                        [
                            'attributes' => [
                                'title' => 'title 2',
                            ],
                            'nested' => [],
                            'type' => 'product',
                            'url' => '/p/title-2-id',
                        ],
                    ],
                    'model_version' => null,
                    'recommendation_id' => '111',
                    'recommendation_type' => 'user_conversion_based',
                    'recommender' => 'user_conversion_based',
                    'recommender_client_identifier' => 'user_conversion_based',
                    'recommender_version' => '111',
                    'user_id' => '1234',
                ],
            ],
        ];
    }
}
