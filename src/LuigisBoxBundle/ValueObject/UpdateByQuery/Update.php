<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

use Webmozart\Assert\Assert;

class Update
{
    /**
     * @var array
     */
    private $fields;

    public function __construct(array $fields)
    {
        Assert::notEmpty($fields);
        $this->fields = $fields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
