<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

class UpdateByQuery implements ObjectsInterface
{
    /**
     * @var UpdateByQuery\Search
     */
    private $search;

    /**
     * @var UpdateByQuery\Update
     */
    private $update;

    public function __construct(UpdateByQuery\Search $search, UpdateByQuery\Update $update)
    {
        $this->search = $search;
        $this->update = $update;
    }

    public function getSearch(): UpdateByQuery\Search
    {
        return $this->search;
    }

    public function getUpdate(): UpdateByQuery\Update
    {
        return $this->update;
    }
}
