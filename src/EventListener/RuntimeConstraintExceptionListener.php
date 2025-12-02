<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

#[AsEventListener(event: 'kernel.exception', priority: 255)]
class RuntimeConstraintExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = $this->getCode($exception);
        $errors = $this->getErrors($exception);

        $event->setResponse(
            new JsonResponse(
                [
                    "data" => [
                        "code" => $code,
                        "errors" => $errors
                    ]
                ],
                $code
            )
        );
    }

    private function getCode(Throwable $exception): int
    {
        if (method_exists($exception, "getStatusCode")) {
            return Response::$statusTexts[$exception->getStatusCode()]
                ? $exception->getStatusCode()
                : Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return Response::$statusTexts[$exception->getCode()] ?? Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    private function getErrors(Throwable $exception): array
    {
        if (method_exists($exception, "getConstraintViolationList")) {
            return $this->formatConstraintViolationList($exception->getConstraintViolationList());
        }

        if ($decoded = json_decode($exception->getMessage(), true)) {
            return $decoded;
        }

        return [$exception->getMessage()];
    }

    private function formatConstraintViolationList(ConstraintViolationList $list): array
    {
        $errors = [];

        foreach ($list as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
