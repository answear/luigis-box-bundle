<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\ContentRemovalCollection;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdateCollection;

interface RequestInterface
{
    public function contentUpdate(ContentUpdateCollection $objects): ApiResponse;

    public function partialContentUpdate(ContentUpdateCollection $objects): ApiResponse;

    public function contentRemoval(ContentRemovalCollection $objects): ApiResponse;

    public function changeAvailability($object): ApiResponse;
}
