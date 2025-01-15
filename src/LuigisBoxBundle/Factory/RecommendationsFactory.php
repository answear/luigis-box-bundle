<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

class RecommendationsFactory extends AbstractFactory
{
    private const HTTP_METHOD = 'POST';
    private const ENDPOINT = '/' . ConfigProvider::API_VERSION . '/recommend';

    public function __construct(
        private ConfigProvider $configProvider,
        private LuigisBoxSerializer $serializer,
    ) {
        parent::__construct($configProvider, $serializer);
    }

    public function prepareRequest(ObjectsInterface $bodyObject): Request
    {
        $now = \DateTime::createFromFormat('U', (string) time());

        return new Request(
            $this->getHttpMethod(),
            new Uri(
                sprintf(
                    '%s?tracker_id=%s',
                    $this->configProvider->getHost() . self::ENDPOINT,
                    $this->configProvider->getPublicKey(),
                )
            ),
            $this->configProvider->getAuthorizationHeaders($this->getHttpMethod(), $this->getEndpoint(), $now),
            $this->serializer->serialize($bodyObject)
        );
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
