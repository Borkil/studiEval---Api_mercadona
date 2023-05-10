<?php

namespace App\Controller;

use App\Entity\Category;
use OpenApi\Attributes As OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    /**
     * Return all categories
     */
    #[OA\Response(
        response: 200,
        description: 'show all categories',
        content: new Model(type: Category::class, groups: ['category:read'])
    )]
    #[Route('/api/category', name: 'api_show_category', methods:['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->json($categoryRepository->findAll(), Response::HTTP_OK, [], ['groups'=>'category:read']);
    }

    /**
     * Return one categories
     */
    #[OA\Response(
        response: 200,
        description: 'show one category',
        content: new Model(type: Category::class, groups: ['category:read'])
    )]
    #[Route('/api/category/{id}', name: 'api_show_one_category', methods:['GET'])]
    public function showOne(Category $category): Response
    {
        return $this->json($category, Response::HTTP_OK,[], ['groups'=>'category:read']);
    }
    
    /**
     * Create a new category
     */
    #[OA\RequestBody(
        description:'Create a category',
        required: true,
        content: new Model(type: Category::class, groups: ['category:create'])
    )]
    #[OA\Response(
        response : Response::HTTP_CREATED,
        description : 'Successful operation',
        content: new Model(type: Category::class, groups : ['category:read'])
    )]
    #[OA\Response(
        response : Response::HTTP_BAD_REQUEST,
        description : 'Invalide Request',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'status', type:'integer'),
                new OA\Property(property: 'message', type:'string')
            ]
        )
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

        return $this->json($newCategory, Response::HTTP_CREATED, ['Access-Control-Allow-Origin' => '*']);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a new category
     */
    #[OA\RequestBody(
        description:'Create a category',
        required: true,
        content: new Model(type: Category::class, groups: ['category:create'])
    )]
    #[OA\Response(
        response : Response::HTTP_ACCEPTED,
        description : 'Successful operation',
        content: new Model(type: Category::class, groups: ['category:read'])
    )]
    #[OA\Response(
        response : Response::HTTP_BAD_REQUEST,
        description : 'Invalide Request',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'status', type:'integer'),
                new OA\Property(property: 'message', type:'string')
            ]
        )
    )]
    #[Route('/api/category/{id}', name: 'api_update_category', methods: ['PUT', 'OPTIONS'])]
    public function update(EntityManagerInterface $manager, Request $request, CategoryRepository $categoryRepository, int $id, SerializerInterface $serialiser, ValidatorInterface $validator)
    {
        $header = ['Access-Control-Allow-Origin' => '*'];

        if($request->getMethod() === 'OPTIONS'){
            $header['Access-Control-Allow-Methods'] = 'PUT';
            return $this->json([], 200, $header);
        };

        try {
            $categoryUpdate = $categoryRepository->find($id);
            $newCategory = $serialiser->deserialize($request->getContent(), Category::class, 'json');

            $errors = $validator->validate($newCategory);

            if($errors->count() > 0){
                throw new Exception(message: $errors);
            }

            $categoryUpdate->setLabel($newCategory->getLabel());
            $manager->flush();

            return $this->json($categoryUpdate, Response::HTTP_OK, $header);
        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST, $header);

        }   
    }
}
