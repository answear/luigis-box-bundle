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
use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailability;
use Answear\LuigisBoxBundle\ValueObject\ContentAvailabilityCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;
use Answear\LuigisBoxBundle\ValueObject\PartialContentUpdate;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class Request implements RequestInterface
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
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        Assert::allIsInstanceOf($objects->getObjects(), ContentUpdate::class);

        if (\count($objects) > self::CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new TooManyItemsException(\count($objects), self::CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        try {
            $request = $this->contentUpdateFactory->prepareRequest($objects);

            return new ApiResponse(
                \count($objects),
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function partialContentUpdate(ContentUpdateCollection $objects): ApiResponse
    {
        Assert::allIsInstanceOf($objects->getObjects(), PartialContentUpdate::class);

        if (\count($objects) > self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT) {
            throw new TooManyItemsException(\count($objects), self::PARTIAL_CONTENT_UPDATE_OBJECTS_LIMIT);
        }

        try {
            $request = $this->partialContentUpdateFactory->prepareRequest($objects);

            return new ApiResponse(
                \count($objects),
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function contentRemoval(ContentRemovalCollection $objects): ApiResponse
    {
        try {
            $request = $this->contentRemovalFactory->prepareRequest($objects);

            return new ApiResponse(
                \count($objects),
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ContentAvailabilityCollection|ContentAvailability $object
     *
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     * @throws TooManyRequestsException
     */
    public function changeAvailability($object): ApiResponse
    {
        try {
            $request = $this->partialContentUpdateFactory->prepareRequestForAvailability($object);

            return new ApiResponse(
                $object instanceof ContentAvailabilityCollection ? \count($object) : 1,
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
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
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws MalformedResponseException
     */
    private function handleResponse(\GuzzleHttp\Psr7\Request $request, ResponseInterface $response): array
    {
        if (Response::HTTP_BAD_REQUEST === $response->getStatusCode()) {
            throw new BadRequestException($response, $request);
        }

        if (Response::HTTP_TOO_MANY_REQUESTS === $response->getStatusCode()) {
            $retryAfter = $response->getHeader('Retry-After');
            $retryAfter = reset($retryAfter);
            throw new TooManyRequestsException((int) $retryAfter, $response);
        }

        if (Response::HTTP_REQUEST_ENTITY_TOO_LARGE === $response->getStatusCode()) {
            throw new TooManyItemsException(null, null, $response);
        }

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
