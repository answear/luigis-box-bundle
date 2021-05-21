<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\Service;

use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\SearchClient;
use Answear\LuigisBoxBundle\Service\SearchRequest;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class SearchRequestTest extends TestCase
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configProvider = new ConfigProvider(
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
    }

    /**
     * @test
     */
    public function additionalHeadersTest(): void
    {
        $expectedContent = '';
        $apiResponse = [
            'results' => [
                'query' => '',
                'filters' => [],
                'hits' => [],
                'quicksearch_hits' => [],
                'facets' => [],
                'total_hits' => 0,
            ],
        ];
        $this->configProvider->setHeader('New-Header', 'new header value');
        $response = $this->getSearchService($expectedContent, $apiResponse)->search(
            new SearchUrlBuilder()
        );

        self::assertSame('', $response->getQuery());
    }

    private function getSearchService(
        string $expectedContent,
        array $apiResponse
    ): SearchRequest {
        $endpoint = '/search';

        $expectedRequest = new \GuzzleHttp\Psr7\Request(
            'GET',
            new Uri('host' . $endpoint),
            [
                'New-Header' => ['new header value'],
            ],
            $expectedContent
        );

        $client = $this->createMock(SearchClient::class);
        $client->expects(self::once())
            ->method('request')
            ->with(
                self::callback(
                    static function (\GuzzleHttp\Psr7\Request $currentRequest) use ($expectedRequest) {
                        $currentHeaders = $currentRequest->getHeaders();
                        $expectedHeaders = $expectedRequest->getHeaders();

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

        return new SearchRequest(
            $client,
            new SearchFactory($this->configProvider)
        );
    }
}
