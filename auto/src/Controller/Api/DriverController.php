<?php

namespace App\Controller\Api;

use App\Entity\Driver;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/drivers')]
class DriverController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private DriverRepository $repo)
    {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $drivers = $this->repo->findAll();

        $data = array_map(fn(Driver $d) => [
            'id' => $d->getId(),
            'fullName' => $d->getFullName(),
            'licenseCategory' => $d->getLicenseCategory(),
            'contact' => $d->getContact(),
        ], $drivers);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Driver $driver): JsonResponse
    {
        return $this->json([
            'id' => $driver->getId(),
            'fullName' => $driver->getFullName(),
            'licenseCategory' => $driver->getLicenseCategory(),
            'contact' => $driver->getContact(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        $d = new Driver();
        $d->setFullName($data['fullName'] ?? '');
        $d->setLicenseCategory($data['licenseCategory'] ?? null);
        $d->setContact($data['contact'] ?? null);

        $this->em->persist($d);
        $this->em->flush();

        return $this->json(['id' => $d->getId()], 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(Request $request, Driver $driver): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['fullName'])) $driver->setFullName($data['fullName']);
        if (array_key_exists('licenseCategory', $data)) $driver->setLicenseCategory($data['licenseCategory']);
        if (array_key_exists('contact', $data)) $driver->setContact($data['contact']);

        $this->em->flush();

        return $this->json(['ok' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Driver $driver): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $this->em->remove($driver);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
