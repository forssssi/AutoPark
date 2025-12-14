<?php

namespace App\Controller\Api;

use App\Service\ImportExportService;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/import')]
class ImportController extends AbstractController
{
    public function __construct(private ImportExportService $svc, private TripRepository $tripRepo)
    {
    }

    #[Route('/trips', methods: ['POST'])]
    public function importTrips(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $content = $request->getContent();
        $res = $this->svc->importTripsFromCsv($content);

        return $this->json($res);
    }

    #[Route('/trips/export', methods: ['GET'])]
    public function exportTrips(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        if ($request->query->get('driver')) {
            $trips = $this->tripRepo->findTripsByDriver((int)$request->query->get('driver'));
        } else {
            $trips = $this->tripRepo->findAll();
        }

        $csv = $this->svc->exportTripsCsv($trips);

        return $this->json(['csv' => $csv]);
    }
}
