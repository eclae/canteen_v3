<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

class ContactRepository extends EntityRepository {

  public function findEmails() {

    $emConfig = $this->getEntityManager()->getConfiguration();

    $qb = $this->createQueryBuilder('C');
    $qb->select('C.email')
    ;

    return $qb->getQuery()->getResult();
  }

}

