<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentUpdate extends AbstractContentUpdate
{
    public function __construct(string $url, ?string $type, array $fields)
    {
        Assert::keyExists($fields, 'title', 'Field title must be provided for $fields');

        parent::__construct($url, $type, $fields);
    }

    public function setNested(array $nested): void
    {
        Assert::allIsInstanceOf($nested, ContentUpdate::class);
        $this->nested = $nested;
    }
}
