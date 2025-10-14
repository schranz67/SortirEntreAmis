<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     *
     * Fonction de renvoi de la liste des évènements à venir
     *
     * @return Event[]
     */
    public function findUpcomingEvents(int $limit = null): array
    {
         $qb = $this->createQueryBuilder('event')
            ->where('event.start > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('event.start', 'ASC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

         return $qb->getQuery()->getResult();
    }
}
