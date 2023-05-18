<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/api/user', name:'api_show_user', methods:['GET'])]
    public function show(UserRepository $userRepository):Response
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups'=>'user:read']);
    }

    #[Route('/api/user', name: 'api_create_user', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            $content = $request->getContent();
            $newUser = $serializer->deserialize($content, User::class, 'json');
            $plaintextPassword = $newUser->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($newUser, $plaintextPassword);

            $newUser->setPassword($hashedPassword);
            
            $manager->persist($newUser);

            $manager->flush();

            return $this->json($newUser, Response::HTTP_CREATED,['Access-Control-Allow-Origin' => '*']);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST, ['Access-Control-Allow-Origin' => '*']);
        }
    }
}
