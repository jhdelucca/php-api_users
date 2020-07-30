<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\CreateUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateUserMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;


    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }


    public function __invoke(CreateUserMessage $message)
    {
        $requestContent = $message->getRequest()->getContent();
        $json = json_decode($requestContent, true);

        $user = new User($json['name'], $json['email']);
        foreach ($json['telephones'] as $telephone) {
            $user->addTelephone($telephone['number']);
        }

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $violations = array_map(fn(ConstraintViolationInterface $violation) => [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ], iterator_to_array($errors));
            return new JsonResponse($violations, Response::HTTP_BAD_REQUEST);
        }

        $this->manager->persist($user);
        $this->manager->flush();

    }
}
