<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository {

  private function findInvoiceByTypeAndSentDateNotNull(InvoiceType $invoiceType) {
    if (is_null($invoiceType))
      $qb = $this->createQueryBuilder('I')
              ->setParameter('invoiceType', $invoiceType)
              ->where('I.invoiceType = :invoiceType')
              ->andWhere('C.sentDate IS NOT NULL')
      ;
    return $qb;
  }

}
