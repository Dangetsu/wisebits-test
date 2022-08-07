<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class UpdatingDeletedEntityException extends \Exception implements HumanReadableInterface
{

    public function __construct(
        public readonly string $entityId,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getSystemCode(): string
    {
        return 'updating_deleted_entity';
    }

    public function getReadableMessage(): string
    {
        return "Entity with id {$this->entityId} was deleted, you can't update it.";
    }

    public function getHttpCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}