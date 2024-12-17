<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\ValueObject\ArrayWrapInterface;
use Answear\LuigisBoxBundle\ValueObject\ObjectsInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer;
use Symfony\Component\Serializer\Serializer;

class LuigisBoxSerializer
{
    private const SERIALIZE_FORMAT = 'json';

    public function serialize(ObjectsInterface|ArrayWrapInterface $objects): string
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new Normalizer\PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize(
            $objects instanceof ArrayWrapInterface ? $objects->getObjects() : $objects,
            self::SERIALIZE_FORMAT,
            [Normalizer\AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
        );
    }
}
