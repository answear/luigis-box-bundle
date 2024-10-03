<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\Service\RequestInterface;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RequestExceptionsTest extends TestCase
{
    #[Test]
    public function tooManyItemsExceptionThrows(): void
    {
        $this->expectException(TooManyItemsException::class);
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

    #[Test]
    public function tooManyRequestsExceptionThrows(): void
    {
        $this->expectException(TooManyRequestsException::class);
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

    #[Test]
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

    #[Test]
    public function serviceUnavailableThrows(): void
    {
        $this->expectException(ServiceUnavailableException::class);
        $this->expectExceptionMessage('bad transfer');

        $objects = new ContentUpdateCollection([new ContentUpdate('title', 'url', null, [])]);

        $service = $this->getServiceWithGuzzleException();
        $service->contentUpdate($objects);
    }

    private function getService(Response $expectedResponse): RequestInterface
    {
        $guzzleClient = $this->createMock(\GuzzleHttp\Client::class);
        $guzzleClient->expects(self::once())
            ->method('send')
            ->willReturn($expectedResponse);

        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request(
            new Client($this->createMock(ConfigProvider::class), $guzzleClient),
            $contentUpdateFactory,
            $partialContentUpdateFactory,
            $contentRemovalUpdateFactory
        );
    }

    private function getServiceWithGuzzleException(): RequestInterface
    {
        $guzzleClient = $this->createMock(\GuzzleHttp\Client::class);
        $guzzleClient->expects(self::once())
            ->method('send')
            ->willThrowException(new TransferException('bad transfer'));

        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request(
            new Client($this->createMock(ConfigProvider::class), $guzzleClient),
            $contentUpdateFactory,
            $partialContentUpdateFactory,
            $contentRemovalUpdateFactory
        );
    }
}
