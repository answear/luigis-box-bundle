<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Exception;

use Psr\Http\Message\ResponseInterface;

class TooManyItemsException extends \RuntimeException
{
    /**
     * @var int|null
     */
    private $passedObjectsCount;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    public function __construct(?int $passedObjectsCount, ?int $limit, ?ResponseInterface $response = null)
    {
        $message = sprintf(
            'Expect less than or equal %s items. Got %s.',
            $limit,
            $passedObjectsCount
        );
        if (null === $passedObjectsCount || null === $limit) {
            $message = 'To many items in single request.';
        }

        parent::__construct($message);

        $this->passedObjectsCount = $passedObjectsCount;
        $this->limit = $limit;
        $this->response = $response;
    }

    public function getPassedObjectsCount(): int
    {
        return $this->passedObjectsCount;
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
