<?php

namespace App\Controller;

use Exception;
use App\Entity\Product;
use OpenApi\Attributes As OA;
use Doctrine\ORM\EntityManager;
use OpenApi\Annotations\JsonContent;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ProductController extends AbstractController
{

    /**
     * Return all product
     */
    #[OA\Response(
        response: 200,
        description: 'Return all products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class))
        )
    )]
    #[Route('/api/product', name: 'api_show_product', methods:['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->json($products, Response::HTTP_OK,[] , ['groups'=>'product:read']);
    }



    /**
     * Create a new product
     */
    #[OA\RequestBody(
        description:'Create a product',
        required:true,
        content: new Model(type: Product::class)
    )]
    #[OA\Response(
        response : 200,
        description : 'Successful operation',
        content: new Model(type: Product::class)
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
                        
    #[Route('/api/product', name: 'api_add_product', methods:['POST'])]
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator)
    {
        try {
            $newProduct =$serializer->deserialize($request->getContent(), Product::class, 'json');

            $errorValidator = $validator->validate($newProduct);
            if(count($errorValidator) > 0)
            {
                throw new ValidatorException(message: 'Validation errors');
            }

            $entityManagerInterface->persist($newProduct);
            $entityManagerInterface->flush();
            return $this->json($newProduct, Response::HTTP_ACCEPTED);

        } catch (Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update a product
     */
    #[OA\RequestBody(
        description:'Update a product',
        required:true,
        content: new Model(type: Product::class)
    )]
    #[OA\Response(
        response : 200,
        description : 'Successful operation',
        content: new Model(type: Product::class)
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
    #[Route('/api/product/update/{id}', name:'api_update_product', methods:['POST'])]
    public function update(EntityManagerInterface $entityManager,int $id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
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

            $product->setLabel($content->getLabel());
            $product->setDescription($content->getDescription());
            $product->setPrice($content->getPrice());
            $product->setImage($content->getImage());
            $product->setUpdatedAt($content->getUpdatedAt());

            $errorValidator = $validator->validate($product);
            if(count($errorValidator) > 0)
            {
                throw new ValidatorException(message: 'Validation errors');
            }

            $entityManager->flush();

            return $this->json($product, Response::HTTP_ACCEPTED, [], ['groups'=>'product:read']);

        } catch (Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ]);
        } catch (NotFoundHttpException $e)
        {
            return $this->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'product not found'
            ],Response::HTTP_NOT_FOUND);
        }
    }

}


