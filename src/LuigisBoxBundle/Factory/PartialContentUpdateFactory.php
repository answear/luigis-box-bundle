<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Psr7\Request;

class PartialContentUpdateFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'PATCH';
    private const ENDPOINT = '/' . ConfigProvider::API_VERSION . '/content';

    public function prepareRequestForAvailability(ContentAvailabilityCollection|ContentAvailability $object): Request
    {
        $objects = $object;
        if ($object instanceof ContentAvailability) {
            $objects = new ContentAvailabilityCollection([$object]);
        }

        return $this->prepareRequest(ContentUpdateCollection::fromContentAvailabilityObjects($objects));
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
