<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalObjects;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateObjects;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ContentUpdateDataProvider::provideSuccessContentUpdateObjects()
     */
    public function contentUpdateWithSuccess(ContentUpdateObjects $objects): void
    {
        $requestService = $this->getRequestServiceForContentUpdate($objects);
        $response = $requestService->contentUpdate($objects);

        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertSame('example response', $body);
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ContentUpdateDataProvider::provideSuccessContentUpdateObjects()
     */
    public function partialContentUpdateWithSuccess(ContentUpdateObjects $objects): void
    {
        $requestService = $this->getRequestServiceForPartialUpdate($objects);
        $response = $requestService->partialContentUpdate($objects);

        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertSame('example response', $body);
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ContentUpdateDataProvider::provideContentRemovalObjects()
     */
    public function contentRemovalWithSuccess(ContentRemovalObjects $objects): void
    {
        $requestService = $this->getRequestServiceForRemoval($objects);
        $response = $requestService->contentRemoval($objects);

        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        $this->assertSame('example response', $body);
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ContentUpdateDataProvider::provideAboveLimitContentUpdateObjects()
     */
    public function contentUpdateWithExceededLimit(ContentUpdateObjects $objects): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Expect less than or equal %s objects. Got %s.', 100, \count($objects)));

        $requestService = $this->getSimpleRequestService();
        $requestService->contentUpdate($objects);
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ContentUpdateDataProvider::provideAboveLimitContentUpdateObjects()
     */
    public function partialContentUpdateWithExceededLimit(ContentUpdateObjects $objects): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Expect less than or equal %s objects. Got %s.', 50, \count($objects)));

        $requestService = $this->getSimpleRequestService();
        $requestService->partialContentUpdate($objects);
    }

    private function getRequestServiceForContentUpdate(ObjectsInterface $objects): Request
    {
        $guzzleRequest = new \GuzzleHttp\Psr7\Request(
            'POST',
            new Uri('some.url'),
            [],
            'ss'
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with($guzzleRequest)
            ->willReturn(
                new Response(
                    200,
                    [],
                    'example response'
                )
            );

        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $contentUpdateFactory->expects($this->once())
            ->method('prepareRequest')
            ->with($objects)->willReturn($guzzleRequest);

        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request($client, $contentUpdateFactory, $partialContentUpdateFactory, $contentRemovalUpdateFactory);
    }

    private function getRequestServiceForPartialUpdate(ObjectsInterface $objects): Request
    {
        $guzzleRequest = new \GuzzleHttp\Psr7\Request(
            'POST',
            new Uri('some.url'),
            [],
            'ss'
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with($guzzleRequest)
            ->willReturn(
                new Response(
                    200,
                    [],
                    'example response'
                )
            );

        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $partialContentUpdateFactory->expects($this->once())
            ->method('prepareRequest')
            ->with($objects)->willReturn($guzzleRequest);

        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request(
            $client,
            $this->createMock(ContentUpdateFactory::class),
            $partialContentUpdateFactory,
            $contentRemovalUpdateFactory
        );
    }

    private function getRequestServiceForRemoval(ObjectsInterface $objects): Request
    {
        $guzzleRequest = new \GuzzleHttp\Psr7\Request(
            'POST',
            new Uri('some.url'),
            [],
            'ss'
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with($guzzleRequest)
            ->willReturn(
                new Response(
                    200,
                    [],
                    'example response'
                )
            );

        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);
        $contentRemovalUpdateFactory->expects($this->once())
            ->method('prepareRequest')
            ->with($objects)->willReturn($guzzleRequest);

        return new Request(
            $client,
            $this->createMock(ContentUpdateFactory::class),
            $this->createMock(PartialContentUpdateFactory::class),
            $contentRemovalUpdateFactory
        );
    }

    private function getSimpleRequestService(): Request
    {
        return new Request(
            $this->createMock(Client::class),
            $this->createMock(ContentUpdateFactory::class),
            $this->createMock(PartialContentUpdateFactory::class),
            $this->createMock(ContentRemovalFactory::class)
        );
    }
}
