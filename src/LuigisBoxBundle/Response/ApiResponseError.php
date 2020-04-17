<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

class ApiResponseError
{
    private const TYPE_PARAM = 'type';
    private const REASON_PARAM = 'reason';
    private const CAUSED_BY_PARAM = 'caused_by';

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var array|null
     */
    private $causedBy;

    public function __construct(string $url, array $error)
    {
        $this->url = $url;
        $this->type = $error[self::TYPE_PARAM];
        $this->reason = $error[self::REASON_PARAM];
        $this->causedBy = $error[self::CAUSED_BY_PARAM] ?? null;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getCausedBy(): ?array
    {
        return $this->causedBy;
    }
}
