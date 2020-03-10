<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exceptions;

use Psr\Http\Message\ResponseInterface;

class ToManyItemsException extends \RuntimeException
{
    /**
     * @var int|null
     */
    private $passedObjects;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    public function __construct(?int $passedObjects, ?int $limit, ?ResponseInterface $response = null)
    {
        $message = sprintf(
            'Expect less than or equal %s items. Got %s.',
            $limit,
            $passedObjects
        );
        if (null === $passedObjects || null === $limit) {
            $message = 'To many items in single request.';
        }

        parent::__construct($message);

        $this->passedObjects = $passedObjects;
        $this->limit = $limit;
        $this->response = $response;
    }

    public function getPassedObjects(): int
    {
        return $this->passedObjects;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
