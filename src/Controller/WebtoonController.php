<?php
// src/Controller/WebtoonController.php

namespace App\Controller;


use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Entity\Webtoon;
use App\Entity\Episode;
use App\Entity\User;
use App\Form\WebtoonType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/webtoon')]
class WebtoonController extends AbstractController
{
    #[Route('/{slug}', name: 'webtoon_show')]
    public function show(string $slug, EntityManagerInterface $em): Response
    {
        $webtoon = $em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug]);

        if (!$webtoon) {
            throw $this->createNotFoundException('Webtoon non trouvé');
        }

        return $this->render('webtoon/show.html.twig', [
            'webtoon' => $webtoon,
            'episodes' => $webtoon->getEpisodes(),
            'isAuthor' => $this->getUser() === $webtoon->getUser(),
        ]);
    }
    
    #[Route('/{slug}/{number}', name: 'episode_detail', requirements: ['number' => '\d+'], methods: ['GET','POST'])]
    public function showEpisode(string $slug, int $number, EntityManagerInterface $em, Request $request): Response
    {
    $webtoon = $em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug]);

    if (!$webtoon) {
        throw $this->createNotFoundException('Webtoon non trouvé.');
    }

    $episode = $em->getRepository(Episode::class)->findOneBy([
        'webtoon' => $webtoon,
        'number' => $number,
    ]);

    if (!$episode) {
        throw $this->createNotFoundException('Épisode non trouvé.');
    }

    // Requête pour épisode précédent
    $episodeRepo = $em->getRepository(Episode::class);
    $previousEpisode = $episodeRepo->createQueryBuilder('e')
        ->where('e.webtoon = :webtoon')
        ->andWhere('e.number < :currentNumber')
        ->setParameter('webtoon', $webtoon)
        ->setParameter('currentNumber', $episode->getNumber())
        ->orderBy('e.number', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

    // Requête pour épisode suivant
    $nextEpisode = $episodeRepo->createQueryBuilder('e')
        ->where('e.webtoon = :webtoon')
        ->andWhere('e.number > :currentNumber')
        ->setParameter('webtoon', $webtoon)
        ->setParameter('currentNumber', $episode->getNumber())
        ->orderBy('e.number', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

    // Crée un nouveau commentaire
    $commentaire = new Commentaire();
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour commenter.");
        }

        $commentaire->setUser($user);
        $commentaire->setEpisode($episode);
        $commentaire->setWebtoon($webtoon);

        $em->persist($commentaire);
        $em->flush();

        return $this->redirectToRoute('episode_detail', [
            'slug' => $slug,
            'number' => $episode->getNumber(),
        ]);
    }

    // Récupère les commentaires pour cet épisode
    $commentaires = $em->getRepository(Commentaire::class)
        ->findBy(['episode' => $episode], ['id' => 'DESC']);


    return $this->render('webtoon/episode/show.html.twig', [
        'episode' => $episode,
        'previousEpisode' => $previousEpisode,
        'nextEpisode' => $nextEpisode,
        'webtoon' => $webtoon,
        'form' => $form->createView(),
        'commentaires' => $commentaires,
        'user' => $this->getUser(),
    ]);

}


    #[Route('/{slug}/edit', name: 'webtoon_edit')]
    public function edit(
        string $slug,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $webtoon = $em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug]);

        if (!$webtoon || $webtoon->getUser() !== $user) {
            throw $this->createAccessDeniedException("Non autorisé.");
        }

        $form = $this->createForm(WebtoonType::class, $webtoon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverFile')->getData();

            if ($coverFile) {
                $newFilename = uniqid() . '.' . $coverFile->guessExtension();
                $coverFile->move($this->getParameter('cover_directory'), $newFilename);
                $webtoon->setCoverUrl('/uploads/webtoons/' . $newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('webtoon_show', ['slug' => $webtoon->getSlug()]);
        }

        return $this->render('webtoon/edit.html.twig', [
            'form' => $form->createView(),
            'webtoon' => $webtoon,
        ]);
    }

    #[Route('/{id}/favori/toggle', name: 'toggle_favori')]
    public function toggleFavori(Webtoon $webtoon, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($user->getFavoris()->contains($webtoon)) {
            $user->removeFavori($webtoon);
        } else {
            $user->addFavori($webtoon);
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('webtoon_show', ['slug' => $webtoon->getSlug()]);
    }

}
