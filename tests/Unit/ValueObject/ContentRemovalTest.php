<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContentRemovalTest extends TestCase
{
    #[Test]
    #[DataProvider('provideContentRemovalObjects')]
    public function createObjectSuccessfully(string $url): void
    {
        $object = new ContentRemoval($url, '');

        $this->assertSame($url, $object->url);
    }

    public static function provideContentRemovalObjects(): iterable
    {
        yield [
            'test.url',
        ];
    }
}
