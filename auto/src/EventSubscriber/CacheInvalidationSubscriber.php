<?php

namespace App\EventSubscriber;

use App\Entity\Trip;
use App\Entity\Refuel;
use App\Entity\Maintenance;
use App\Entity\Vehicle;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CacheInvalidationSubscriber implements EventSubscriber
{
    public function __construct(private TagAwareCacheInterface $cache)
    {
    }

    public function getSubscribedEvents(): array
    {
        return ['postPersist', 'postUpdate', 'postRemove'];
    }

    private function invalidateIfRelevant(object $entity): void
    {
        if ($entity instanceof Trip || $entity instanceof Refuel || $entity instanceof Maintenance || $entity instanceof Vehicle) {
            // Invalidate general report tags so caches will be recalculated
            $this->cache->invalidateTags(['mileage', 'vehicles_costs']);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->invalidateIfRelevant($args->getObject());
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->invalidateIfRelevant($args->getObject());
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->invalidateIfRelevant($args->getObject());
    }
}
