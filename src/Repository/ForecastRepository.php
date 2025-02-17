<?php

namespace App\Repository;

use App\Entity\Forecast;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Location;

/**
 * @extends ServiceEntityRepository<Forecast>
 */
class ForecastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forecast::class);
    }

    /**
     * @param Location $location
     * @return Forecast[]
     */
    public function findForecast(Location $location): array {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
            ->where('f.location = :location')
            ->setParameter('location', $location)
            ->andWhere('f.date >= :now')
            ->setParameter('now', date('Y-m-d'))
            ->addOrderBy('f.date', 'asc')
            ;

        $query = $queryBuilder->getQuery();

        $forecasts = $query->getResult();

        return $forecasts;
    }
}
