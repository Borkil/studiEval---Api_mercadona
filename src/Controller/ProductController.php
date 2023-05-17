<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Product;
use OpenApi\Attributes As OA;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    /**
     * Return all product
     */
    #[OA\Response(
        response: 200,
        description: 'Return all products order by updated date DESC',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read']))
        )
    )]
    #[Route('/api/product', name: 'api_show_product', methods:['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAllOrderByUpdateDate(), Response::HTTP_OK,[] , ['groups'=>'product:read']);
    }

    /**
     * Return one product
     */
    #[OA\Response(
        response: 200,
        description: 'Return one product',
        content: new Model(type: Product::class, groups: ['product:read'])
    )]

    #[Route('/api/product/{id}', name: 'api_show_one_product', methods:['GET'])]
    public function getOneProduct(Product $product): Response
    {
        return $this->json($product, Response::HTTP_OK,[] , ['groups'=>'product:read']);
    }
    
    /**
     * Create a new product
     */
    #[OA\RequestBody(
        description:'Create a product',
        required:true,
        content: new Model(type: Product::class, groups: ['product:create'])
    )]
    #[OA\Response(
        response : Response::HTTP_CREATED,
        description : 'Successful operation',
        content: new Model(type: Product::class, groups: ['product:read'])
    )]
    #[OA\Response(
        response : Response::HTTP_BAD_REQUEST,
        description : 'Invalide Request',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'status', type:'integer'),
                new OA\Property(property: 'errorMessage', type:'string')
            ]
        )
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/api/product', name: 'api_create_product', methods:['POST'])]
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface,ValidatorInterface $validator, CategoryRepository $categoryRepository)
    {

        try {
            $content = $request->getContent();
            $newProduct = $serializer->deserialize($content, Product::class, 'json');
            $category = $categoryRepository->findOneBy(['label' => $newProduct->getCategory()->getLabel()]);
            $newProduct->setCategory($category);

            $errors = $validator->validate($newProduct);

            if($errors->count() > 0){
                throw new Exception(message: $errors);
            }

            $entityManagerInterface->persist($newProduct);
            $entityManagerInterface->flush();
            return $this->json($newProduct, Response::HTTP_CREATED,[] ,['groups'=>'product:read']);

        } catch (Exception $e) {
            return $this->json(
                ['status' => Response::HTTP_BAD_REQUEST,
                 'message' => $e->getMessage()       
            ],Response::HTTP_BAD_REQUEST, []);
        }

    }

    /**
     * Update a product
     */
    #[OA\RequestBody(
        description:'Update a product',
        required:true,
        content: new Model(type: Product::class, groups:['product:create'])
    )]
    #[OA\Response(
        response : 200,
        description : 'Successful operation',
        content: new Model(type: Product::class, groups: ['product:read'])
    )]
    #[OA\Response(
        response : 400,
        description : 'Invalide Request',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'status', type:'integer'),
                new OA\Property(property: 'errorMessage', type:'string')
            ]
        )
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/api/product/{id}', name:'api_update_product', methods:['PUT'])]
    public function update(EntityManagerInterface $entityManager,int $id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, CategoryRepository $categoryRepository)
    {
        try {

            $product = $entityManager->getRepository(Product::class)->find($id);
            if(!$product)
            {
                return $this->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'product not found'
                ],Response::HTTP_NOT_FOUND);
            }
            

            $content = $serializer->deserialize($request->getContent(), Product::class, 'json');
            $category = $categoryRepository->findOneBy(['label' => $content->getCategory()->getLabel()]);

                        
            $product->setLabel($content->getLabel())
                ->setDescription($content->getDescription())
                ->setPrice($content->getPrice())
                ->setImage($content->getImage())
                ->setCategory($category)
                ->setPercentage($content->getPercentage())
                ->setPriceDeal($content->getPriceDeal())
                ->setFinishDealAt($content->getFinishDealAt())
                ->setIsDeal($content->isIsDeal())
                ->setIsArchive($content->isIsArchive())
                ->setUpdatedAt(new DateTimeImmutable());

            $errors = $validator->validate($product);

            if($errors->count() > 0){
                throw new Exception(message: $errors);
            }

            $entityManager->flush();

            return $this->json($product, Response::HTTP_OK, [] , ['groups'=>'product:read']);

        } catch (Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }



    }

}


