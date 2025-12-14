<?php

namespace App\Service;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportExportService
{
    public function __construct(private EntityManagerInterface $em, private TripRepository $tripRepo)
    {
    }

    /**
     * Import CSV content (string) with header vehicle,date,distance,fuel,driver
     * Returns array with stats
     */
    public function importTripsFromCsv(string $csv): array
    {
        $rows = str_getcsv($csv, "\n");
        $header = null;
        $created = 0;
        $skipped = 0;

        foreach ($rows as $i => $row) {
            if (trim($row) === '') continue;
            $cols = str_getcsv($row);
            if ($i === 0) { $header = $cols; continue; }
            $data = array_combine($header, $cols);
            $vehicleId = (int)$data['vehicle'];
            $date = new \DateTime($data['date']);
            $distance = (float)$data['distance'];

            $exists = $this->tripRepo->findDuplicateTrip($vehicleId, $date, $distance);
            if ($exists) { $skipped++; continue; }

            $t = new Trip();
            $t->setVehicle($this->em->getReference(\App\Entity\Vehicle::class, $vehicleId));
            if (!empty($data['driver'])) $t->setDriver($this->em->getReference(\App\Entity\Driver::class, (int)$data['driver']));
            $t->setDate($date);
            $t->setDistance($distance);
            $t->setFuelConsumed(isset($data['fuel']) ? (float)$data['fuel'] : null);

            $this->em->persist($t);
            $created++;
        }

        $this->em->flush();

        return ['created' => $created, 'skipped' => $skipped];
    }

    public function exportTripsCsv(array $trips): string
    {
        $out = fopen('php://memory', 'r+');
        fputcsv($out, ['vehicle', 'date', 'distance', 'fuel', 'driver']);
        foreach ($trips as $t) {
            fputcsv($out, [
                $t->getVehicle()->getId(),
                $t->getDate()->format('c'),
                $t->getDistance(),
                $t->getFuelConsumed(),
                $t->getDriver()?->getId(),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $csv;
    }
}
