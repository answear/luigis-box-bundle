<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\UpdateByQuery;

use Answear\LuigisBoxBundle\Response\ApiResponseError;

class UpdateByQueryStatusResponse
{
    private const TRACKER_ID = 'tracker_id';
    private const STATUS = 'status';
    private const UPDATES_COUNT = 'updates_count';
    private const FAILURES_COUNT = 'failures_count';
    private const FAILURES = 'failures';
    private const STATUS_COMPLETED = 'complete';

    public readonly array $rawResponse;

    public readonly string $trackerId;

    public readonly bool $completed;

    public readonly ?int $okCount;

    public readonly ?int $errorsCount;

    /**
     * @var ?ApiResponseError[]
     */
    public readonly ?array $errors;

    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->trackerId = $response[self::TRACKER_ID];
        $this->completed = self::STATUS_COMPLETED === $response[self::STATUS];

        $okCount = null;
        $errorsCount = null;
        $errors = null;

        if ($this->completed) {
            $okCount = $response[self::UPDATES_COUNT];
            $errorsCount = $response[self::FAILURES_COUNT];
            $errors = [];
            foreach ($response[self::FAILURES] as $url => $failure) {
                $errors[] = new ApiResponseError($url, $failure);
            }
        }

        $this->okCount = $okCount;
        $this->errorsCount = $errorsCount;
        $this->errors = $errors;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }
}
