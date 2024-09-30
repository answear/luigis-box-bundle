<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use Psr\Http\Message\ResponseInterface;

class TooManyItemsException extends \RuntimeException
{
    public function __construct(
        public readonly ?int $passedObjectsCount,
        public readonly ?int $limit,
        public readonly ?ResponseInterface $response = null,
    ) {
        $message = sprintf('Expect less than or equal %s items. Got %s.', $limit, $passedObjectsCount);

        if (null === $passedObjectsCount || null === $limit) {
            $message = 'To many items in single request.';
        }

        parent::__construct($message);
    }
}
