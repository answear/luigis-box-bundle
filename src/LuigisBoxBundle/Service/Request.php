<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\RecommendationsFactory;
use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\RecommendationsCollection;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

class Request implements RequestInterface
{
    private const CONTENT_UPDATE_OBJECTS_LIMIT = 100;
    private const PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT = 50;

    public function __construct(
        private Client $client,
        private ContentUpdateFactory $contentUpdateFactory,
        private PartialContentUpdateFactory $partialContentUpdateFactory,
        private ContentRemovalFactory $contentRemovalFactory,
        private RecommendationsFactory $recommendationsFactory,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        Assert::allIsInstanceOf($objects->objects, ContentUpdate::class);

        if (\count($objects) > self::CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new TooManyItemsException(\count($objects), self::CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        $request = $this->contentUpdateFactory->prepareRequest($objects);

        return new ApiResponse(
            \count($objects),
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function partialContentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        Assert::allIsInstanceOf($objects->objects, PartialContentUpdate::class);

        if (\count($objects) > self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new TooManyItemsException(\count($objects), self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        $request = $this->partialContentUpdateFactory->prepareRequest($objects);

        return new ApiResponse(
            \count($objects),
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentRemoval(ContentRemovalCollection $objects): ApiResponse
    {
        $request = $this->contentRemovalFactory->prepareRequest($objects);

        return new ApiResponse(
            \count($objects),
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    /**
     * @param ContentAvailabilityCollection|ContentAvailability $object
     *
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function changeAvailability($object): ApiResponse
    {
        $request = $this->partialContentUpdateFactory->prepareRequestForAvailability($object);

        return new ApiResponse(
            $object instanceof ContentAvailabilityCollection ? \count($object) : 1,
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     *
     * @experimental This feature is in an experimental stage. Breaking changes may occur without prior notice.
     */
    public function getRecommendations(RecommendationsCollection $recommendationsCollection): ApiResponse
    {
        $request = $this->recommendationsFactory->prepareRequest($recommendationsCollection);

        return new ApiResponse(
            0,
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    public static function getContentUpdateLimit(): int
    {
        return self::CONTENT_UPDATE_OBJECTS_LIMIT;
    }

    public static function getPartialContentUpdateLimit(): int
    {
        return self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT;
    }

    /**
     * @throws MalformedResponseException
     */
    private function handleResponse(\GuzzleHttp\Psr7\Request $request, ResponseInterface $response): array
    {
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }

        $responseText = $response->getBody()->getContents();

        try {
            if (empty($responseText)) {
                throw new \RuntimeException('Empty response.');
            }
            $decoded = \json_decode($responseText, true, 512, JSON_THROW_ON_ERROR);
            Assert::isArray($decoded);
        } catch (\Throwable $e) {
            throw new MalformedResponseException($e->getMessage(), $response, $request, $e);
        }

        return $decoded;
    }
}
