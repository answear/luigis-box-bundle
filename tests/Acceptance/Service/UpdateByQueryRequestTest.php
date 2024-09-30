<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\Service;

use Answear\LuigisBoxBundle\Factory\UpdateByRequestFactory;
use Answear\LuigisBoxBundle\Factory\UpdateByRequestStatusFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\Service\UpdateByQueryRequest;
use Answear\LuigisBoxBundle\Service\UpdateByQueryRequestInterface;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateByQueryRequestTest extends TestCase
{
    #[Test]
    #[DataProvider('forUpdate')]
    public function updatePassed(
        UpdateByQuery $updateByQuery,
        string $expectedContent,
        array $apiResponse,
        int $jobId,
    ): void {
        $response = $this->getService('PATCH', '/v1/update_by_query', $expectedContent, $apiResponse)->update(
            $updateByQuery
        );

        $this->assertSame($apiResponse, $response->rawResponse);
        $this->assertSame($jobId, $response->jobId);
    }

    public static function forUpdate(): iterable
    {
        yield [
            new UpdateByQuery(
                new UpdateByQuery\Search(['product'], ['color' => 'olive']),
                new UpdateByQuery\Update(['color' => 'green']),
            ),
            '{"search":{"partial":{"fields":{"color":"olive"}},"types":["product"]},"update":{"fields":{"color":"green"}}}',
            [
                'status_url' => '/v1/update_by_query?job_id=1',
            ],
            1,
        ];

        yield [
            new UpdateByQuery(
                new UpdateByQuery\Search(['product', 'brand'], ['color' => 'olive']),
                new UpdateByQuery\Update(['color' => ['green', 'blue'], 'brand' => 'Star']),
            ),
            '{"search":{"partial":{"fields":{"color":"olive"}},"types":["product","brand"]},"update":{"fields":{"color":["green","blue"],"brand":"Star"}}}',
            [
                'status_url' => '/v1/update_by_query?job_id=12',
            ],
            12,
        ];
    }

    #[Test]
    #[DataProvider('forUpdateStatus')]
    public function updateStatusPassed(
        int $jobId,
        array $apiResponse,
    ): void {
        $response = $this->getService('GET', '/v1/update_by_query?job_id=' . $jobId, '', $apiResponse)
            ->getStatus($jobId);

        $this->assertSame('complete' === $apiResponse['status'], $response->isCompleted());
        $this->assertSame($apiResponse['tracker_id'], $response->trackerId);
        $this->assertSame($apiResponse['updates_count'] ?? null, $response->okCount);
        $this->assertSame($apiResponse['failures_count'] ?? null, $response->errorsCount);

        if (!isset($apiResponse['failures'])) {
            $this->assertNull($response->errors);
        } else {
            foreach ($response->errors as $error) {
                $failure = $apiResponse['failures'][$error->url];

                $this->assertNotEmpty($failure);
                $this->assertSame($failure['type'], $error->type);
                $this->assertSame($failure['reason'], $error->reason);
                $this->assertSame($failure['caused_by'], $error->causedBy);
            }
        }

        $this->assertSame($apiResponse, $response->rawResponse);
    }

    public static function forUpdateStatus(): iterable
    {
        yield [
            1,
            [
                'tracker_id' => 'abcd',
                'status' => 'complete',
                'updates_count' => 5,
                'failures_count' => 0,
                'failures' => [],
            ],
        ];

        yield [
            111,
            [
                'tracker_id' => 'iabad',
                'status' => 'in progress',
            ],
        ];

        yield [
            12,
            [
                'tracker_id' => 'abad',
                'status' => 'complete',
                'updates_count' => 5,
                'failures_count' => 1,
                'failures' => [
                    '/products/1' => [
                        'type' => 'data_schema_mismatch',
                        'reason' => 'failed to parse [attributes.price]',
                        'caused_by' => [
                            'type' => 'number_format_exception',
                            'reason' => 'For input string: "wrong sale price"',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getService(
        string $httpMethod,
        string $endpoint,
        string $expectedContent,
        array $apiResponse,
    ): UpdateByQueryRequestInterface {
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
                            case $currentRequest->getUri()->getQuery() !== $expectedRequest->getUri()->getQuery():
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

        $configProvider = ExampleConfiguration::provideDefaultConfig();

        return new UpdateByQueryRequest(
            $client,
            new UpdateByRequestFactory($configProvider, $serializer),
            new UpdateByRequestStatusFactory($configProvider)
        );
    }
}
