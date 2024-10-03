<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PartialContentUpdateFactoryTest extends TestCase
{
    #[Test]
    #[DataProvider('provideAvailabilityObjects')]
    public function prepareRequestSuccessfully($objects): void
    {
        $factory = $this->getFactory();

        $request = $factory->prepareRequestForAvailability($objects);

        $this->assertSame('host/v1/content', $request->getUri()->getPath());

        $headers = $request->getHeaders();

        $this->assertArrayHasKey('date', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertSame(['application/json; charset=utf-8'], $headers['Content-Type']);

        $this->assertSame('serialized', $request->getBody()->getContents());
    }

    public static function provideAvailabilityObjects(): iterable
    {
        yield [new ContentAvailability('url', true)];

        yield [new ContentAvailability('url', false)];

        yield [new ContentAvailabilityCollection([new ContentAvailability('url', false)])];
    }

    private function getFactory(): PartialContentUpdateFactory
    {
        $serializer = $this->createMock(LuigisBoxSerializer::class);
        $serializer->expects($this->once())
            ->method('serialize')
            ->willReturn('serialized');

        return new PartialContentUpdateFactory(ExampleConfiguration::provideDefaultConfig(), $serializer);
    }
}
