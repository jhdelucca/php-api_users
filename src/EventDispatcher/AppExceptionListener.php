<?php

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AppExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'exceptionToJson'
        ];
    }

    public function exceptionToJson(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $code = 500;

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        }

        $json = [
            'message' => $exception->getMessage(),
            'status' => $code
        ];

        $event->setResponse(new JsonResponse($json, $code));
    }
}
