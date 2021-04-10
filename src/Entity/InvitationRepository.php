<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

class InvitationRepository extends EntityRepository {

  public function findInvitationsByMonth($date) {

    $emConfig = $this->getEntityManager()->getConfiguration();

    $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
    $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
    
    $qb = $this->createQueryBuilder('I');
    $qb->select('I')
       ->leftJoin('I.day', 'D')
       ->addSelect('D')
       ->where('YEAR(D.day) = :year')
       ->andWhere('MONTH(D.day) = :month')
       ->orderBy('D.day')
    ;
    
    $qb->setParameter('year', $date->format('Y'))
       ->setParameter('month', $date->format('m'))
    ;

    return $qb->getQuery()->getResult();
  }

 
}
