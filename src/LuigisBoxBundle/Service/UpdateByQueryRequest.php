<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Factory\UpdateByRequestFactory;
use Answear\LuigisBoxBundle\Factory\UpdateByRequestStatusFactory;
use Answear\LuigisBoxBundle\Response\UpdateByQuery\UpdateByQueryResponse;
use Answear\LuigisBoxBundle\Response\UpdateByQuery\UpdateByQueryStatusResponse;
use Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class UpdateByQueryRequest implements UpdateByQueryRequestInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var UpdateByRequestFactory
     */
    private $factory;

    /**
     * @var UpdateByRequestStatusFactory
     */
    private $statusFactory;

    public function __construct(
        Client $client,
        UpdateByRequestFactory $factory,
        UpdateByRequestStatusFactory $statusFactory
    ) {
        $this->client = $client;
        $this->factory = $factory;
        $this->statusFactory = $statusFactory;
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function update(UpdateByQuery $updateByQuery): UpdateByQueryResponse
    {
        $request = $this->factory->prepareRequest($updateByQuery);

        return new UpdateByQueryResponse($this->handleResponse($request, $this->client->request($request)));
    }

    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function getStatus(int $jobId): UpdateByQueryStatusResponse
    {
        $request = $this->statusFactory->prepareRequest($jobId);

        return new UpdateByQueryStatusResponse($this->handleResponse($request, $this->client->request($request)));
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
