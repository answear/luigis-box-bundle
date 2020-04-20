<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;

class ValueObjectDataProvider
{
    public static function provideContentUpdateObjects(): iterable
    {
        yield [
            'test.url',
            'products',
            [
                'title' => 'test url title',
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
        ];

        yield [
            'test.url2',
            null,
            [
                'title' => 'test url title',
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
            [
                'categories',
                'autocomplete type 2',
            ],
            'generation 1',
            [
                new ContentUpdate(
                    'title',
                    's',
                    'products',
                    []
                ),
            ],
        ];
    }

    public static function provideContentUpdateObjectsForException(): iterable
    {
        yield [
            'Field title can not be empty',
            'test.url',
            'products',
            [],
        ];

        yield [
            'Field availability must be one of [0, 1]',
            'test.url',
            'products',
            [
                'title' => 'title',
                'availability' => 3,
            ],
        ];

        yield [
            'Field availability_rank must be between 1 and 15',
            'test.url',
            'products',
            [
                'title' => 'title',
                'availability_rank' => 16,
            ],
        ];

        yield [
            'Expected an instance of Answear\LuigisBoxBundle\ValueObject\ContentUpdate. Got: Answear\LuigisBoxBundle\ValueObject\ContentRemoval',
            'test.url',
            'products',
            [
                'title' => 'title',
            ],
            null,
            null,
            [
                new ContentRemoval('', ''),
            ],
        ];
    }

    public static function provideContentRemovalObjects(): iterable
    {
        yield [
            'test.url',
        ];
    }
}
