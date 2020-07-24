<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentUpdate extends AbstractContentUpdate
{
    public function __construct(string $title, string $url, ?string $type, array $fields)
    {
        $fields['title'] = $fields['title'] ?? $title;
        Assert::notEmpty($fields['title'], 'Field title can not be empty');

        parent::__construct($url, $type, $fields);
    }

    public function setNested(array $nested): void
    {
        Assert::allIsInstanceOf($nested, ContentUpdate::class);
        $this->nested = $nested;
    }

    public function getTitle(): string
    {
        return $this->getField('title');
    }
}
