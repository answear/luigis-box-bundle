<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

abstract class AbstractContentUpdate
{
    /**
     * @var ?string[]
     */
    protected ?array $autocompleteType = null;

    protected ?string $generation = null;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     */
    protected ?string $activeFrom = null;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     */
    protected ?string $activeTo = null;

    /**
     * @var ?AbstractContentUpdate[]
     */
    protected ?array $nested = null;

    public function __construct(
        protected string $url,
        protected ?string $type,
        protected array $fields,
    ) {
        if (isset($fields['availability'])) {
            Assert::oneOf($fields['availability'], [0, 1], 'Field availability must be one of [0, 1]');
        }

        if (isset($fields['availability_rank'])) {
            Assert::integer($fields['availability_rank'], 'Field availability_rank must be integer');
            Assert::range($fields['availability_rank'], 1, 15, 'Field availability_rank must be between 1 and 15');
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): ?string
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

    public function getField(string $fieldName): array|string|null
    {
        return $this->fields[$fieldName] ?? null;
    }

    /**
     * @return AbstractContentUpdate[]|null
     */
    public function getNested(): ?array
    {
        return $this->nested;
    }

    abstract public function setNested(array $nested): void;
}
