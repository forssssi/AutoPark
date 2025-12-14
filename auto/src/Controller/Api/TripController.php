<?php

namespace App\Controller\Api;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/trips')]
class TripController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private TripRepository $repo)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser();

        // date filters
        $from = $request->query->get('from') ? new \DateTime($request->query->get('from')) : null;
        $to = $request->query->get('to') ? new \DateTime($request->query->get('to')) : null;

        // If the current user is a driver, only return their trips
        if ($this->isGranted('ROLE_DRIVER') && $user) {
            if ($user instanceof \App\Entity\User) {
                $driver = $user->getDriver();
            } else {
                // fallback: find driver by user identifier (email)
                $driver = $this->em->getRepository(\App\Entity\Driver::class)->findOneBy(['contact' => $user->getUserIdentifier()]);
            }

            if ($driver) {
                $trips = $this->repo->findTripsByDriver($driver->getId(), $from, $to);
            } else {
                $trips = [];
            }
        } else {
            // Allow filtering by driver id (dispatcher/admin)
            if ($request->query->get('driver')) {
                $trips = $this->repo->findTripsByDriver((int)$request->query->get('driver'), $from, $to);
            } else {
                $trips = $this->repo->findBy([]);
            }
        }

        $data = array_map(fn(Trip $t) => [
            'id' => $t->getId(),
            'vehicle' => $t->getVehicle()?->getId(),
            'driver' => $t->getDriver()?->getId(),
            'date' => $t->getDate()?->format('c'),
            'distance' => $t->getDistance(),
            'fuelConsumed' => $t->getFuelConsumed(),
        ], $trips);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Trip $trip): JsonResponse
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_DRIVER') && $user) {
            if ($user instanceof \App\Entity\User) {
                $driver = $user->getDriver();
            } else {
                $driver = $this->em->getRepository(\App\Entity\Driver::class)->findOneBy(['contact' => $user->getUserIdentifier()]);
            }

            if (!$driver || $trip->getDriver()?->getId() !== $driver->getId()) {
                return $this->json(['error' => 'Access denied'], 403);
            }
        }

        return $this->json([
            'id' => $trip->getId(),
            'vehicle' => $trip->getVehicle()?->getId(),
            'driver' => $trip->getDriver()?->getId(),
            'date' => $trip->getDate()?->format('c'),
            'distance' => $trip->getDistance(),
            'fuelConsumed' => $trip->getFuelConsumed(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (empty($data['vehicle']) || empty($data['driver'])) {
            return $this->json(['error' => 'vehicle and driver are required'], 400);
        }

        $t = new Trip();
        $entityManager = $this->em;
        $vehicle = $entityManager->getRepository(\App\Entity\Vehicle::class)->find((int)$data['vehicle']);
        $driver = $entityManager->getRepository(\App\Entity\Driver::class)->find((int)$data['driver']);

        if (!$vehicle || !$driver) {
            return $this->json(['error' => 'vehicle or driver not found'], 400);
        }

        $t->setVehicle($vehicle);
        $t->setDriver($driver);
        $t->setDate(new \DateTime($data['date'] ?? 'now'));
        $t->setDistance((float)($data['distance'] ?? 0));
        $t->setFuelConsumed(isset($data['fuelConsumed']) ? (float)$data['fuelConsumed'] : null);

        $this->em->persist($t);
        $this->em->flush();

        return $this->json(['id' => $t->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Trip $trip): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);
        $em = $this->em;

        if (isset($data['vehicle'])) {
            $vehicle = $em->getRepository(\App\Entity\Vehicle::class)->find((int)$data['vehicle']);
            if (!$vehicle) return $this->json(['error' => 'vehicle not found'], 400);
            $trip->setVehicle($vehicle);
        }
        if (isset($data['driver'])) {
            $driver = $em->getRepository(\App\Entity\Driver::class)->find((int)$data['driver']);
            if (!$driver) return $this->json(['error' => 'driver not found'], 400);
            $trip->setDriver($driver);
        }
        if (isset($data['date'])) $trip->setDate(new \DateTime($data['date']));
        if (isset($data['distance'])) $trip->setDistance((float)$data['distance']);
        if (array_key_exists('fuelConsumed', $data)) $trip->setFuelConsumed($data['fuelConsumed'] !== null ? (float)$data['fuelConsumed'] : null);

        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Trip $trip): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($trip);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
