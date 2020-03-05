<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityObjects;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateObjects;
use GuzzleHttp\Psr7\Request;
use Webmozart\Assert\Assert;

class PartialContentUpdateFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'PATCH';
    private const ENDPOINT = '/v1/content';

    /**
     * @param ContentAvailabilityObjects|ContentAvailability $object
     */
    public function prepareRequestForAvailability($object): Request
    {
        $expectedClasses = [
            ContentAvailabilityObjects::class,
            ContentAvailability::class,
        ];

        Assert::isInstanceOfAny(
            $object,
            $expectedClasses,
            sprintf(
                'Passed object must be an instance of [%s]',
                implode(', ', $expectedClasses)
            )
        );

        $objects = $object;
        if ($object instanceof ContentAvailability) {
            $objects = new ContentAvailabilityObjects([$object]);
        }

        return $this->prepareRequest(ContentUpdateObjects::fromContentAvailabilityObjects($objects));
    }

    protected function getHttpMethod(): string
    {
        return self::HTTP_METHOD;
    }

    protected function getEndpoint(): string
    {
        return self::ENDPOINT;
    }
}
