<?php
// src/Controller/LikeController.php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Webtoon;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    #[Route('/webtoon/{id}/like', name: 'webtoon_like')]
    #[IsGranted('ROLE_USER')]
    public function toggleLike(
        Webtoon $webtoon,
        EntityManagerInterface $em,
        LikeRepository $likeRepo
    ): Response {
        $user = $this->getUser();

        $existingLike = $likeRepo->findOneBy([
            'user' => $user,
            'webtoon' => $webtoon,
        ]);

        if ($existingLike) {
            $em->remove($existingLike);
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setWebtoon($webtoon);
            $like->setLikedAt(new \DateTimeImmutable());
            $em->persist($like);
        }

        $em->flush();

        return $this->redirectToRoute('webtoon_show', ['slug' => $webtoon->getSlug()]);
    }
}
