<?php

namespace App\Controller\Api;

use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reports')]
class ReportController extends AbstractController
{
    public function __construct(private ReportService $reports)
    {
    }

    #[Route('/vehicle/{id}/mileage', methods: ['GET'])]
    public function mileage(int $id, Request $request): JsonResponse
    {
        $from = $request->query->get('from') ? new \DateTime($request->query->get('from')) : null;
        $to = $request->query->get('to') ? new \DateTime($request->query->get('to')) : null;

        $m = $this->reports->getMileage($id, $from, $to);

        return $this->json(['vehicle' => $id, 'mileage' => $m]);
    }

    #[Route('/vehicle/{id}/fuel-anomalies', methods: ['GET'])]
    public function fuelAnomalies(int $id, Request $request): JsonResponse
    {
        $mult = $request->query->get('multiplier') ? (float)$request->query->get('multiplier') : 2.0;
        $an = $this->reports->detectFuelAnomalies($id, $mult);

        return $this->json(['vehicle' => $id, 'anomalies' => $an]);
    }

    #[Route('/vehicles/costs', methods: ['GET'])]
    public function vehiclesCosts(Request $request): JsonResponse
    {
        $limit = (int)($request->query->get('limit') ?? 10);
        $list = $this->reports->getVehiclesByCosts(null, null, $limit);

        return $this->json($list);
    }
}
