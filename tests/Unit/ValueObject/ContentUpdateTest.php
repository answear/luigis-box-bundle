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
        ?string $type,
        array $fields,
        ?array $autocompleteType = null,
        ?string $generation = null,
        ?array $nested = null
    ): void {
        $object = new ContentUpdate($fields['title'] ?? '', $url, $type, $fields);
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
     */
    public function passingFieldTitleFromTitleProperty(): void
    {
        $title = 'Title property';
        $url = 'test.url';
        $type = 'products';
        $fields = [
            'field2' => 'Other field',
        ];

        $object = new ContentUpdate(
            $title,
            $url,
            $type,
            $fields
        );
        $this->assertSame($title, $object->getTitle());
        $this->assertSame($url, $object->getUrl());
        $this->assertSame($type, $object->getType());
        $this->assertSame(
            [
                'field2' => 'Other field',
                'title' => $title,
            ],
            $object->getFields()
        );
    }

    /**
     * @test
     */
    public function passingFieldTitleInsteadOfTitleProperty(): void
    {
        $title = 'Title property';
        $url = 'test.url';
        $type = 'products';
        $fields = [
            'title' => 'Title on fields',
            'field2' => 'Other field',
        ];

        $object = new ContentUpdate(
            $title,
            $url,
            $type,
            $fields
        );
        $this->assertSame($fields['title'], $object->getTitle());
        $this->assertSame($url, $object->getUrl());
        $this->assertSame($type, $object->getType());
        $this->assertSame($fields, $object->getFields());
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

        $object = new ContentUpdate($fields['title'] ?? '', $url, $type, $fields);
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
