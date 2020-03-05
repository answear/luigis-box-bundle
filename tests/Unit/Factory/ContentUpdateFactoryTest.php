<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateObjects;
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
        $configProvider = new ConfigProvider('host', 'key', 'key', 1, 1);
        $serializer = $this->createMock(LuigisBoxSerializer::class);
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($objects)
            ->willReturn('serialized');

        return new ContentUpdateFactory($configProvider, $serializer);
    }

    private function getObjects(): ContentUpdateObjects
    {
        $objects = [
            new ContentUpdate('test.url', 'type', ['title' => 't']),
            new ContentUpdate('test.url2', 'type', ['title' => 't2']),
        ];

        return new ContentUpdateObjects($objects);
    }
}
