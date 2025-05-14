<?php
namespace App\Controller;

use App\Entity\Webtoon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiWebtoonController extends AbstractController
{
    #[Route('/api/webtoons', name: 'api_webtoons', methods: ['GET'])]
    public function getWebtoons(EntityManagerInterface $em): JsonResponse
    {
        $webtoons = $em->getRepository(Webtoon::class)->findAll();

        $data = array_map(function (Webtoon $webtoon) {
            return [
                'id' => $webtoon->getId(),
                'titre' => $webtoon->getTitre(),
                'description' => $webtoon->getDescription(),
            ];
        }, $webtoons);

        return $this->json($data);
    }
}
