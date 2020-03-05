<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use PHPUnit\Framework\TestCase;

class ContentUpdateTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ValueObjectDataProvider::provideContentUpdateObjects()
     */
    public function createObjectSuccessfully(
        string $url,
        string $type,
        array $fields,
        ?array $autocompleteType = null,
        ?string $generation = null,
        ?array $nested = null
    ): void {
        $object = new ContentUpdate($url, $type, $fields);
        $object->setAutocompleteType($autocompleteType);
        $object->setGeneration($generation);
        $object->setNested($nested ?? []);

        $this->assertSame($url, $object->getUrl());
        $this->assertSame($type, $object->getType());
        $this->assertSame($fields, $object->getFields());
        $this->assertSame($autocompleteType, $object->getAutocompleteType());
        $this->assertSame($generation, $object->getGeneration());
        $this->assertSame($nested ?? [], $object->getNested());
    }

    /**
     * @test
     * @dataProvider \Answear\LuigisBoxBundle\Tests\DataProvider\ValueObjectDataProvider::provideContentUpdateObjectsForException()
     */
    public function createObjectWithFailure(
        string $expectedExceptionMessage,
        string $url,
        string $type,
        array $fields,
        ?array $autocompleteType = null,
        ?string $generation = null,
        ?array $nested = null
    ): void {
        $this->expectExceptionMessage($expectedExceptionMessage);

        $object = new ContentUpdate($url, $type, $fields);
        $object->setAutocompleteType($autocompleteType);
        $object->setGeneration($generation);
        $object->setNested($nested ?? []);

        $this->assertSame($url, $object->getUrl());
        $this->assertSame($type, $object->getType());
        $this->assertSame($fields, $object->getFields());
        $this->assertSame($autocompleteType, $object->getAutocompleteType());
        $this->assertSame($generation, $object->getGeneration());
        $this->assertSame($nested ?? [], $object->getNested());
    }
}
