<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Response\SearchResponse;
use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

interface SearchRequestInterface
{
    public function search(SearchUrlBuilder $searchUrlBuilder): SearchResponse;
}
