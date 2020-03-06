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
        string $expectedContent
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent)->contentUpdate(
            $collection
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forPartialContentUpdate()
     */
    public function partialContentUpdateRequestPassed(
        string $httpMethod,
        ContentUpdateCollection $collection,
        string $expectedContent
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent)->partialContentUpdate(
            $collection
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forContentRemoval()
     */
    public function contentRemovalRequestPassed(
        string $httpMethod,
        ContentRemovalCollection $collection,
        string $expectedContent
    ): void {
        $response = $this->getRequestService($httpMethod, $expectedContent)->contentRemoval(
            $collection
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider::forChangeAvailability()
     */
    public function changeAvailabilityRequestPassed(string $httpMethod, $collection, string $expectedContent): void
    {
        $response = $this->getRequestService($httpMethod, $expectedContent)->changeAvailability(
            $collection
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    private function getRequestService(string $httpMethod, string $expectedContent): Request
    {
        $endpoint = '/v1/content';

        $configProvider = new ConfigProvider('host', '', '', 5.0, 5.0);

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
            ->willReturn(new Response());

        return new Request(
            $client,
            new ContentUpdateFactory($configProvider, $serializer),
            new PartialContentUpdateFactory($configProvider, $serializer),
            new ContentRemovalFactory($configProvider, $serializer),
        );
    }
}
