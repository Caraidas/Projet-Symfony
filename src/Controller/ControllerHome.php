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
        
        $webtoons = $entityManager->getRepository(Webtoon::class)->findAll();

        $recommendedWebtoons = [];

        if ($user) {
            $favoris = $user->getFavoris();

            // Compter la fréquence des genres
            $genreCount = [];
            foreach ($favoris as $fav) {
                $genres = $fav->getGenre();
                foreach ($genres as $genre) {
                    $genreCount[$genre->getNom()] = ($genreCount[$genre->getNom()] ?? 0) + 1;
                }
            }

            // Trouver le genre préféré
            arsort($genreCount);
            $favoriteGenre = key($genreCount);

            if ($favoriteGenre) {
                // Rechercher les webtoons du genre préféré que l'utilisateur n’a pas en favori
                $qb = $entityManager->createQueryBuilder();
                $qb->select('w')
                    ->from(Webtoon::class, 'w')
                    ->leftJoin('w.genre', 'g')
                    ->where('g.nom = :genre')
                    ->setParameter('genre', $favoriteGenre);

                $recommendedWebtoons = $qb->getQuery()->getResult();
            }
        }

        return $this->render('home/index.html.twig', [
            'webtoons' => $webtoons,
            'user' => $user,
            'recommendedWebtoons' => $recommendedWebtoons,
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
