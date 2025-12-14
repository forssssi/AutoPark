<?php

namespace App\Tests\Service;

use App\Entity\Trip;
use App\Service\ReportService;
use App\Repository\TripRepository;
use App\Repository\RefuelRepository;
use App\Repository\MaintenanceRepository;
use App\Repository\VehicleRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ReportServiceTest extends TestCase
{
    public function testGetMileageUsesTripRepoAndCache()
    {
        $tripRepo = $this->createMock(TripRepository::class);
        $refuelRepo = $this->createMock(RefuelRepository::class);
        $maintRepo = $this->createMock(MaintenanceRepository::class);
        $vehicleRepo = $this->createMock(VehicleRepository::class);

        $cache = $this->createMock(\Symfony\Contracts\Cache\TagAwareCacheInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $tripRepo->expects($this->once())->method('getTotalDistanceForVehicle')->with(1, null, null)->willReturn(123.45);

        $cache->method('get')->willReturnCallback(function ($key, $cb) {
            $item = $this->createMock(\Symfony\Contracts\Cache\ItemInterface::class);
            return $cb($item);
        });

        $service = new ReportService($tripRepo, $refuelRepo, $maintRepo, $vehicleRepo, $cache, $logger);

        $m = $service->getMileage(1);
        $this->assertEquals(123.45, $m);
    }

    public function testDetectFuelAnomaliesFlagsHighConsumption()
    {
        $tripRepo = $this->createMock(TripRepository::class);
        $refuelRepo = $this->createMock(RefuelRepository::class);
        $maintRepo = $this->createMock(MaintenanceRepository::class);
        $vehicleRepo = $this->createMock(VehicleRepository::class);

        $cache = $this->createMock(\Symfony\Contracts\Cache\TagAwareCacheInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $t1 = $this->createMock(Trip::class);
        $t1->method('getId')->willReturn(10);
        $t1->method('getDistance')->willReturn(100.0);
        $t1->method('getFuelConsumed')->willReturn(10.0);

        $t2 = $this->createMock(Trip::class);
        $t2->method('getId')->willReturn(11);
        $t2->method('getDistance')->willReturn(50.0);
        $t2->method('getFuelConsumed')->willReturn(20.0); 

        $tripRepo->method('getAverageFuelConsumptionForVehicle')->willReturn(0.1);
        $tripRepo->method('findTripsByVehicle')->willReturn([$t1, $t2]);

        $cache->method('get')->willReturnCallback(function ($key, $cb) {
            $item = $this->createMock(\Symfony\Contracts\Cache\ItemInterface::class);
            return $cb($item);
        });

        $service = new ReportService($tripRepo, $refuelRepo, $maintRepo, $vehicleRepo, $cache, $logger);

        $an = $service->detectFuelAnomalies(1, 2.0);
        $this->assertEquals([11], $an);
    }
}
