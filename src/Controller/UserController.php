<?php

namespace App\Controller;

use App\Entity\Telephone;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    
    private ValidatorInterface $validator;
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
       
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function listAction(): Response
    {
        $users = $this->manager->getRepository(User::class)->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = $this->userToArray($user);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     */
    public function detailAction(int $id): Response
    {
        $user = $this->manager->getRepository(User::class)->find($id);

        if (null === $user) {
            throw $this->createNotFoundException('User with ID #' . $id . ' not found');
        }

        return new JsonResponse($this->userToArray($user));
    }

    /**
     * @Route("/users/{id}", methods={"PUT"})
     */
    public function updateAction(Request $request, int $id): Response
    {
        $requestContent = $request->getContent();
        $json = json_decode($requestContent, true);

        $user = $this->manager->getRepository(User::class)->find($id);

        if (null === $user) {
            throw $this->createNotFoundException('User with ID #' . $id . ' not found');
        }

        $user->setName($json['name']);
        $user->setEmail($json['email']);

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

        return new Response('', Response::HTTP_OK);
    }

    private function userToArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'created_date' => $user->getCreatedDate(),
            'telephones' => array_map(fn(Telephone $telephone) => [
                'number' => $telephone->getNumber()
            ], $user->getTelephones()->toArray())
        ];
    }
}
