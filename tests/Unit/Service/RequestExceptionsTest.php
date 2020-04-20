<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\ToManyItemsException;
use Answear\LuigisBoxBundle\Exception\ToManyRequestsException;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RequestExceptionsTest extends TestCase
{
    /**
     * @test
     */
    public function toManyItemsExceptionThrows(): void
    {
        $this->expectException(ToManyItemsException::class);
        $this->expectExceptionMessage('To many items in single request.');

        $objects = new ContentUpdateCollection([new ContentUpdate('title', 'url', null, [])]);
        $service = $this->getService(
            new Response(
                \Symfony\Component\HttpFoundation\Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                [],
                json_encode(['example response'], JSON_THROW_ON_ERROR, 512)
            )
        );
        $service->contentUpdate($objects);
    }

    /**
     * @test
     */
    public function toManyRequestsExceptionThrows(): void
    {
        $this->expectException(ToManyRequestsException::class);
        $this->expectExceptionMessage(
            'To many requests. Check $retryAfterSeconds field to see how many seconds must wait before retrying the request.'
        );

        $objects = new ContentUpdateCollection([new ContentUpdate('title', 'url', null, [])]);
        $service = $this->getService(
            new Response(
                \Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS,
                [],
                json_encode(['example response'], JSON_THROW_ON_ERROR, 512)
            )
        );
        $service->contentUpdate($objects);
    }

    /**
     * @test
     */
    public function malformedResponseThrows(): void
    {
        $this->expectException(MalformedResponseException::class);
        $this->expectExceptionMessage(
            'Expected an array. Got: string'
        );

        $objects = new ContentUpdateCollection([new ContentUpdate('title', 'url', null, [])]);
        $service = $this->getService(
            new Response(
                200,
                [],
                json_encode('not json', JSON_THROW_ON_ERROR, 512)
            )
        );
        $service->contentUpdate($objects);
    }

    /**
     * @test
     */
    public function serviceUnavailableThrows(): void
    {
        $this->expectException(ServiceUnavailableException::class);
        $this->expectExceptionMessage('bad transfer');

        $objects = new ContentUpdateCollection([new ContentUpdate('title', 'url', null, [])]);

        $service = $this->getServiceWithGuzzleException();
        $service->contentUpdate($objects);
    }

    private function getService(Response $expectedResponse): Request
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request($client, $contentUpdateFactory, $partialContentUpdateFactory, $contentRemovalUpdateFactory);
    }

    private function getServiceWithGuzzleException(): Request
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willThrowException(new TransferException('bad transfer'));

        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request($client, $contentUpdateFactory, $partialContentUpdateFactory, $contentRemovalUpdateFactory);
    }
}
