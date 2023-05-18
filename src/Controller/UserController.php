<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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

    #[Route('/api/user/{id}', name: 'api_edit_user', methods: ['PUT'])]
    public function edit(UserRepository $userRepository,int $id, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            $user = $userRepository->find($id);

            if(!$user){
                return $this->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $content = $serializer->deserialize($request->getContent(), User::class, 'json');

            $plaintextPassword = $content->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            if($content->getRoles() === ['ROLE_USER']){
               $roles = $content->getRoles();     
            }else{
                $roles = ['ROLE_USER', $content->getRoles()[0]];
            }

            $user->setEmail($content->getEmail())
                ->setPassword($hashedPassword)
                ->setRoles($roles);

            $manager->flush();

            return $this->json($user, Response::HTTP_ACCEPTED);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/user/{id}', name: 'api_delete_user', methods: ['DELETE'])]
    public function remove(UserRepository $userRepository,int $id, EntityManagerInterface $manager): Response
    {
        $user = $userRepository->find($id);
        if(!$user){
            return $this->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $manager->remove($user);
        $manager->flush();
        return $this->json([
            "status" => Response::HTTP_ACCEPTED,
            "message" => "success delete user"
        ], Response::HTTP_ACCEPTED);
    }
}
