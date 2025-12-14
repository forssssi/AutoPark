<?php

namespace App\Repository;

use App\Entity\Maintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maintenance::class);
    }

    public function getTotalCostForVehicle(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUM(m.cost) as total')
            ->andWhere('m.vehicle = :vid')
            ->setParameter('vid', $vehicleId);

        if ($from) $qb->andWhere('m.date >= :from')->setParameter('from', $from);
        if ($to) $qb->andWhere('m.date <= :to')->setParameter('to', $to);

        $res = $qb->getQuery()->getSingleScalarResult();
        return (float)($res ?? 0.0);
    }
}
