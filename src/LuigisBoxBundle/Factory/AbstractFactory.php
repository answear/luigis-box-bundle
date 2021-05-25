<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Answear\LuigisBoxBundle\Service\LuigisBoxSerializer;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

abstract class AbstractFactory
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LuigisBoxSerializer
     */
    private $serializer;

    public function __construct(ConfigProvider $configProvider, LuigisBoxSerializer $serializer)
    {
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
    }

    public function prepareRequest(ObjectsInterface $bodyObject): Request
    {
        $now = \DateTime::createFromFormat('U', (string) time());

        return new Request(
            $this->getHttpMethod(),
            new Uri($this->configProvider->getHost() . $this->getEndpoint()),
            $this->configProvider->getAuthorizationHeaders($this->getHttpMethod(), $this->getEndpoint(), $now),
            $this->serializer->serialize($bodyObject)
        );
    }

    abstract protected function getHttpMethod(): string;

    abstract protected function getEndpoint(): string;
}
