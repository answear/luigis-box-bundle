<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Unit\ValueObject;

use Answear\LuigisBoxBundle\ValueObject\ContentRemoval;
use Answear\LuigisBoxBundle\ValueObject\ContentUpdate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContentUpdateTest extends TestCase
{
    #[Test]
    #[DataProvider('provideContentUpdateObjects')]
    public function createObjectSuccessfully(
        string $url,
        ?string $type,
        array $fields,
        ?array $autocompleteType = null,
        ?string $generation = null,
        ?array $nested = null,
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

    public static function provideContentUpdateObjects(): iterable
    {
        yield [
            'test.url',
            'products',
            [
                'title' => 'test url title',
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
        ];

        yield [
            'test.url2',
            null,
            [
                'title' => 'test url title',
            ],
        ];

        yield [
            'test.url2',
            'categories',
            [
                'title' => 'test url title',
                'availability' => 0,
                'availability_rank' => 14,
            ],
            [
                'categories',
                'autocomplete type 2',
            ],
            'generation 1',
            [
                new ContentUpdate(
                    'title',
                    's',
                    'products',
                    []
                ),
            ],
        ];
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    #[DataProvider('provideContentUpdateObjectsForException')]
    public function createObjectWithFailure(
        string $expectedExceptionMessage,
        string $url,
        string $type,
        array $fields,
        ?array $autocompleteType = null,
        ?string $generation = null,
        ?array $nested = null,
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

    public static function provideContentUpdateObjectsForException(): iterable
    {
        yield [
            'Field title can not be empty',
            'test.url',
            'products',
            [],
        ];

        yield [
            'Field availability must be one of [0, 1]',
            'test.url',
            'products',
            [
                'title' => 'title',
                'availability' => 3,
            ],
        ];

        yield [
            'Field availability_rank must be between 1 and 15',
            'test.url',
            'products',
            [
                'title' => 'title',
                'availability_rank' => 16,
            ],
        ];

        yield [
            'Expected an instance of Answear\LuigisBoxBundle\ValueObject\ContentUpdate. Got: Answear\LuigisBoxBundle\ValueObject\ContentRemoval',
            'test.url',
            'products',
            [
                'title' => 'title',
            ],
            null,
            null,
            [
                new ContentRemoval('', ''),
            ],
        ];
    }
}
