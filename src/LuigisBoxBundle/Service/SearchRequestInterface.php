<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Exception\BadRequestException;
use Answear\LuigisBoxBundle\Exception\MalformedResponseException;
use Answear\LuigisBoxBundle\Exception\ServiceUnavailableException;
use Answear\LuigisBoxBundle\Exception\TooManyItemsException;
use Answear\LuigisBoxBundle\Exception\TooManyRequestsException;
use Answear\LuigisBoxBundle\Response\SearchResponse;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

interface SearchRequestInterface
{
    /**
     * @throws BadRequestException
     * @throws TooManyRequestsException
     * @throws TooManyItemsException
     * @throws ServiceUnavailableException
     * @throws MalformedResponseException
     */
    public function search(SearchUrlBuilder $searchUrlBuilder): SearchResponse;
}
