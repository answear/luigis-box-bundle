<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Factory;

use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use PHPUnit\Framework\TestCase;

class PartialContentUpdateFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideAvailabilityObjects
     */
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

    public function provideAvailabilityObjects(): iterable
    {
        yield [new ContentAvailability('url', true)];

        yield [new ContentAvailability('url', false)];

        yield [new ContentAvailabilityCollection([new ContentAvailability('url', false)])];
    }

    private function getFactory(): PartialContentUpdateFactory
    {
        $configProvider = new ConfigProvider(
            'config_name',
            [
                'config_name' => [
                    'host' => 'host',
                    'publicKey' => '',
                    'privateKey' => '',
                    'connectionTimeout' => 5.0,
                    'requestTimeout' => 5.0,
                    'searchTimeout' => 2.0,
                ],
            ]
        );

        $serializer = $this->createMock(LuigisBoxSerializer::class);
        $serializer->expects($this->once())
            ->method('serialize')
            ->willReturn('serialized');

        return new PartialContentUpdateFactory($configProvider, $serializer);
    }
}
