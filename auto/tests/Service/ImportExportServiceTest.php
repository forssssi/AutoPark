<?php

namespace App\Tests\Service;

use App\Service\ImportExportService;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ImportExportServiceTest extends TestCase
{
    public function testImportSkipsDuplicates()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $tripRepo = $this->createMock(TripRepository::class);

        // simulate no duplicates for first row, duplicate for second
        $duplicateTrip = $this->createMock(\App\Entity\Trip::class);
        $tripRepo->method('findDuplicateTrip')->willReturnOnConsecutiveCalls(null, $duplicateTrip);

        // getReference should return mock entities
        $vehicleMock = $this->createMock(\App\Entity\Vehicle::class);
        $driverMock = $this->createMock(\App\Entity\Driver::class);
        $em->method('getReference')->willReturnCallback(function ($class, $id) use ($vehicleMock, $driverMock) {
            if ($class === \App\Entity\Vehicle::class) return $vehicleMock;
            if ($class === \App\Entity\Driver::class) return $driverMock;
            return null;
        });

        $svc = new ImportExportService($em, $tripRepo);

        $csv = "vehicle,date,distance,fuel,driver\n1,2020-01-01T00:00:00+00:00,10,1,1\n1,2020-01-01T00:00:00+00:00,10,1,1\n";

        $res = $svc->importTripsFromCsv($csv);

        $this->assertEquals(['created' => 1, 'skipped' => 1], $res);
    }
}
