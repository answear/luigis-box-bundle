<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

readonly class UpdateByQuery implements ObjectsInterface
{
    public function __construct(
        public UpdateByQuery\Search $search,
        public UpdateByQuery\Update $update,
    ) {
    }
}
