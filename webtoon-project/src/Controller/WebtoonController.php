<?php
// src/Controller/WebtoonController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class WebtoonController extends AbstractController
{

    #[Route('/api/webtoons', methods: ['GET'])]
    public function list(): JsonResponse
    {
        // Exemple de réponse JSON
        return $this->json([
            ['id' => 1, 'title' => 'One Piece'],
            ['id' => 2, 'title' => 'Solo Leveling'],
        ]);
    }
}
