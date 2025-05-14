<?php
// src/Controller/ControllerHome.php

namespace App\Controller;

use App\Entity\Webtoon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerHome extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les webtoons depuis la base
        $webtoons = $entityManager->getRepository(Webtoon::class)->findAll();

        return $this->render('home/index.html.twig', [
            'webtoons' => $webtoons,
        ]);
    }
}
