<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\Tests\DataProvider\Faker\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use PHPUnit\Framework\TestCase;

class ContentUpdateFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function prepareRequestSuccessfully(): void
    {
        $objects = $this->getObjects();
        $factory = $this->getFactory($objects);

        $request = $factory->prepareRequest($objects);

        $this->assertSame('host/v1/content', $request->getUri()->getPath());

        $headers = $request->getHeaders();

        $this->assertArrayHasKey('date', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertSame(['application/json; charset=utf-8'], $headers['Content-Type']);

        $this->assertSame('serialized', $request->getBody()->getContents());
    }

    private function getFactory(ObjectsInterface $objects): ContentUpdateFactory
    {
        $serializer = $this->createMock(LuigisBoxSerializer::class);
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($objects)
            ->willReturn('serialized');

        return new ContentUpdateFactory(ExampleConfiguration::provideDefaultConfig(), $serializer);
    }

    private function getObjects(): ContentUpdateCollection
    {
        $objects = [
            new ContentUpdate('t', 'test.url', 'type', []),
            new ContentUpdate('t2', 'test.url2', 'type', []),
        ];

        return new ContentUpdateCollection($objects);
    }
}
