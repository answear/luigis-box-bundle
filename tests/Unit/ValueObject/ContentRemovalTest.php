<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use PHPUnit\Framework\TestCase;

class ContentRemovalTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ValueObjectDataProvider::provideContentRemovalObjects()
     */
    public function createObjectSuccessfully(string $url): void
    {
        $object = new ContentRemoval($url, '');

        $this->assertSame($url, $object->getUrl());
    }
}
