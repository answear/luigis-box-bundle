<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

class ApiResponse
{
    private const OK_COUNT_PARAM = 'ok_count';
    private const ERRORS_COUNT_PARAM = 'errors_count';
    private const ERRORS_PARAM = 'errors';

    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var int
     */
    private $okCount;

    /**
     * @var int
     */
    private $errorsCount;

    /**
     * @var ApiResponseError[]
     */
    private $errors = [];

    /**
     * @var array
     */
    private $rawResponse;

    public function __construct(int $allCount, array $response)
    {
        $this->rawResponse = $response;
        $this->okCount = (int) $response[self::OK_COUNT_PARAM];
        $this->errorsCount = (int) ($response[self::ERRORS_COUNT_PARAM] ?? 0);

        if ($this->okCount === $allCount) {
            $this->success = true;
        }

        $errors = $response[self::ERRORS_PARAM] ?? [];
        foreach ($errors as $url => $error) {
            $this->addError(new ApiResponseError($url, $error));
        }
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getOkCount(): int
    {
        return $this->okCount;
    }

    public function getErrorsCount(): int
    {
        return $this->errorsCount;
    }

    /**
     * @return ApiResponseError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function addError(ApiResponseError $apiResponseError): void
    {
        $this->errors[] = $apiResponseError;
    }
}
