<?php

declare(strict_types=1);

namespace Acceptance\Service;

use Answear\LuigisBoxBundle\Factory\RecommendationsFactory;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\Service\RecommendationsClient;
use Answear\LuigisBoxBundle\Service\RecommendationsRequest;
use Answear\LuigisBoxBundle\Service\RecommendationsRequestInterface;
use Answear\LuigisBoxBundle\Tests\DataProvider\RequestDataProvider;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\RecommendationsCollection;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecommendationsRequestTest extends TestCase
{
    private ConfigProvider $configProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configProvider = ExampleConfiguration::provideDefaultConfig();
    }

    #[Test]
    #[DataProviderExternal(RequestDataProvider::class, 'forRecommendationsRequest')]
    public function getRecommendationsRequestPassed(
        string $httpMethod,
        RecommendationsCollection $collection,
        string $expectedContent,
        array $apiResponse,
    ): void {
        $response = $this
            ->getRequestService($httpMethod, $expectedContent, $apiResponse, '/v1/recommend')
            ->getRecommendations($collection);

        self::assertTrue($response->isSuccess());
        self::assertSame(0, $response->errorsCount);
        self::assertSame([], $response->errors);
    }

    private function getRequestService(
        string $httpMethod,
        string $expectedContent,
        array $apiResponse,
        string $endpoint = '/v1/content',
    ): RecommendationsRequestInterface {
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

        $recommendationsClient = $this->createMock(RecommendationsClient::class);
        $recommendationsClient->expects(self::once())
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

        return new RecommendationsRequest(
            new RecommendationsFactory($this->configProvider, $serializer),
            $recommendationsClient
        );
    }
}
