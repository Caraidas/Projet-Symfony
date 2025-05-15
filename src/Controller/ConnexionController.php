<?php
// src/Controller/ConnexionController.php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;


class ConnexionController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function loginPage(): Response
    {
        return $this->render('Security/connexion.html.twig');
    }

    #[Route('/store-token', name: 'store_token', methods: ['POST'])]
    public function storeToken(Request $request, SessionInterface $session, Security $security, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        $token = $data['token'] ?? null;
        $security->login($user);
        if ($token) {
            $session->set('jwt_token', $token);
            return $this->json(['status' => 'ok']);
        }
        return $this->json(['error' => 'Token manquant'], 400);
    }

    #[Route(path:"/logout", name:"app_logout", methods: ["POST"])]
    public function logout(Security $security): Response | null
    {
        return $security->logout();
    }
}
