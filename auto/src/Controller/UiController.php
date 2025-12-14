<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VehicleRepository;
use App\Repository\TripRepository;
use App\Repository\DriverRepository;
use App\Repository\AssignmentRepository;
use App\Repository\MaintenanceRepository;
use App\Repository\RefuelRepository;
use App\Repository\RouteRepository as RouteRepo;
use App\Repository\UserRepository;

class UiController extends AbstractController
{
    #[Route('/vehicles/new', name: 'vehicle_new')]
    public function newVehicle(): Response
    {
        return $this->render('vehicle/new.html.twig');
    }

    #[Route('/vehicles', name: 'vehicle_list')]
    public function listVehicles(VehicleRepository $repo): Response
    {
        return $this->render('vehicle/list.html.twig', ['vehicles' => $repo->findAll()]);
    }

    #[Route('/trips/new', name: 'trip_new')]
    public function newTrip(): Response
    {
        return $this->render('trip/new.html.twig');
    }

    #[Route('/trips', name: 'trip_list')]
    public function listTrips(TripRepository $repo): Response
    {
        return $this->render('trip/list.html.twig', ['trips' => $repo->findAll()]);
    }

    #[Route('/drivers', name: 'driver_list')]
    public function listDrivers(DriverRepository $repo): Response
    {
        return $this->render('driver/list.html.twig', ['drivers' => $repo->findAll()]);
    }

    #[Route('/users', name: 'user_list')]
    public function listUsers(UserRepository $repo): Response
    {
        return $this->render('user/list.html.twig', ['users' => $repo->findAll()]);
    }

    #[Route('/assignments', name: 'assignment_list')]
    public function listAssignments(AssignmentRepository $repo): Response
    {
        return $this->render('assignment/list.html.twig', ['assignments' => $repo->findAll()]);
    }

    #[Route('/maintenances', name: 'maintenance_list')]
    public function listMaintenances(MaintenanceRepository $repo): Response
    {
        return $this->render('maintenance/list.html.twig', ['maintenances' => $repo->findAll()]);
    }

    #[Route('/refuels', name: 'refuel_list')]
    public function listRefuels(RefuelRepository $repo): Response
    {
        return $this->render('refuel/list.html.twig', ['refuels' => $repo->findAll()]);
    }

    #[Route('/routes', name: 'route_list')]
    public function listRoutes(RouteRepo $repo): Response
    {
        return $this->render('route/list.html.twig', ['routes' => $repo->findAll()]);
    }
}
