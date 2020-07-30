<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\RemoveUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveUserMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(RemoveUserMessage $message)
    {
        $user = $this->manager->getRepository(User::class)->find($message->getUserId());

        if (null === $user) {
            throw new \InvalidArgumentException('User with ID #' . $message->getUserId() . ' not found');
        }

        $this->manager->remove($user);
        $this->manager->flush();
    }
}
