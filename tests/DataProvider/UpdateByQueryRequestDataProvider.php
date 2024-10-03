<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\DataProvider;

use Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

class UpdateByQueryRequestDataProvider
{
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
}
