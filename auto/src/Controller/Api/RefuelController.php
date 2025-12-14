<?php

namespace App\Controller\Api;

use App\Entity\Refuel;
use App\Repository\RefuelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/refuels')]
class RefuelController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private RefuelRepository $repo)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = $this->repo->findAll();

        $data = array_map(fn(Refuel $r) => [
            'id' => $r->getId(),
            'vehicle' => $r->getVehicle()->getId(),
            'date' => $r->getDate()->format('c'),
            'liters' => $r->getLiters(),
            'amount' => $r->getAmount(),
        ], $items);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Refuel $refuel): JsonResponse
    {
        return $this->json([
            'id' => $refuel->getId(),
            'vehicle' => $refuel->getVehicle()->getId(),
            'date' => $refuel->getDate()->format('c'),
            'liters' => $refuel->getLiters(),
            'amount' => $refuel->getAmount(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $r = new Refuel();
        $r->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle'] ?? null));
        $r->setDate(new \DateTime($data['date'] ?? 'now'));
        $r->setLiters((float)($data['liters'] ?? 0));
        $r->setAmount((float)($data['amount'] ?? 0));

        $this->em->persist($r);
        $this->em->flush();

        return $this->json(['id' => $r->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Refuel $refuel): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['vehicle'])) $refuel->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle']));
        if (isset($data['date'])) $refuel->setDate(new \DateTime($data['date']));
        if (isset($data['liters'])) $refuel->setLiters((float)$data['liters']);
        if (isset($data['amount'])) $refuel->setAmount((float)$data['amount']);

        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Refuel $refuel): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($refuel);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
