<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UiController extends AbstractController
{
    #[Route('/vehicles/new', name: 'vehicle_new')]
    public function newVehicle(): Response
    {
        return $this->render('vehicle/new.html.twig');
    }

    #[Route('/trips/new', name: 'trip_new')]
    public function newTrip(): Response
    {
        return $this->render('trip/new.html.twig');
    }
}
