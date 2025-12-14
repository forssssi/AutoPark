<?php

namespace App\Repository;

use App\Entity\Refuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RefuelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Refuel::class);
    }

    public function getTotalAmountForVehicle(int $vehicleId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.amount) as total')
            ->andWhere('r.vehicle = :vid')
            ->setParameter('vid', $vehicleId);

        if ($from) $qb->andWhere('r.date >= :from')->setParameter('from', $from);
        if ($to) $qb->andWhere('r.date <= :to')->setParameter('to', $to);

        $res = $qb->getQuery()->getSingleScalarResult();
        return (float)($res ?? 0.0);
    }
}
