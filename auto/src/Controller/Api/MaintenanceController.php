<?php

namespace App\Controller\Api;

use App\Entity\Maintenance;
use App\Repository\MaintenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/maintenances')]
class MaintenanceController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private MaintenanceRepository $repo, private LoggerInterface $maintenanceLogger)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = $this->repo->findAll();

        $data = array_map(fn(Maintenance $m) => [
            'id' => $m->getId(),
            'vehicle' => $m->getVehicle()->getId(),
            'date' => $m->getDate()->format('c'),
            'workType' => $m->getWorkType(),
            'cost' => $m->getCost(),
        ], $items);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Maintenance $maintenance): JsonResponse
    {
        return $this->json([
            'id' => $maintenance->getId(),
            'vehicle' => $maintenance->getVehicle()->getId(),
            'date' => $maintenance->getDate()->format('c'),
            'workType' => $maintenance->getWorkType(),
            'cost' => $maintenance->getCost(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $m = new Maintenance();
        $m->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle'] ?? null));
        $m->setDate(new \DateTime($data['date'] ?? 'now'));
        $m->setWorkType($data['workType'] ?? '');
        $m->setCost((float)($data['cost'] ?? 0));

        $this->em->persist($m);
        $this->em->flush();

        $this->maintenanceLogger->info('Maintenance created', ['id' => $m->getId(), 'vehicle' => $m->getVehicle()->getId(), 'cost' => $m->getCost()]);

        return $this->json(['id' => $m->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Maintenance $maintenance): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['vehicle'])) $maintenance->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle']));
        if (isset($data['date'])) $maintenance->setDate(new \DateTime($data['date']));
        if (isset($data['workType'])) $maintenance->setWorkType($data['workType']);
        if (isset($data['cost'])) $maintenance->setCost((float)$data['cost']);

        $this->em->flush();

        $this->maintenanceLogger->info('Maintenance updated', ['id' => $maintenance->getId()]);

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Maintenance $maintenance): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($maintenance);
        $this->em->flush();

        $this->maintenanceLogger->info('Maintenance deleted', ['id' => $maintenance->getId()]);

        return $this->json(null, 204);
    }
}
