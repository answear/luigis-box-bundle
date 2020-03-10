<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityObjects;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalObjects;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateObjects;
use Psr\Http\Message\ResponseInterface;

class Request
{
    private const CONTENT_UPDATE_OBJECTS_LIMIT = 100;
    private const PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT = 50;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ContentUpdateFactory
     */
    private $contentUpdateFactory;

    /**
     * @var PartialContentUpdateFactory
     */
    private $partialContentUpdateFactory;

    /**
     * @var ContentRemovalFactory
     */
    private $contentRemovalFactory;

    public function __construct(
        Client $client,
        ContentUpdateFactory $contentUpdateFactory,
        PartialContentUpdateFactory $partialContentUpdateFactory,
        ContentRemovalFactory $contentRemovalFactory
    ) {
        $this->client = $client;
        $this->contentUpdateFactory = $contentUpdateFactory;
        $this->partialContentUpdateFactory = $partialContentUpdateFactory;
        $this->contentRemovalFactory = $contentRemovalFactory;
    }

    public function contentUpdate(ContentUpdateObjects $objects): ResponseInterface
    {
        if (\count($objects) > self::CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new \RuntimeException(
                sprintf(
                    'Expect less than or equal %s objects. Got %s.',
                    self::CONTENT_UPDATE_OBJECTS_LIMIT,
                    \count($objects)
                )
            );
        }

        return $this->client->request($this->contentUpdateFactory->prepareRequest($objects));
    }

    public function partialContentUpdate(ContentUpdateObjects $objects): ResponseInterface
    {
        if (\count($objects) > self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new \RuntimeException(
                sprintf(
                    'Expect less than or equal %s objects. Got %s.',
                    self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT,
                    \count($objects)
                )
            );
        }

        return $this->client->request($this->partialContentUpdateFactory->prepareRequest($objects));
    }

    public function contentRemoval(ContentRemovalObjects $objects): ResponseInterface
    {
        return $this->client->request($this->contentRemovalFactory->prepareRequest($objects));
    }

    /**
     * @param ContentAvailabilityObjects|ContentAvailability $object
     */
    public function changeAvailability($object): ResponseInterface
    {
        return $this->client->request($this->partialContentUpdateFactory->prepareRequestForAvailability($object));
    }
}
