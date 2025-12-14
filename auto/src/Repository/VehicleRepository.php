<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * Returns array of ['vehicle' => vehicleId, 'cost' => totalCost]
     */
    public function getRankingByCosts(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null, int $limit = 10): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // Using raw SQL for performance and simplicity
        $sql = <<<SQL
SELECT v.id as vehicle,
       COALESCE(SUM(m.cost),0) + COALESCE(SUM(r.amount),0) as cost
FROM vehicle v
LEFT JOIN maintenance m ON m.vehicle_id = v.id
LEFT JOIN refuel r ON r.vehicle_id = v.id
WHERE 1=1
SQL;

        $params = [];
        if ($from) {
            $sql .= " AND (m.date >= :from OR r.date >= :from)";
            $params['from'] = $from->format('Y-m-d H:i:s');
        }
        if ($to) {
            $sql .= " AND (m.date <= :to OR r.date <= :to)";
            $params['to'] = $to->format('Y-m-d H:i:s');
        }

        $sql .= " GROUP BY v.id ORDER BY cost DESC LIMIT :limit";
        $params['limit'] = $limit;

        $stmt = $conn->prepare($sql);
        foreach ($params as $k => $v) {
            if ($k === 'limit') $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            else $stmt->bindValue($k, $v);
        }

        $result = $stmt->executeQuery()->fetchAllAssociative();
        return $result;
    }
}
