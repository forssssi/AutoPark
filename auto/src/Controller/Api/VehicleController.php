<?php

namespace App\Controller\Api;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/vehicles')]
class VehicleController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private VehicleRepository $repo)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $vehicles = $this->repo->findAll();

        $data = array_map(fn(Vehicle $v) => [
            'id' => $v->getId(),
            'vin' => $v->getVin(),
            'plateNumber' => $v->getPlateNumber(),
            'model' => $v->getModel(),
            'status' => $v->getStatus(),
            'mileage' => $v->getMileage(),
        ], $vehicles);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->json([
            'id' => $vehicle->getId(),
            'vin' => $vehicle->getVin(),
            'plateNumber' => $vehicle->getPlateNumber(),
            'model' => $vehicle->getModel(),
            'status' => $vehicle->getStatus(),
            'mileage' => $vehicle->getMileage(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $v = new Vehicle();
        $v->setVin($data['vin'] ?? '');
        $v->setPlateNumber($data['plateNumber'] ?? null);
        $v->setModel($data['model'] ?? null);
        $v->setStatus($data['status'] ?? 'active');
        $v->setMileage((int)($data['mileage'] ?? 0));

        $errors = $validator->validate($v);
        if (count($errors) > 0) {
            $msgs = [];
            foreach ($errors as $e) {
                $msgs[] = $e->getPropertyPath() . ': ' . $e->getMessage();
            }

            return $this->json(['errors' => $msgs], 400);
        }

        $this->em->persist($v);
        $this->em->flush();

        return $this->json(['id' => $v->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Vehicle $vehicle, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['vin'])) $vehicle->setVin($data['vin']);
        if (array_key_exists('plateNumber', $data)) $vehicle->setPlateNumber($data['plateNumber']);
        if (array_key_exists('model', $data)) $vehicle->setModel($data['model']);
        if (isset($data['status'])) $vehicle->setStatus($data['status']);
        if (isset($data['mileage'])) $vehicle->setMileage((int)$data['mileage']);

        $errors = $validator->validate($vehicle);
        if (count($errors) > 0) {
            $msgs = [];
            foreach ($errors as $e) {
                $msgs[] = $e->getPropertyPath() . ': ' . $e->getMessage();
            }

            return $this->json(['errors' => $msgs], 400);
        }

        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($vehicle);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
