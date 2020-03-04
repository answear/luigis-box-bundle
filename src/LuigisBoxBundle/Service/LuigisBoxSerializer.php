<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\DTO\ObjectsInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LuigisBoxSerializer
{
    private const SERIALIZE_FORMAT = 'json';

    public function serialize(ObjectsInterface $objects): string
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($objects, self::SERIALIZE_FORMAT);
    }
}
