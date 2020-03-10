<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\ToManyItemsException;
use Answear\LuigisBoxBundle\Exception\ToManyRequestsException;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

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

    /**
     * @throws ToManyRequestsException
     * @throws ToManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        if (\count($objects) > self::CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new ToManyItemsException(\count($objects), self::CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        try {
            $request = $this->contentUpdateFactory->prepareRequest($objects);

            return new ApiResponse(
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ToManyRequestsException
     * @throws ToManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function partialContentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        if (\count($objects) > self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new ToManyItemsException(\count($objects), self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        try {
            $request = $this->partialContentUpdateFactory->prepareRequest($objects);

            return new ApiResponse(
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ToManyRequestsException
     * @throws ToManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentRemoval(ContentRemovalCollection $objects): ApiResponse
    {
        try {
            $request = $this->contentRemovalFactory->prepareRequest($objects);

            return new ApiResponse(
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ContentAvailabilityCollection|ContentAvailability $object
     *
     * @throws ToManyRequestsException
     * @throws ToManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function changeAvailability($object): ApiResponse
    {
        try {
            $request = $this->partialContentUpdateFactory->prepareRequestForAvailability($object);

            return new ApiResponse(
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ToManyRequestsException
     * @throws ToManyItemsException
     * @throws MalformedResponseException
     */
    private function handleResponse(\GuzzleHttp\Psr7\Request $request, ResponseInterface $response): array
    {
        if (Response::HTTP_TOO_MANY_REQUESTS === $response->getStatusCode()) {
            $retryAfter = $response->getHeader('Retry-After');
            $retryAfter = reset($retryAfter);
            throw new ToManyRequestsException((int) $retryAfter, $response);
        }

        if (Response::HTTP_REQUEST_ENTITY_TOO_LARGE === $response->getStatusCode()) {
            throw new ToManyItemsException(null, null, $response);
        }

        $responseText = $response->getBody()->getContents();

        $decoded = null;
        try {
            $decoded = \json_decode($responseText, true, 512, JSON_THROW_ON_ERROR);
            Assert::isArray($decoded);
        } catch (\Throwable $e) {
            throw new MalformedResponseException($e->getMessage(), $responseText, $request, $e);
        }

        return $decoded;
    }
}
