<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\Search;

use Webmozart\Assert\Assert;

class Hit
{
    private const ALTERNATIVE_PARAM = 'alternative';
    private const EXACT_PARAM = 'exact';
    private const HIGHLIGHT_PARAM = 'highlight';
    private const TYPE_PARAM = 'type';
    private const NESTED_PARAM = 'nested';
    private const ATTRIBUTES_PARAM = 'attributes';
    private const URL_PARAM = 'url';

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var array
     */
    private $nested;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $highlight;

    /**
     * @var bool
     */
    private $exact;

    /**
     * @var bool
     */
    private $alternative;

    public function __construct(array $hitArray)
    {
        Assert::string($hitArray[self::URL_PARAM]);
        Assert::isArray($hitArray[self::ATTRIBUTES_PARAM]);
        Assert::isArray($hitArray[self::NESTED_PARAM]);
        Assert::string($hitArray[self::TYPE_PARAM]);
        Assert::isArray($hitArray[self::HIGHLIGHT_PARAM] ?? []);
        Assert::boolean($hitArray[self::EXACT_PARAM]);
        Assert::boolean($hitArray[self::ALTERNATIVE_PARAM]);

        $this->url = $hitArray[self::URL_PARAM];
        $this->attributes = $hitArray[self::ATTRIBUTES_PARAM];
        $this->nested = $hitArray[self::NESTED_PARAM];
        $this->type = $hitArray[self::TYPE_PARAM];
        $this->highlight = $hitArray[self::HIGHLIGHT_PARAM] ?? [];
        $this->exact = $hitArray[self::EXACT_PARAM];
        $this->alternative = $hitArray[self::ALTERNATIVE_PARAM];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getNested(): array
    {
        return $this->nested;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHighlight(): array
    {
        return $this->highlight;
    }

    public function isExact(): bool
    {
        return $this->exact;
    }

    public function isAlternative(): bool
    {
        return $this->alternative;
    }
}
