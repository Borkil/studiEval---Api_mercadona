<?php

namespace App\Controller;

use App\Entity\Product;
use OpenApi\Attributes As OA;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'api_show_product', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return all products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class))
        )
    )]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->json($products, Response::HTTP_OK,[] , ['groups'=>'product:read']);
    }

    #[Route('/api/product', name: 'api_add_product', methods:['POST'])]
    #[OA\RequestBody(
        description:'Create a product',
        required: true,
        content: new Model(type: Product::class)
    )]

    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator)
    {
        try {
            $newProduct =$serializer->deserialize($request->getContent(), Product::class, 'json');

            $errorValidator = $validator->validate($newProduct);
            if(count($errorValidator) > 0)
            {
                return $this->json([
                    'status' => 400,
                    'errors' => $errorValidator
                ]);
            }

            $entityManagerInterface->persist($newProduct);
            $entityManagerInterface->flush();
            return $this->json($newProduct, Response::HTTP_ACCEPTED);

        } catch (NotNormalizableValueException $e) {
            return $this->json([
                'status' => 400,
                'attribute' => $e->getPath(),
                'currentType' => $e->getCurrentType(),
                'expectedType' => $e->getExpectedTypes()
            ], Response::HTTP_BAD_REQUEST);

        } catch (NotEncodableValueException $e)
        {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}


