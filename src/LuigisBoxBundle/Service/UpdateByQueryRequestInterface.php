<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Response\UpdateByQuery\UpdateByQueryResponse;
use Answear\LuigisBoxBundle\Response\UpdateByQuery\UpdateByQueryStatusResponse;
use Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

interface UpdateByQueryRequestInterface
{
    public function update(UpdateByQuery $updateByQuery): UpdateByQueryResponse;

    public function getStatus(int $jobId): UpdateByQueryStatusResponse;
}
