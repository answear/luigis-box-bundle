<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Factory\SearchFactory;
use Answear\LuigisBoxBundle\Response\SearchResponse;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class SearchRequest implements SearchRequestInterface
{
    public function __construct(
        private SearchClient $client,
        private SearchFactory $searchFactory,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function search(SearchUrlBuilder $searchUrlBuilder): SearchResponse
    {
        $request = $this->searchFactory->prepareRequest($searchUrlBuilder);

        $url = $searchUrlBuilder->toUrlQuery();
        Assert::notEmpty($url);

        return new SearchResponse(
            $url . '&v=' . $this->searchFactory->prepareRequestCacheHash(),
            $this->handleResponse($request, $this->client->request($request))
        );
    }

    /**
     * @throws MalformedResponseException
     */
    private function handleResponse(\GuzzleHttp\Psr7\Request $request, ResponseInterface $response): array
    {
        if (\in_array($response->getStatusCode(), [Response::HTTP_NOT_FOUND, Response::HTTP_BAD_REQUEST], true)) {
            throw new BadRequestException($response, $request);
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
