<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

class ApiResponse
{
    private const OK_COUNT_PARAM = 'ok_count';
    private const ERRORS_COUNT_PARAM = 'errors_count';
    private const ERRORS_PARAM = 'errors';

    public readonly bool $success;

    public readonly int $okCount;

    public readonly int $errorsCount;

    /**
     * @var ApiResponseError[]
     */
    public readonly array $errors;

    public readonly array $rawResponse;

    public function __construct(
        int $allCount,
        array $response,
    ) {
        $this->rawResponse = $response;
        $this->okCount = (int) ($response[self::OK_COUNT_PARAM] ?? 0);
        $this->errorsCount = (int) ($response[self::ERRORS_COUNT_PARAM] ?? 0);

        $success = false;
        if ($this->okCount === $allCount) {
            $success = true;
        }
        $this->success = $success;

        $responseErrors = $response[self::ERRORS_PARAM] ?? [];
        $errors = [];
        foreach ($responseErrors as $url => $error) {
            $errors[] = new ApiResponseError($url, $error);
        }
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
