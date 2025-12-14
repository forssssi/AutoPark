<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function getTotalDistanceForVehicle(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.distance) as total')
            ->andWhere('t.vehicle = :vid')
            ->setParameter('vid', $vehicleId);

        if ($from) {
            $qb->andWhere('t.date >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('t.date <= :to')->setParameter('to', $to);
        }

        $res = $qb->getQuery()->getSingleScalarResult();

        return (float)($res ?? 0.0);
    }

    public function getAverageFuelConsumptionForVehicle(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): ?float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('AVG(t.fuelConsumed / NULLIF(t.distance,0)) as avg')
            ->andWhere('t.vehicle = :vid')
            ->setParameter('vid', $vehicleId)
            ->andWhere('t.distance > 0')
        ;

        if ($from) $qb->andWhere('t.date >= :from')->setParameter('from', $from);
        if ($to) $qb->andWhere('t.date <= :to')->setParameter('to', $to);

        $res = $qb->getQuery()->getSingleScalarResult();

        return $res !== null ? (float)$res : null;
    }

    /**
     * Return Trip[] for a vehicle in the period
     */
    public function findTripsByVehicle(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.vehicle = :vid')
            ->setParameter('vid', $vehicleId)
            ->orderBy('t.date', 'DESC');

        if ($from) $qb->andWhere('t.date >= :from')->setParameter('from', $from);
        if ($to) $qb->andWhere('t.date <= :to')->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return Trip[] for a driver in the period
     */
    public function findTripsByDriver(int $driverId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.driver = :did')
            ->setParameter('did', $driverId)
            ->orderBy('t.date', 'DESC');

        if ($from) $qb->andWhere('t.date >= :from')->setParameter('from', $from);
        if ($to) $qb->andWhere('t.date <= :to')->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function findDuplicateTrip(int $vehicleId, \DateTimeInterface $date, float $distance): ?Trip
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.vehicle = :vid')
            ->andWhere('t.date = :dt')
            ->andWhere('ABS(t.distance - :dist) < 0.01')
            ->setParameter('vid', $vehicleId)
            ->setParameter('dt', $date, Types::DATETIME_MUTABLE)
            ->setParameter('dist', $distance)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
