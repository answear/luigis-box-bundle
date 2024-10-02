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
use Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProviderExternal;
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
    #[DataProviderExternal(RequestDataProvider::class, 'forContentUpdate')]
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

    #[Test]
    #[DataProviderExternal(RequestDataProvider::class, 'forPartialContentUpdate')]
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

    #[Test]
    #[DataProviderExternal(RequestDataProvider::class, 'forContentRemoval')]
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

    #[Test]
    #[DataProviderExternal(RequestDataProvider::class, 'forChangeAvailability')]
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
