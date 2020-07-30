<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\RemoveUserMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RemoveUserAction {

    private MessageBusInterface $bus;

    public function __construct(\Symfony\Component\Messenger\MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     */
    public function __invoke(int $id): Response {
        $this->bus->dispatch(new RemoveUserMessage($id));
        return new Response('', Response::HTTP_OK);
    }

}