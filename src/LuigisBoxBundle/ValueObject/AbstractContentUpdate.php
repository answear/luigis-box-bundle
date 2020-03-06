<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

abstract class AbstractContentUpdate
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string[]|null
     */
    protected $autocompleteType;

    /**
     * @var string|null
     */
    protected $generation;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     *
     * @var string|null
     */
    protected $activeFrom;

    /**
     * The date/time must be formatted in the ISO 8601 format, e.g. 2019-05-17T21:12:35+00:00
     *
     * @var string|null
     */
    protected $activeTo;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var AbstractContentUpdate[]|null
     */
    protected $nested;

    public function __construct(string $url, ?string $type, array $fields)
    {
        if (isset($fields['availability'])) {
            Assert::oneOf($fields['availability'], [0, 1], 'Field availability must be one of [0, 1]');
        }

        if (isset($fields['availability_rank'])) {
            Assert::integer($fields['availability_rank'], 'Field availability_rank must be integer');
            if ($fields['availability_rank'] < 1 || $fields['availability_rank'] > 15) {
                throw new \InvalidArgumentException('Field availability_rank must be between 1 and 15');
            }
        }

        $this->url = $url;
        $this->type = $type;
        $this->fields = $fields;
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

    /**
     * @return AbstractContentUpdate[]|null
     */
    public function getNested(): ?array
    {
        return $this->nested;
    }

    abstract public function setNested(array $nested): void;
}
