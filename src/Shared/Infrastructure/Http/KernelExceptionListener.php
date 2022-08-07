<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Exception\DomainResourceNotFoundException;
use App\Shared\Domain\Exception\HumanReadableInterface;
use App\Shared\Domain\Exception\ValidationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\ConstraintViolation;

final class KernelExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }
        if ($exception instanceof ValidationFailedException) {
            $this->processValidationException($event, $exception);
            return;
        }
        if ($exception instanceof HumanReadableInterface) {
            $event->setResponse(new JsonResponse(['code' => $exception->getSystemCode(), 'message' => $exception->getReadableMessage()], $exception->getHttpCode()));
            return;
        }
        if ($exception instanceof DomainResourceNotFoundException || $exception instanceof NotFoundHttpException) {
            $event->setResponse(new JsonResponse(null, Response::HTTP_NOT_FOUND));
            return;
        }
        $e = FlattenException::createFromThrowable($event->getThrowable());
        $this->logger->critical("Uncaught PHP Exception {$e->getClass()}: \"{$e->getMessage()}\" at {$e->getFile()} line {$e->getLine()}", ['exception' => (string)$event->getThrowable()]);
        $event->setResponse(new JsonResponse(['code' => 'internal_error'], Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    private function processValidationException(ExceptionEvent $event, ValidationFailedException $exception): void
    {
        $messages = ['code' => 'validation_failed', 'errors' => []];
        /** @var ConstraintViolation $message */
        foreach ($exception->getViolations() as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }
        $event->setResponse(new JsonResponse($messages, Response::HTTP_BAD_REQUEST));
    }
}