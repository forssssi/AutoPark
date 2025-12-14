<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\Driver;
use App\Entity\Trip;
use App\Entity\Refuel;
use App\Entity\Maintenance;
use App\Entity\Assignment;
use App\Entity\Route;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $dispatcher = new User();
        $dispatcher->setEmail('dispatcher@example.test');
        $dispatcher->setRoles(['ROLE_DISPATCHER']);
        $dispatcher->setPassword($this->passwordHasher->hashPassword($dispatcher, 'password'));
        $dispatcher->setApiToken(bin2hex(random_bytes(16)));
        $manager->persist($dispatcher);


        $drivers = [];
        for ($i = 1; $i <= 3; $i++) {
            $d = new Driver();
            $d->setFullName("Driver {$i}");
            $d->setLicenseCategory('B');
            $d->setContact('driver' . $i . '@example.test');

            $u = new User();
            $u->setEmail('driver' . $i . '@example.test');
            $u->setRoles(['ROLE_DRIVER']);
            $u->setPassword($this->passwordHasher->hashPassword($u, 'password'));
            $u->setApiToken(bin2hex(random_bytes(16)));
            $manager->persist($u);

            $d->setUser($u);

            $manager->persist($d);
            $drivers[] = $d;
        }


        $vehicles = [];
        for ($i = 1; $i <= 3; $i++) {
            $v = new Vehicle();
            $v->setVin('VIN' . str_pad((string)$i, 10, '0', STR_PAD_LEFT));
            $v->setPlateNumber('AAA' . $i);
            $v->setModel('Model ' . $i);
            $v->setMileage(1000 * $i);
            $manager->persist($v);
            $vehicles[] = $v;
        }


        $route = new Route();
        $route->setStartLocation('Point A');
        $route->setEndLocation('Point B');
        $route->setDistance(42.5);
        $manager->persist($route);


        foreach ($vehicles as $idx => $vehicle) {
            $assignment = new Assignment();
            $assignment->setVehicle($vehicle);
            $assignment->setDriver($drivers[$idx % count($drivers)]);
            $assignment->setStartAt(new \DateTime('-7 days'));
            $manager->persist($assignment);

            $trip = new Trip();
            $trip->setVehicle($vehicle);
            $trip->setDriver($drivers[$idx % count($drivers)]);
            $trip->setDate(new \DateTime('-2 days'));
            $trip->setDistance(120.5 + $idx);
            $trip->setFuelConsumed(12.3 + $idx);
            $manager->persist($trip);

            $refuel = new Refuel();
            $refuel->setVehicle($vehicle);
            $refuel->setDate(new \DateTime('-3 days'));
            $refuel->setLiters(50 + $idx);
            $refuel->setAmount(2000 + $idx * 10);
            $manager->persist($refuel);

            $maintenance = new Maintenance();
            $maintenance->setVehicle($vehicle);
            $maintenance->setDate(new \DateTime('-10 days'));
            $maintenance->setWorkType('Oil change');
            $maintenance->setCost(1500 + $idx * 100);
            $manager->persist($maintenance);
        }

        $manager->flush();
    }
}