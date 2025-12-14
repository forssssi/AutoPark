<?php

namespace App\Controller\Api;

use App\Entity\Route as RouteEntity;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/routes')]
class RouteController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private RouteRepository $repo)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = $this->repo->findAll();

        $data = array_map(fn(RouteEntity $r) => [
            'id' => $r->getId(),
            'startLocation' => $r->getStartLocation(),
            'endLocation' => $r->getEndLocation(),
            'distance' => $r->getDistance(),
        ], $items);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(RouteEntity $route): JsonResponse
    {
        return $this->json([
            'id' => $route->getId(),
            'startLocation' => $route->getStartLocation(),
            'endLocation' => $route->getEndLocation(),
            'distance' => $route->getDistance(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $r = new RouteEntity();
        $r->setStartLocation($data['startLocation'] ?? '');
        $r->setEndLocation($data['endLocation'] ?? '');
        $r->setDistance((float)($data['distance'] ?? 0));

        $this->em->persist($r);
        $this->em->flush();

        return $this->json(['id' => $r->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, RouteEntity $route): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['startLocation'])) $route->setStartLocation($data['startLocation']);
        if (isset($data['endLocation'])) $route->setEndLocation($data['endLocation']);
        if (isset($data['distance'])) $route->setDistance((float)$data['distance']);

        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(RouteEntity $route): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($route);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
