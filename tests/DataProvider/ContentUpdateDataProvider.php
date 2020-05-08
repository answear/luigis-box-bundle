<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;

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

    public static function provideSuccessPartialContentUpdateObjects(): iterable
    {
        $objects = [
            new PartialContentUpdate(
                'test.url',
                'products',
                [],
            ),
            new PartialContentUpdate(
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
        for ($i = 0; $i <= Request::getContentUpdateLimit(); ++$i) {
            $objects[] = new ContentUpdate(
                'test url title' . $i,
                'test.url' . $i,
                'products',
                []
            );
        }

        yield [new ContentUpdateCollection($objects)];
    }

    public static function provideAboveLimitPartialContentUpdateObjects(): iterable
    {
        $objects = [];
        for ($i = 0; $i <= Request::getPartialContentUpdateLimit(); ++$i) {
            $objects[] = new PartialContentUpdate(
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
            new ContentRemoval('test.url', 'product'),
            new ContentRemoval('test.url2', 'product'),
        ];

        yield [
            new ContentRemovalCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }
}
