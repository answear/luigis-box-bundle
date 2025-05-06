<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Response\ApiResponse;
use Answear\LuigisBoxBundle\ValueObject\RecommendationsCollection;

interface RecommendationsRequestInterface
{
    /**
     * @experimental This feature is in an experimental stage. Breaking changes may occur without prior notice.
     */
    public function getRecommendations(RecommendationsCollection $recommendationsCollection): ApiResponse;
}
