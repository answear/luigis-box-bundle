<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\Service;

use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\Service\Request;
use Answear\LuigisBoxBundle\Service\RequestInterface;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private ConfigProvider $configProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configProvider = ExampleConfiguration::provideDefaultConfig();
    }

    #[Test]
    #[DataProvider('forContentUpdate')]
    public function contentUpdateRequestPassed(
        string $httpMethod,
        ContentUpdateCollection $collection,
        string $expectedContent,
        array $apiResponse,
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->contentUpdate(
            $collection
        );

        self::assertTrue($response->isSuccess());
        self::assertSame(\count($collection), $response->okCount);
        self::assertSame(0, $response->errorsCount);
        self::assertSame([], $response->errors);
    }

    public static function forContentUpdate(): iterable
    {
        yield [
            'POST',
            new ContentUpdateCollection([new ContentUpdate('title', 'product/1', 'products', [])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $collection = new ContentUpdateCollection(
            [new ContentUpdate('title', 'product/1', 'products', ['availability' => 1])]
        );
        yield [
            'POST',
            $collection,
            '{"objects":[{"url":"product\/1","type":"products","fields":{"availability":1,"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $contentUpdate1 = new ContentUpdate('title', 'product/2', 'products', ['availability' => 1]);
        $contentUpdate1->setActiveTo('2019-12-12 00:01:02');
        $contentUpdate1->setAutocompleteType(
            [
                'categories',
                'other',
            ]
        );
        $contentUpdate2 = new ContentUpdate('title', 'product/1', 'products', ['availability' => 0]);
        $contentUpdate2->setGeneration('one');
        $contentUpdate2->setNested([$contentUpdate1]);
        $collection = new ContentUpdateCollection(
            [$contentUpdate1, $contentUpdate2]
        );
        yield [
            'POST',
            $collection,
            '{"objects":[{"autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"availability":1,"title":"title"}},{"generation":"one","nested":[{"autocomplete_type":["categories","other"],"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"availability":1,"title":"title"}}],"url":"product\/1","type":"products","fields":{"availability":0,"title":"title"}}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    #[Test]
    #[DataProvider('forPartialContentUpdate')]
    public function partialContentUpdateRequestPassed(
        string $httpMethod,
        ContentUpdateCollection $collection,
        string $expectedContent,
        array $apiResponse,
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->partialContentUpdate(
            $collection
        );

        self::assertTrue($response->isSuccess());
        self::assertSame(\count($collection), $response->okCount);
        self::assertSame(0, $response->errorsCount);
        self::assertSame([], $response->errors);
    }

    public static function forPartialContentUpdate(): iterable
    {
        yield [
            'PATCH',
            new ContentUpdateCollection([new PartialContentUpdate('product/1', 'products', ['brand' => 'brand name'])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"brand":"brand name"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        yield [
            'PATCH',
            new ContentUpdateCollection([new PartialContentUpdate('product/1', 'products', ['title' => 'title'])]),
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title"}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $collection = new ContentUpdateCollection(
            [new PartialContentUpdate('product/1', 'products', ['title' => 'title', 'availability' => 1])]
        );
        yield [
            'PATCH',
            $collection,
            '{"objects":[{"url":"product\/1","type":"products","fields":{"title":"title","availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        $contentUpdate1 = new PartialContentUpdate('product/2', 'products', ['title' => 'title', 'availability' => 1]);
        $contentUpdate1->setActiveTo('2019-12-12 00:01:02');
        $contentUpdate2 = new PartialContentUpdate('product/1', 'products', ['title' => 'title', 'availability' => 0]);
        $contentUpdate2->setGeneration('one');
        $contentUpdate2->setNested([$contentUpdate1]);
        $collection = new ContentUpdateCollection(
            [$contentUpdate1, $contentUpdate2]
        );
        yield [
            'PATCH',
            $collection,
            '{"objects":[{"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"title":"title","availability":1}},{"generation":"one","nested":[{"active_to":"2019-12-12 00:01:02","url":"product\/2","type":"products","fields":{"title":"title","availability":1}}],"url":"product\/1","type":"products","fields":{"title":"title","availability":0}}]}',
            [
                'ok_count' => 2,
            ],
        ];
    }

    #[Test]
    #[DataProvider('forContentRemoval')]
    public function contentRemovalRequestPassed(
        string $httpMethod,
        ContentRemovalCollection $collection,
        string $expectedContent,
        array $apiResponse,
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->contentRemoval(
            $collection
        );

        self::assertTrue($response->isSuccess());
        self::assertSame(\count($collection), $response->okCount);
        self::assertSame(0, $response->errorsCount);
        self::assertSame([], $response->errors);
    }

    public static function forContentRemoval(): iterable
    {
        yield [
            'DELETE',
            new ContentRemovalCollection([new ContentRemoval('product/1', 'product')]),
            '{"objects":[{"url":"product\/1","type":"product"}]}',
            [
                'ok_count' => 1,
            ],
        ];
    }

    #[Test]
    #[DataProvider('forChangeAvailability')]
    public function changeAvailabilityRequestPassed(
        string $httpMethod,
        $collection,
        string $expectedContent,
        array $apiResponse,
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->changeAvailability(
            $collection
        );

        self::assertTrue($response->isSuccess());
        self::assertSame(
            ($collection instanceof ContentAvailability) ? 1 : \count($collection),
            $response->okCount
        );
        self::assertSame(0, $response->errorsCount);
        self::assertSame([], $response->errors);
    }

    public static function forChangeAvailability(): iterable
    {
        yield [
            'PATCH',
            new ContentAvailabilityCollection([new ContentAvailability('product/1', true)]),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];

        yield [
            'PATCH',
            new ContentAvailabilityCollection(
                [new ContentAvailability('product/1', true), new ContentAvailability('product/2', false)]
            ),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}},{"url":"product\/2","fields":{"availability":0}}]}',
            [
                'ok_count' => 2,
            ],
        ];

        yield [
            'PATCH',
            new ContentAvailability('product/1', true),
            '{"objects":[{"url":"product\/1","fields":{"availability":1}}]}',
            [
                'ok_count' => 1,
            ],
        ];
    }

    private function getRequestService(
        string $httpMethod,
        string $expectedContent,
        array $apiResponse,
    ): RequestInterface {
        $endpoint = '/v1/content';

        $expectedRequest = new \GuzzleHttp\Psr7\Request(
            $httpMethod,
            new Uri('host' . $endpoint),
            [
                'Content-Type' => ['application/json; charset=utf-8'],
                'date' => [''],
                'Authorization' => [''],
            ],
            $expectedContent
        );
        $serializer = new LuigisBoxSerializer();

        $client = $this->createMock(Client::class);
        $client->expects(self::once())
            ->method('request')
            ->with(
                self::callback(
                    static function (\GuzzleHttp\Psr7\Request $currentRequest) use ($expectedRequest) {
                        $currentHeaders = $currentRequest->getHeaders();
                        $expectedHeaders = $expectedRequest->getHeaders();
                        $expectedHeaders['date'] = $currentHeaders['date'];
                        $expectedHeaders['Authorization'] = $currentHeaders['Authorization'];

                        $currentContent = $currentRequest->getBody()->getContents();
                        $expectedContent = $expectedRequest->getBody()->getContents();

                        switch (true) {
                            case $currentRequest->getMethod() !== $expectedRequest->getMethod():
                            case $currentRequest->getUri()->getPath() !== $expectedRequest->getUri()->getPath():
                            case $currentHeaders !== $expectedHeaders:
                            case $currentContent !== $expectedContent:
                                // check equals for showing difference
                                self::assertEquals($expectedContent, $currentContent);
                                self::assertEquals($expectedRequest, $currentRequest);

                                return false;
                        }

                        return true;
                    }
                )
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    json_encode($apiResponse, JSON_THROW_ON_ERROR, 512)
                )
            );

        return new Request(
            $client,
            new ContentUpdateFactory($this->configProvider, $serializer),
            new PartialContentUpdateFactory($this->configProvider, $serializer),
            new ContentRemovalFactory($this->configProvider, $serializer),
        );
    }
}
