<?php

namespace NB\PlatformBundle\Repository;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends \Doctrine\ORM\EntityRepository
{
    public function getApplicationsWithAdvert($limit){
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.advert', 'advert')
            ->addSelect('advert')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }
}
