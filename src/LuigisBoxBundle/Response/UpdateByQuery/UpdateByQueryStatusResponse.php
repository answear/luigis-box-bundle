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

    /**
     * @var array
     */
    private $rawResponse;

    /**
     * @var string
     */
    private $trackerId;

    /**
     * @var bool
     */
    private $completed;

    /**
     * @var int|null
     */
    private $okCount;

    /**
     * @var int|null
     */
    private $errorsCount;

    /**
     * @var ApiResponseError[]|null
     */
    private $errors;

    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->trackerId = $response[self::TRACKER_ID];
        $this->completed = self::STATUS_COMPLETED === $response[self::STATUS];

        if ($this->completed) {
            $this->okCount = $response[self::UPDATES_COUNT];
            $this->errorsCount = $response[self::FAILURES_COUNT];
            $this->errors = [];
            foreach ($response[self::FAILURES] as $url => $failure) {
                $this->errors[] = new ApiResponseError($url, $failure);
            }
        }
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public function getTrackerId(): string
    {
        return $this->trackerId;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getOkCount(): ?int
    {
        return $this->okCount;
    }

    public function getErrorsCount(): ?int
    {
        return $this->errorsCount;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
