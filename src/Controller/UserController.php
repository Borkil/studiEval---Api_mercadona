<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use OpenApi\Attributes As OA;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: "User")]
#[Security(name: 'Bearer')]
class UserController extends AbstractController
{   

    /**
     * Return all user
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return an array of user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/UserRead')
        )
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
    #[Route('/api/user', name:'api_show_user', methods:['GET'])]
    public function show(UserRepository $userRepository):Response
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups'=>'user:read']);
    }

    /**
     * Return one user
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return all user',
        content: new OA\JsonContent(
            ref: '#/components/schemas/UserRead'
        )
    )]
    #[OA\Response(
        response : 
            Response::HTTP_NOT_FOUND,
            ref:'#/components/responses/NotFoundError',
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
    #[Route('/api/user/{id}', name:'api_show_one_user', methods:['GET'])]
    public function showOne(UserRepository $userRepository, Int $id):Response
    {
        $user = $userRepository->find($id);
        if(!$user){
            return $this->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found'
            ]);
        }
        return $this->json($user, Response::HTTP_OK, [], ['groups'=>'user:read']);
    }

    /**
     * Create new user
     */
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Successful operation',
        content: new OA\JsonContent(
            ref: '#/components/schemas/UserRead'
        )
    )]
    #[OA\Response(
        response : 
            Response::HTTP_BAD_REQUEST,
            ref:'#/components/responses/BadRequestError',
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
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

            return $this->json($newUser, Response::HTTP_CREATED);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Edit a user
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new OA\JsonContent(
            ref: '#/components/schemas/UserRead'
        )
    )]
    #[OA\Response(
        response : 
            Response::HTTP_NOT_FOUND,
            ref:'#/components/responses/NotFoundError',
    )]
    #[OA\Response(
        response : 
            Response::HTTP_BAD_REQUEST,
            ref:'#/components/responses/BadRequestError',
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
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

            return $this->json($user, Response::HTTP_OK);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove a user
     */
    #[OA\Response(
        response: 
            Response::HTTP_OK,
            ref: '#/components/responses/RemoveSuccess'
    )]
    #[OA\Response(
        response : 
            Response::HTTP_NOT_FOUND,
            ref:'#/components/responses/NotFoundError',
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
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
            "status" => Response::HTTP_OK,
            "message" => "success delete user"
        ], Response::HTTP_OK);
    }
}
