<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\Service\RequestInterface;
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    #[Test]
    #[DataProvider('provideSuccessContentUpdateObjects')]
    public function contentUpdateWithSuccess(ContentUpdateCollection $objects, array $apiResponse): void
    {
        $requestService = $this->getRequestServiceForContentUpdate($objects, $apiResponse);
        $response = $requestService->contentUpdate($objects);

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($objects), $response->okCount);
        $this->assertSame(0, $response->errorsCount);
        $this->assertSame([], $response->errors);
    }

    public static function provideSuccessContentUpdateObjects(): iterable
    {
        $objects = [
            new ContentUpdate(
                'test url title',
                'test.url',
                'products',
                [],
            ),
            new ContentUpdate(
                'test url title',
                'test.url2',
                'categories',
                []
            ),
        ];

        yield [
            new ContentUpdateCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }

    #[Test]
    #[DataProvider('provideSuccessPartialContentUpdateObjects')]
    public function partialContentUpdateWithSuccess(ContentUpdateCollection $objects, array $apiResponse): void
    {
        $requestService = $this->getRequestServiceForPartialUpdate($objects, $apiResponse);
        $response = $requestService->partialContentUpdate($objects);

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($objects), $response->okCount);
        $this->assertSame(0, $response->errorsCount);
        $this->assertSame([], $response->errors);
    }

    public static function provideSuccessPartialContentUpdateObjects(): iterable
    {
        $objects = [
            new PartialContentUpdate(
                'test.url',
                'products',
                [],
            ),
            new PartialContentUpdate(
                'test.url2',
                'categories',
                []
            ),
        ];

        yield [
            new ContentUpdateCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }

    #[Test]
    #[DataProvider('provideContentRemovalObjects')]
    public function contentRemovalWithSuccess(ContentRemovalCollection $objects, array $apiResponse): void
    {
        $requestService = $this->getRequestServiceForRemoval($objects, $apiResponse);
        $response = $requestService->contentRemoval($objects);

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($objects), $response->okCount);
        $this->assertSame(0, $response->errorsCount);
        $this->assertSame([], $response->errors);
    }

    public static function provideContentRemovalObjects(): iterable
    {
        $objects = [
            new ContentRemoval('test.url', 'product'),
            new ContentRemoval('test.url2', 'product'),
        ];

        yield [
            new ContentRemovalCollection($objects),
            [
                'ok_count' => 2,
            ],
        ];
    }

    #[Test]
    #[DataProvider('provideAboveLimitContentUpdateObjects')]
    public function contentUpdateWithExceededLimit(ContentUpdateCollection $objects): void
    {
        $this->expectException(TooManyItemsException::class);
        $this->expectExceptionMessage(sprintf('Expect less than or equal %s items. Got %s.', 100, \count($objects)));

        $requestService = $this->getSimpleRequestService();
        $requestService->contentUpdate($objects);
    }

    public static function provideAboveLimitContentUpdateObjects(): iterable
    {
        $objects = [];
        for ($i = 0; $i <= Request::getContentUpdateLimit(); ++$i) {
            $objects[] = new ContentUpdate(
                'test url title' . $i,
                'test.url' . $i,
                'products',
                []
            );
        }

        yield [new ContentUpdateCollection($objects)];
    }

    #[Test]
    #[DataProvider('provideAboveLimitPartialContentUpdateObjects')]
    public function partialContentUpdateWithExceededLimit(ContentUpdateCollection $objects): void
    {
        $this->expectException(TooManyItemsException::class);
        $this->expectExceptionMessage(sprintf('Expect less than or equal %s items. Got %s.', 50, \count($objects)));

        $requestService = $this->getSimpleRequestService();
        $requestService->partialContentUpdate($objects);
    }

    public static function provideAboveLimitPartialContentUpdateObjects(): iterable
    {
        $objects = [];
        for ($i = 0; $i <= Request::getPartialContentUpdateLimit(); ++$i) {
            $objects[] = new PartialContentUpdate(
                'test.url' . $i,
                'products',
                []
            );
        }

        yield [new ContentUpdateCollection($objects)];
    }

    #[Test]
    public function contentUpdateWithErrors(): void
    {
        $objects = new ContentUpdateCollection(
            [
                new ContentUpdate(
                    'test title',
                    'test.url',
                    'products',
                    [],
                ),
                new ContentUpdate(
                    'test title',
                    'test.url2',
                    'categories',
                    []
                ),
            ]
        );

        $requestService = $this->getRequestServiceForContentUpdate(
            $objects,
            [
                'ok_count' => 1,
                'errors_count' => 1,
                'errors' => [
                    'test.url2' => [
                        'type' => 'malformed_input',
                        'reason' => 'incorrect object format',
                        'caused_by' => [
                            'title' => ['must be filled'],
                        ],
                    ],
                ],
            ]
        );
        $response = $requestService->contentUpdate($objects);

        $this->assertFalse($response->isSuccess());
        $this->assertSame(1, $response->okCount);
        $this->assertSame(1, $response->errorsCount);
        $this->assertCount(1, $response->errors);

        $apiResponseError = $response->errors[0];
        $this->assertSame('test.url2', $apiResponseError->url);
        $this->assertSame('malformed_input', $apiResponseError->type);
        $this->assertSame('incorrect object format', $apiResponseError->reason);
        $this->assertSame(
            [
                'title' => ['must be filled'],
            ],
            $apiResponseError->causedBy
        );
    }

    private function getRequestServiceForContentUpdate(ObjectsInterface $objects, array $apiResponse): RequestInterface
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
                    json_encode($apiResponse, JSON_THROW_ON_ERROR, 512)
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

    private function getRequestServiceForPartialUpdate(ObjectsInterface $objects, array $apiResponse): RequestInterface
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
                    json_encode($apiResponse, JSON_THROW_ON_ERROR, 512)
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

    private function getRequestServiceForRemoval(ObjectsInterface $objects, array $apiResponse): RequestInterface
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
                    json_encode($apiResponse, JSON_THROW_ON_ERROR, 512)
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

    private function getSimpleRequestService(): RequestInterface
    {
        return new Request(
            $this->createMock(Client::class),
            $this->createMock(ContentUpdateFactory::class),
            $this->createMock(PartialContentUpdateFactory::class),
            $this->createMock(ContentRemovalFactory::class)
        );
    }
}
