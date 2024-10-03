<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Factory;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use GuzzleHttp\Psr7\Request;

class UpdateByRequestStatusFactory
{
    private const ENDPOINT = '/' . ConfigProvider::API_VERSION . '/update_by_query';

    public function __construct(private ConfigProvider $configProvider)
    {
    }

    public function prepareRequest(int $jobId): Request
    {
        $now = \DateTime::createFromFormat('U', (string) time());

        return new Request(
            'GET',
            sprintf(
                '%s?job_id=%s',
                $this->configProvider->getHost() . self::ENDPOINT,
                $jobId
            ),
            $this->configProvider->getAuthorizationHeaders('GET', self::ENDPOINT, $now)
        );
    }
}
