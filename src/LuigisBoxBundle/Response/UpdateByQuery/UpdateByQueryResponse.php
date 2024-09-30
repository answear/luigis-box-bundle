<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\UpdateByQuery;

use Webmozart\Assert\Assert;

class UpdateByQueryResponse
{
    private const STATUS_URL = 'status_url';

    public readonly array $rawResponse;

    public readonly int $jobId;

    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->jobId = $this->getJobIdFromStatusUrl($response[self::STATUS_URL]);
    }

    private function getJobIdFromStatusUrl(string $statusUrl): int
    {
        parse_str($statusUrl, $parsed);
        Assert::count($parsed, 1);
        $jobId = array_values($parsed);
        Assert::numeric($jobId[0], sprintf('Job id is no numeric. Got: %s.', $jobId[0]));

        return (int) $jobId[0];
    }
}
