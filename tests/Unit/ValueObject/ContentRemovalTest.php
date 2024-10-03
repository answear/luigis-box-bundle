<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\Tests\DataProvider\ValueObjectDataProvider;
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContentRemovalTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(ValueObjectDataProvider::class, 'provideContentRemovalObjects')]
    public function createObjectSuccessfully(string $url): void
    {
        $object = new ContentRemoval($url, '');

        $this->assertSame($url, $object->url);
    }
}
