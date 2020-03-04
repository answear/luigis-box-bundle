<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\Service;

use Answear\LuigisBoxBundle\DTO\ContentUpdateObjects;
use Answear\LuigisBoxBundle\Factory\ContentRemovalFactory;
use Answear\LuigisBoxBundle\Factory\ContentUpdateFactory;
use Answear\LuigisBoxBundle\Factory\PartialContentUpdateFactory;
use Answear\LuigisBoxBundle\Service\Client;
use Answear\LuigisBoxBundle\Service\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideContentUpdateObjects
     */
    public function contentUpdate(ContentUpdateObjects $objects): void
    {
        $requestService = $this->getRequestService();

        $response = $requestService->contentUpdate($objects);
        //TODO write tests
    }

    private function getRequestService(): Request
    {
        $client = $this->createMock(Client::class);
        $contentUpdateFactory = $this->createMock(ContentUpdateFactory::class);
        $partialContentUpdateFactory = $this->createMock(PartialContentUpdateFactory::class);
        $contentRemovalUpdateFactory = $this->createMock(ContentRemovalFactory::class);

        return new Request($client, $contentUpdateFactory, $partialContentUpdateFactory, $contentRemovalUpdateFactory);
    }

    public function provideContentUpdateObjects(): array
    {
        return [
            'ok' => [new ContentUpdateObjects([])],
        ];
    }
}
