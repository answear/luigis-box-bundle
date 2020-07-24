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

class RequestDataProvider
{
    public function forContentUpdate(): iterable
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
            '{"objects":[{"url":"product\/2","type":"products","autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","fields":{"availability":1,"title":"title"}},{"url":"product\/1","type":"products","generation":"one","fields":{"availability":0,"title":"title"},"nested":[{"url":"product\/2","type":"products","autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","fields":{"availability":1,"title":"title"}}]}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    public function forPartialContentUpdate(): iterable
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
            '{"objects":[{"url":"product\/2","type":"products","active_to":"2019-12-12 00:01:02","fields":{"title":"title","availability":1}},{"url":"product\/1","type":"products","generation":"one","fields":{"title":"title","availability":0},"nested":[{"url":"product\/2","type":"products","active_to":"2019-12-12 00:01:02","fields":{"title":"title","availability":1}}]}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    public function forContentRemoval(): iterable
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

    public function forChangeAvailability(): iterable
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
}
