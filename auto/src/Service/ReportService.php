<?php

namespace App\Service;

use App\Repository\MaintenanceRepository;
use App\Repository\RefuelRepository;
use App\Repository\TripRepository;
use App\Repository\VehicleRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ReportService
{
    public function __construct(
        private TripRepository $tripRepo,
        private RefuelRepository $refuelRepo,
        private MaintenanceRepository $maintRepo,
        private VehicleRepository $vehicleRepo,
        private TagAwareCacheInterface $cache,
        private LoggerInterface $logger
    ) {}

    public function getMileage(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float
    {
        $cacheKey = sprintf('mileage_%d_%s_%s', $vehicleId, $from?->format('Ymd') ?? 'null', $to?->format('Ymd') ?? 'null');
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($vehicleId, $from, $to) {
            $item->tag(['mileage', 'vehicle_' . $vehicleId]);
            return $this->tripRepo->getTotalDistanceForVehicle($vehicleId, $from, $to);
        });
    }

    /**
     * Detect trips where fuel consumption per km > average * multiplier
     * Returns array of trip ids flagged as anomalies
     */
    public function detectFuelAnomalies(int $vehicleId, float $multiplier = 2.0, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $avg = $this->tripRepo->getAverageFuelConsumptionForVehicle($vehicleId, $from, $to) ?? 0.0;
        if ($avg <= 0) {
            return [];
        }

        $trips = $this->tripRepo->findTripsByVehicle($vehicleId, $from, $to);

        $anomalies = [];
        foreach ($trips as $t) {
            if ($t->getDistance() <= 0) continue;
            $consumption = $t->getFuelConsumed() / $t->getDistance();
            if ($consumption > $avg * $multiplier) {
                $anomalies[] = $t->getId();
            }
        }

        return $anomalies;
    }

    public function getVehiclesByCosts(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null, int $limit = 10): array
    {
        $cacheKey = sprintf('vehicles_costs_%s_%s_limit%d', $from?->format('Ymd') ?? 'null', $to?->format('Ymd') ?? 'null', $limit);
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($from, $to, $limit) {
            $item->tag(['vehicles_costs']);
            return $this->vehicleRepo->getRankingByCosts($from, $to, $limit);
        });
    }
}
