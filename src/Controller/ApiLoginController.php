<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login_check', name: 'app_api_login')]
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
