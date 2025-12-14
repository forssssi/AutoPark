<?php

namespace App\Controller\Api;

use App\Entity\Assignment;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/assignments')]
class AssignmentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private AssignmentRepository $repo, private LoggerInterface $assignmentsLogger)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $assignments = $this->repo->findAll();

        $data = array_map(fn(Assignment $a) => [
            'id' => $a->getId(),
            'vehicle' => $a->getVehicle()->getId(),
            'driver' => $a->getDriver()->getId(),
            'startAt' => $a->getStartAt()->format('c'),
            'endAt' => $a->getEndAt()?->format('c'),
        ], $assignments);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Assignment $assignment): JsonResponse
    {
        return $this->json([
            'id' => $assignment->getId(),
            'vehicle' => $assignment->getVehicle()->getId(),
            'driver' => $assignment->getDriver()->getId(),
            'startAt' => $assignment->getStartAt()->format('c'),
            'endAt' => $assignment->getEndAt()?->format('c'),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $a = new Assignment();
        $a->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle'] ?? null));
        $a->setDriver($this->em->getReference(\App\Entity\Driver::class, $data['driver'] ?? null));
        $a->setStartAt(new \DateTime($data['startAt'] ?? 'now'));
        $a->setEndAt(isset($data['endAt']) ? new \DateTime($data['endAt']) : null);

        $this->em->persist($a);
        $this->em->flush();

        $this->assignmentsLogger->info('Assignment created', ['id' => $a->getId(), 'vehicle' => $a->getVehicle()->getId(), 'driver' => $a->getDriver()->getId()]);

        return $this->json(['id' => $a->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['vehicle'])) $assignment->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $data['vehicle']));
        if (isset($data['driver'])) $assignment->setDriver($this->em->getReference(\App\Entity\Driver::class, $data['driver']));
        if (isset($data['startAt'])) $assignment->setStartAt(new \DateTime($data['startAt']));
        if (array_key_exists('endAt', $data)) $assignment->setEndAt($data['endAt'] ? new \DateTime($data['endAt']) : null);

        $this->em->flush();

        $this->assignmentsLogger->info('Assignment updated', ['id' => $assignment->getId()]);

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Assignment $assignment): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($assignment);
        $this->em->flush();

        $this->assignmentsLogger->info('Assignment deleted', ['id' => $assignment->getId()]);

        return $this->json(null, 204);
    }
}
