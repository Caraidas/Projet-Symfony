<?php
// src/Controller/WebtoonController.php

namespace App\Controller;

use App\Entity\Webtoon;
use App\Entity\Episode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebtoonController extends AbstractController
{
    #[Route('/webtoon/{slug}', name: 'webtoon_show')]
    public function show(string $slug, EntityManagerInterface $em): Response
    {
        $webtoon = $em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug]);

        if (!$webtoon) {
            throw $this->createNotFoundException('Webtoon non trouvé');
        }

        return $this->render('webtoon/show.html.twig', [
            'webtoon' => $webtoon,
            'episodes' => $webtoon->getEpisodes(),
        ]);
    }
    
    #[Route('/{slug}/{id}', name: 'episode_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showEpisode(string $slug, Episode $episode): Response
    {
        $webtoon = $episode->getWebtoon();
        
        if (!$webtoon || $webtoon->getSlug() !== $slug) {
            throw $this->createNotFoundException('Webtoon non trouvé pour cet épisode.');
        }

        return $this->render('webtoon/episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }
}
