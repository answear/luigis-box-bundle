<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Util;

class AuthenticationUtil
{
    private const DATE_STRING = 'D, d M Y H:i:s T';
    private const CONTENT_TYPE = 'application/json; charset=utf-8';

    public static function getRequestHeaders(
        string $publicKey,
        string $privateKey,
        string $httpMethod,
        string $endpoint,
        \DateTimeInterface $date
    ): array {
        $digest = self::digest($privateKey, $httpMethod, $endpoint, $date);

        return [
            'Content-Type' => self::CONTENT_TYPE,
            'date' => self::formatDate($date),
            'Authorization' => "guzzle {$publicKey}:{$digest}",
        ];
    }

    public static function digest(
        string $privateKey,
        string $httpMethod,
        string $endpoint,
        \DateTimeInterface $date
    ): string {
        $contentType = self::CONTENT_TYPE;

        $dateString = self::formatDate($date);
        $data = "{$httpMethod}\n{$contentType}\n{$dateString}\n{$endpoint}";

        return trim(base64_encode(hash_hmac('sha256', $data, $privateKey, true)));
    }

    private static function formatDate(\DateTimeInterface $date): string
    {
        return $date->format(self::DATE_STRING);
    }
}
