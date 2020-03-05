<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentUpdate
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]|null
     */
    private $autocompleteType;

    /**
     * @var string|null
     */
    private $generation;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     *
     * @var string|null
     */
    private $activeFrom;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     *
     * @var string|null
     */
    private $activeTo;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var ContentUpdate[]
     */
    private $nested;

    public function __construct(string $url, string $type, array $fields)
    {
        Assert::keyExists($fields, 'title', 'Field title must be provided for $fields');

        if (isset($fields['availability'])) {
            Assert::oneOf($fields['availability'], [0, 1], 'Field availability must be one of [0, 1]');
        }

        if (isset($fields['availability_rank'])) {
            Assert::integer($fields['availability_rank'], 'Field availability_rank must be integer');
            if ($fields['availability_rank'] < 1 || $fields['availability_rank'] > 15) {
                throw new \InvalidArgumentException('Field availability_rank be between 1 and 15');
            }
        }

        $this->url = $url;
        $this->type = $type;
        $this->fields = $fields;
        $this->nested = [];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAutocompleteType(): ?array
    {
        return $this->autocompleteType;
    }

    public function setAutocompleteType(?array $autocompleteType): void
    {
        if (null !== $autocompleteType) {
            Assert::allString($autocompleteType);
        }

        $this->autocompleteType = $autocompleteType;
    }

    public function getGeneration(): ?string
    {
        return $this->generation;
    }

    public function setGeneration(?string $generation): void
    {
        $this->generation = $generation;
    }

    public function getActiveFrom(): ?string
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(?string $activeFrom): void
    {
        $this->activeFrom = $activeFrom;
    }

    public function getActiveTo(): ?string
    {
        return $this->activeTo;
    }

    public function setActiveTo(?string $activeTo): void
    {
        $this->activeTo = $activeTo;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getNested(): array
    {
        return $this->nested;
    }

    public function setNested(array $nested): void
    {
        Assert::allIsInstanceOf($nested, ContentUpdate::class);
        $this->nested = $nested;
    }
}
