<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;

class ContentUpdateDataProvider
{
    public static function provideSuccessContentUpdateObjects(): iterable
    {
        $objects = [
            new ContentUpdate(
                'test url title',
                'test.url',
                'products',
                [],
            ),
            new ContentUpdate(
                'test url title',
                'test.url2',
                'categories',
                []
            ),
        ];

        yield [
            new ContentUpdateCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }

    public static function provideAboveLimitContentUpdateObjects(): iterable
    {
        $objects = [];
        for ($i = 0; $i <= 101; ++$i) {
            $objects[] = new ContentUpdate(
                'test url title' . $i,
                'test.url' . $i,
                'products',
                []
            );
        }

        yield [new ContentUpdateCollection($objects)];
    }

    public static function provideContentRemovalObjects(): iterable
    {
        $objects = [
            new ContentRemoval('test.url'),
            new ContentRemoval('test.url2'),
        ];

        yield [
            new ContentRemovalCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }
}
