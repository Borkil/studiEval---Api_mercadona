<?php

namespace App\Controller;

use Exception;
use App\Entity\Category;
use OpenApi\Attributes As OA;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[OA\Tag(name: "Category")]
#[Security(name: 'Bearer')]
class CategoryController extends AbstractController
{
    /**
     * Return all categories
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return a array of category',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(new Model(type: Category::class, groups: ['category:read']))
        )
    )]
    #[OA\Response(
        response:
            Response::HTTP_UNAUTHORIZED,
            ref: '#/components/responses/UnauthorizedError'
    )]
    #[Route('/api/category', name: 'api_show_category', methods:['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->json($categoryRepository->findAll(), Response::HTTP_OK, [], ['groups'=>'category:read']);
    }

    /**
     * Return one category
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'return one category',
        content: new Model(type: Category::class, groups: ['category:read'])
    )]
    #[OA\Response(
        response:
            Response::HTTP_NOT_FOUND,
            ref: '#/components/responses/NotFoundError',
    )]
    #[OA\Response(
        response:
            Response::HTTP_UNAUTHORIZED,
            ref: '#/components/responses/UnauthorizedError'
    )]
    #[Route('/api/category/{id}', name: 'api_show_one_category', methods:['GET'])]
    public function showOne(CategoryRepository $categoryRepository, Int $id): Response
    {   
        $category = $categoryRepository->find($id);
        if(!$category){
            return $this->json([
                "status" => Response::HTTP_NOT_FOUND,
                "message" => "Category not found"
            ]);
        }
        return $this->json($category, Response::HTTP_OK,[], ['groups'=>'category:read']);
    }
    
    /**
     * Create a new category
     */
    #[OA\RequestBody(
        description:'Create a new category',
        required: true,
        content: new Model(type: Category::class, groups: ['category:create'])
    )]
    #[OA\Response(
        response : Response::HTTP_CREATED,
        description : 'Successful operation',
        content: new Model(type: Category::class, groups : ['category:read'])
    )]
    #[OA\Response(
        response:
            Response::HTTP_BAD_REQUEST,
            ref: '#/components/responses/BadRequestError'
    )]
    #[OA\Response(
        response:
            Response::HTTP_UNAUTHORIZED,
            ref: '#/components/responses/UnauthorizedError'
    )]
    #[Route('/api/category', name: 'api_create_category', methods:['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        try {
            $newCategory = $serializer->deserialize($request->getContent(), Category::class, 'json');
            $errors = $validator->validate($newCategory);

            if($errors->count() > 0){
                throw new Exception(message: $errors->get(1));
            }

            $manager->persist($newCategory);
            $manager->flush();

        return $this->json($newCategory, Response::HTTP_CREATED);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a category
     */
    #[OA\RequestBody(
        description:'Update a category',
        required: true,
        content: new Model(type: Category::class, groups: ['category:create'])
    )]
    #[OA\Response(
        response : Response::HTTP_OK,
        description : 'Successful operation',
        content: new Model(type: Category::class, groups: ['category:read'])
    )]
    #[OA\Response(
        response:
            Response::HTTP_BAD_REQUEST,
            ref: '#/components/responses/BadRequestError'
    )]
    #[OA\Response(
        response:
            Response::HTTP_NOT_FOUND,
            ref: '#/components/responses/NotFoundError',
    )]
    #[OA\Response(
        response:
            Response::HTTP_UNAUTHORIZED,
            ref: '#/components/responses/UnauthorizedError'
    )]
    #[Route('/api/category/{id}', name: 'api_update_category', methods: ['PUT'])]
    public function update(EntityManagerInterface $manager, Request $request, CategoryRepository $categoryRepository, int $id, SerializerInterface $serialiser, ValidatorInterface $validator)
    {
        try {
            $categoryUpdate = $categoryRepository->find($id);

            if(!$categoryUpdate){
                return $this->json([
                    "status" => Response::HTTP_NOT_FOUND,
                    "message" => "Category not found"
                ]);
            }

            $newCategory = $serialiser->deserialize($request->getContent(), Category::class, 'json');

            $errors = $validator->validate($newCategory);

            if($errors->count() > 0){
                throw new Exception(message: $errors);
            }

            $categoryUpdate->setLabel($newCategory->getLabel());
            $manager->flush();

            return $this->json($categoryUpdate, Response::HTTP_OK, [], ['groups'=>'category:read']);
        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);

        }   
    }
}
