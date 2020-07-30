<?php


namespace App\Controller;


use App\Entity\User;
use App\Message\CreateUserMessage;
use App\Message\RemoveUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserAction
{
    private MessageBusInterface $bus;
    private EntityManagerInterface $manager;
    private ValidatorInterface $validator;


    public function __construct(MessageBusInterface $bus, EntityManagerInterface $manager, ValidatorInterface $validator)
{
    $this->bus = $bus;
    $this->manager = $manager;
    $this->validator = $validator;
}

    /**
     * @Route("/users", methods={"POST"})
     */
    public function __invoke(Request $request):Response
    {
        $requestContent = $request->getContent();
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

        // enviar email aqui????

        return new Response('', Response::HTTP_CREATED, [
            'Location' => '/users/' . $user->getId()
        ]);
    }

    /**
     public function __invoke(Request $request) :Response
    {
        $this->bus->dispatch(new CreateUserMessage($request));
        return new Response('', Response::HTTP_CREATED);
    }
    **/

}