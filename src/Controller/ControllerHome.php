<?php
// src/Controller/ControllerHome.php

namespace App\Controller;

use App\Entity\Webtoon;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ControllerHome extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $jwtToken = $session->get('jwt_token');
        #dd($jwtToken);
        // Récupérer tous les webtoons depuis la base
        $webtoons = $entityManager->getRepository(Webtoon::class)->findAll();

        return $this->render('home/index.html.twig', [
            'webtoons' => $webtoons,
            'user' => $user,
        ]);
    }
    #[Route('/mes-favoris', name: 'user_favoris')]
    public function favoris(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('user/favoris.html.twig', [
            'favoris' => $user->getFavoris(),
        ]);
    }
}
