<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Response\SearchResponse;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class SearchRequest
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SearchFactory
     */
    private $searchFactory;

    public function __construct(
        Client $client,
        SearchFactory $searchFactory
    ) {
        $this->client = $client;
        $this->searchFactory = $searchFactory;
    }

    /**
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     * @throws BadRequestException
     */
    public function search(SearchUrlBuilder $searchUrlBuilder): SearchResponse
    {
        try {
            $request = $this->searchFactory->prepareRequest($searchUrlBuilder);

            return new SearchResponse(
                $searchUrlBuilder->toUrl(),
                $this->handleResponse($request, $this->client->request($request))
            );
        } catch (GuzzleException $e) {
            throw new ServiceUnavailableException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws BadRequestException
     * @throws MalformedResponseException
     */
    private function handleResponse(\GuzzleHttp\Psr7\Request $request, ResponseInterface $response): array
    {
        if (\in_array($response->getStatusCode(), [Response::HTTP_NOT_FOUND, Response::HTTP_BAD_REQUEST], true)) {
            throw new BadRequestException($response, $request);
        }

        $responseText = $response->getBody()->getContents();

        $decoded = null;
        try {
            $decoded = \json_decode($responseText, true, 512, JSON_THROW_ON_ERROR);
            Assert::isArray($decoded);
        } catch (\Throwable $e) {
            throw new MalformedResponseException($e->getMessage(), $response, $request, $e);
        }

        return $decoded;
    }
}
