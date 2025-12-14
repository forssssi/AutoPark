<?php

namespace App\Controller\Api;

use App\Service\MapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/map')]
class MapController extends AbstractController
{
    public function __construct(private MapService $mapService)
    {
    }

    #[Route('/route', methods: ['POST'])]
    public function route(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;

        if (!$from || !$to) {
            return $this->json(['error' => 'from and to required'], 400);
        }

        $res = $this->mapService->getRoute($from, $to);

        return $this->json($res);
    }
}
