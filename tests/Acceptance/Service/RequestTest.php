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
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forContentUpdate()
     */
    public function contentUpdateRequestPassed(
        string $httpMethod,
        ContentUpdateCollection $collection,
        string $expectedContent,
        array $apiResponse
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->contentUpdate(
            $collection
        );

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($collection), $response->getOkCount());
        $this->assertSame(0, $response->getErrorsCount());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forPartialContentUpdate()
     */
    public function partialContentUpdateRequestPassed(
        string $httpMethod,
        ContentUpdateCollection $collection,
        string $expectedContent,
        array $apiResponse
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->partialContentUpdate(
            $collection
        );

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($collection), $response->getOkCount());
        $this->assertSame(0, $response->getErrorsCount());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forContentRemoval()
     */
    public function contentRemovalRequestPassed(
        string $httpMethod,
        ContentRemovalCollection $collection,
        string $expectedContent,
        array $apiResponse
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->contentRemoval(
            $collection
        );

        $this->assertTrue($response->isSuccess());
        $this->assertSame(\count($collection), $response->getOkCount());
        $this->assertSame(0, $response->getErrorsCount());
        $this->assertSame([], $response->getErrors());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forChangeAvailability()
     */
    public function changeAvailabilityRequestPassed(
        string $httpMethod,
        $collection,
        string $expectedContent,
        array $apiResponse
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent, $apiResponse)->changeAvailability(
            $collection
        );

        $this->assertTrue($response->isSuccess());
        $this->assertSame(
            ($collection instanceof ContentAvailability) ? 1 : \count($collection),
            $response->getOkCount()
        );
        $this->assertSame(0, $response->getErrorsCount());
        $this->assertSame([], $response->getErrors());
    }

    private function getRequestService(
        string $httpMethod,
        string $expectedContent,
        array $apiResponse
    ): RequestInterface {
        $endpoint = '/v1/content';

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
        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->callback(
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
                                //check equals for showing difference
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
            new ContentUpdateFactory($configProvider, $serializer),
            new PartialContentUpdateFactory($configProvider, $serializer),
            new ContentRemovalFactory($configProvider, $serializer),
        );
    }
}
