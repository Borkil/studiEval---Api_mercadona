<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes As OA;

#[OA\Tag(name: "Login")]
class ApiLoginController extends AbstractController
{   

    #[OA\RequestBody(
        ref: '#/components/requestBodies/CredentialLogin'
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Return an JWT TOKEN',
        content: new OA\JsonContent(
            ref: '#/components/schemas/JWTResponse'
        )
    )]
    #[OA\Response(
        response : 
            Response::HTTP_UNAUTHORIZED,
            ref:'#/components/responses/UnauthorizedError',
    )]
    #[Route('/api/login_check', name: 'app_api_login', methods:['POST'])]
    public function apiLogin(): Response
    {
        $user = $this->getUser();
        dd($user);
        return $this->json([
            'username' => $user->getUserIdentifier(),
            'role' => $user->getRoles()
        ]);
    }
}
