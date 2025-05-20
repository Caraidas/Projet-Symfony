<?php
// src/Controller/ControllerHome.php

namespace App\Controller;

use App\Entity\Webtoon;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

        $favoris = $user->getFavoris()->toArray();
        
        usort($favoris, function($a, $b) {
            $lastA = $a->getEpisodes()->isEmpty() ? null : $a->getEpisodes()->last()->getCreatedAt();
            $lastB = $b->getEpisodes()->isEmpty() ? null : $b->getEpisodes()->last()->getCreatedAt();

            return $lastB <=> $lastA;
        });

        return $this->render('user/favoris.html.twig', [
            'favoris' => $user->getFavoris(),
            'webtoons' => $favoris,
        ]);
    }

    #[Route('/recherche', name: 'webtoon_search')]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->query->get('q');

        $webtoonRepo = $em->getRepository(Webtoon::class);
        $webtoons = $webtoonRepo->createQueryBuilder('w')
            ->where('w.titre LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('home/search_results.html.twig', [
            'webtoons' => $webtoons,
            'query' => $query,
        ]);
    }

}
