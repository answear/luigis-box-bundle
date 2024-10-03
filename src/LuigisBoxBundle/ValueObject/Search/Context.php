<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject\Search;

use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

class Context
{
    private ?string $geoLocation = null;

    private ?string $geoLocationField = null;

    private ?string $availabilityField = null;

    private ?string $boostField = null;

    private ?string $freshnessField = null;

    public function getGeoLocation(): ?string
    {
        return $this->geoLocation;
    }

    public function setGeoLocation(float $latitude, float $longitude): void
    {
        $this->geoLocation = $latitude . SearchUrlBuilder::LIST_SEPARATOR . $longitude;
    }

    public function getGeoLocationField(): ?string
    {
        return $this->geoLocationField;
    }

    public function setGeoLocationField(string $geoLocationField): void
    {
        $this->geoLocationField = $geoLocationField;
    }

    public function getAvailabilityField(): ?string
    {
        return $this->availabilityField;
    }

    public function setAvailabilityField(string $availabilityField): void
    {
        $this->availabilityField = $availabilityField;
    }

    public function getBoostField(): ?string
    {
        return $this->boostField;
    }

    public function setBoostField(string $boostField): void
    {
        $this->boostField = $boostField;
    }

    public function getFreshnessField(): ?string
    {
        return $this->freshnessField;
    }

    public function setFreshnessField(string $freshnessField): void
    {
        $this->freshnessField = $freshnessField;
    }
}
