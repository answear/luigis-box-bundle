<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Factory\RecommendationsFactory;
use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\RecommendationsCollection;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

class RecommendationsRequest implements RecommendationsRequestInterface
{
    public function __construct(
        private RecommendationsFactory $recommendationsFactory,
        private RecommendationsClient $recommendationsClient,
    ) {
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
            $this->handleResponse($request, $this->recommendationsClient->request($request))
        );
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
