<?php
// src/Controller/Api/WebtoonCreationController.php

namespace App\Controller;

use App\Entity\Webtoon;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class WebtoonCreationController extends AbstractController
{
    #[Route('/api/webtoon/create', name: 'api_create_webtoon', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifié'], 401);
        }

        $titre = $request->request->get('titre');

        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($titre);

        $originalSlug = $slug;
        $i = 1;
        while ($em->getRepository(Webtoon::class)->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        $webtoon = new Webtoon();

        $description = $request->request->get('description');
        $genres = $request->request->all('genres'); // tableau d'IDs
        $imageFile = $request->files->get('image');

        if (!$titre || !$description || !$imageFile) {
            return new JsonResponse(['error' => 'Champs obligatoires manquants'], 400);
        }

        // Traitement image
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move($this->getParameter('kernel.project_dir') . '/public/images', $newFilename);
            $webtoon->setImage($newFilename);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'upload'], 500);
        }

        $webtoon->setTitre($titre);
        $webtoon->setDescription($description);
        $webtoon->setUser($user);
        $webtoon->setSlug($slug);
        foreach ($genres as $genreId) {
            $genre = $em->getRepository(Genre::class)->find($genreId);
            if ($genre) {
                $webtoon->addGenre($genre);
            }
        }

        $em->persist($webtoon);
        $em->flush();

        return new JsonResponse(['success' => true, 'id' => $webtoon->getId()], 201);
    }

    #[Route('/webtoon/create', name: 'webtoon_form', methods: ['GET','POST'])]
    public function form(GenreRepository $genreRepo, SessionInterface $session)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('ROLE_AUTHOR');
        $jwtToken = $session->get('jwt_token');

        return $this->render('webtoon/create.html.twig', [
            'genres' => $genreRepo->findAll(),
            'user' => $user,
            'jwt_token' => $jwtToken,
        ]);
    }
}
