<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

class DayRepository extends EntityRepository {

  public function findDaysByMonth($date) {

    $emConfig = $this->getEntityManager()->getConfiguration();

    $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
    $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
    
    $qb = $this->createQueryBuilder('D');
    $qb->select('D')
       ->where('YEAR(D.day) = :year')
       ->andWhere('MONTH(D.day) = :month')
       ->orderBy('D.day')
    ;
    
    $qb->setParameter('year', $date->format('Y'))
       ->setParameter('month', $date->format('m'))
    ;

    return $qb->getQuery()->getResult();
  }

  public function findAllOlderDays($date) {

    $emConfig = $this->getEntityManager()->getConfiguration();

    $qb = $this->createQueryBuilder('D');
    $qb->select('D')
       ->where('D.day >= :date')
       ->orderBy('D.day')
    ;
    
    $qb->setParameter('date', $date->format('Y-m-d'))
    ;

    return $qb->getQuery()->getResult();
  }
}
