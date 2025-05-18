<?php
// src/Controller/EpisodeCreationController.php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Image;
use App\Entity\Webtoon;
use App\Repository\WebtoonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class EpisodeCreationController extends AbstractController
{
    #[Route('/api/episode/create/{slug}', name: 'api_create_episode', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function create(
        string $slug,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();

        $webtoon = $em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug]);
        if (!$webtoon || $webtoon->getUser() !== $user) {
            return new JsonResponse(['error' => 'Webtoon introuvable ou non autorisé'], 403);
        }

        $title = $request->request->get('title');
        $number = $request->request->get('number');
        $images = $request->files->get('images');

        if (!$title || !$number || !$images) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        $episode = new Episode();
        $episode->setTitle($title);
        $episode->setNumber((int)$number);
        $episode->setCreatedAt(new \DateTimeImmutable());
        $episode->setWebtoon($webtoon);

        $existingEpisode = $em->getRepository(Episode::class)->findOneBy([
            'webtoon' => $webtoon,
            'number' => $number,
        ]);

        if ($existingEpisode) {
            return new JsonResponse([
                'error' => 'Un épisode avec ce numéro existe déjà pour ce webtoon.'
            ], 409);
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/episodes';

        foreach ($images as $index => $imageFile) {
            $safeName = (new AsciiSlugger())->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
            $newFilename = $safeName . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move($uploadDir, $newFilename);

                $image = new Image();
                $image->setUrl('/uploads/episodes/' . $newFilename);
                $image->setPosition($index + 1);
                $image->setEpisode($episode);

                $em->persist($image);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Erreur upload'], 500);
            }
        }

        

        $em->persist($episode);
        $em->flush();

        return new JsonResponse(['success' => true, 'episodeId' => $episode->getId()], 201);
    }

    #[Route('/webtoon/{slug}/add-episode', name: 'episode_form', methods: ['GET'])]
    public function form(
        string $slug,
        WebtoonRepository $webtoonRepository,
        SessionInterface $session
    ): Response {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('ROLE_AUTHOR');

        $webtoon = $webtoonRepository->findOneBy(['slug' => $slug]);
        if (!$webtoon || $webtoon->getUser() !== $user) {
            throw $this->createAccessDeniedException('Webtoon introuvable ou non autorisé.');
        }

        return $this->render('webtoon/episode/create.html.twig', [
            'webtoon' => $webtoon,
            'jwt_token' => $session->get('jwt_token'),
        ]);
    }

    #[Route('/api/episode/update/{id}', name: 'api_update_episode', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        $episode = $em->getRepository(Episode::class)->find($id);

        if (!$episode || $episode->getWebtoon()->getUser() !== $user) {
            return new JsonResponse(['error' => 'Épisode non trouvé ou non autorisé'], 403);
        }

        $images = $request->files->get('images');
        if (!$images) {
            return new JsonResponse(['error' => 'Aucune image fournie'], 400);
        }

        // Supprimer les anciennes images
        foreach ($episode->getImages() as $image) {
            $em->remove($image);
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/episodes';

        foreach ($images as $index => $imageFile) {
            $safeName = (new AsciiSlugger())->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
            $newFilename = $safeName . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move($uploadDir, $newFilename);

                $image = new Image();
                $image->setUrl('/uploads/episodes/' . $newFilename);
                $image->setPosition($index + 1);
                $image->setEpisode($episode);
                $em->persist($image);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Erreur upload'], 500);
            }
        }

        $em->flush();

        return new JsonResponse(['success' => true, 'episodeId' => $episode->getId()], 200);
    }


    #[Route('/webtoon/{slug}/edit-episode', name: 'episode_edit_form', methods: ['GET'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function editForm(
        string $slug,
        WebtoonRepository $webtoonRepository,
        EntityManagerInterface $em,
        SessionInterface $session
    ): Response {
        $user = $this->getUser();
        $webtoon = $webtoonRepository->findOneBy(['slug' => $slug]);

        if (!$webtoon || $webtoon->getUser() !== $user) {
            throw $this->createAccessDeniedException('Webtoon introuvable ou non autorisé.');
        }

        return $this->render('webtoon/episode/edit.html.twig', [
            'webtoon' => $webtoon,
            'episodes' => $webtoon->getEpisodes(),
            'jwt_token' => $session->get('jwt_token'),
        ]);
    }

}
