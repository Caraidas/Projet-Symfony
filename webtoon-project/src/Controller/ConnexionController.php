<?php
// src/Controller/ConnexionController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ConnexionController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function loginPage(): Response
    {
        return $this->render('Security/connexion.html.twig');
    }

    #[Route('/store-token', name: 'store_token', methods: ['POST'])]
    public function storeToken(Request $request, SessionInterface $session): Response
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if ($token) {
            $session->set('jwt_token', $token);
            return $this->json(['status' => 'ok']);
        }

        return $this->json(['error' => 'Token manquant'], 400);
    }
}
