<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

class ApiResponseError implements \JsonSerializable
{
    private const TYPE_PARAM = 'type';
    private const REASON_PARAM = 'reason';
    private const CAUSED_BY_PARAM = 'caused_by';

    public readonly string $type;
    public readonly string $reason;
    public readonly ?array $causedBy;

    public function __construct(
        public readonly string $url,
        array $error,
    ) {
        $this->type = $error[self::TYPE_PARAM];
        $this->reason = $error[self::REASON_PARAM];
        $this->causedBy = $error[self::CAUSED_BY_PARAM] ?? null;
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'reason' => $this->reason,
            'causedBy' => $this->causedBy,
        ];
    }
}
