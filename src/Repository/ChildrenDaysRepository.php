<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ChildrenDaysRepository extends EntityRepository {

  private function queryMealListOfChildrenByDay(\DateTime $date) {
    if (is_null($date))
      $date = new \DateTime();

    // TODO: test $date

    $qb = $this->createQueryBuilder('C');
    return $qb//->select('COUNT(Ch.id) AS Meal, Ch.id')
                    ->leftJoin('C.child', 'Ch')
                    ->addSelect('Ch')
                    ->leftJoin('C.day', 'D')
                    ->addSelect('D')
                    ->leftJoin('Ch.meal', 'M')
                    ->addSelect('M')
                    ->where('D.day = :date')
                    ->andWhere('C.missing = 0')
                    ->setParameter('date', $date)
                    ->orderBy('Ch.grade')
                    ->AddOrderBy('Ch.lastName')
                    ->addOrderBy('Ch.firstName');
  }

  public function findMealListOfChildrenByDayByMeal(\DateTime $date, $meal) {
    if (is_null($meal))
      $meal = 1;
    // TODO: test $meal

    $qb = $this->queryMealListOfChildrenByDay($date);
    $qb->andWhere('M.id = :meal')
            ->setParameter('meal', $meal);

    return $qb->getQuery()->getResult();
  }

  public function findMealListOfChildrenByDayByGrade(\DateTime $date, $grade) {
    if (is_null($grade))
      $grade = 1;
    // TODO: test $meal

    $qb = $this->queryMealListOfChildrenByDay($date);
    $qb->andWhere('Ch.grade = :grade')
            ->setParameter('grade', $grade);

    return $qb->getQuery()->getResult();
  }

  public function findChildrenDaysByMonthByYear($month, $year) {

    $emConfig = $this->getEntityManager()->getConfiguration();
    $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
    $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
      
    $qb = $this->createQueryBuilder('C');
    /*if ($month == 6) {
       $qb->leftJoin('C.child', 'Ch')
            ->addSelect('Ch')
            ->leftJoin('C.day', 'D')
            ->addSelect('D')
            ->where('YEAR(D.day) = :year')
            ->andWhere('MONTH(D.day) in (6,7)')
            //       ->orderBy('Ch.lastName')
            //       ->AddOrderBy('Ch.firstName')
            ->addOrderBy('D.day');
        ; 
    } else {*/
        $qb->leftJoin('C.child', 'Ch')
            ->addSelect('Ch')
            ->leftJoin('C.day', 'D')
            ->addSelect('D')
            ->where('YEAR(D.day) = :year')
            ->andWhere('MONTH(D.day) = :month')
            //       ->orderBy('Ch.lastName')
            //       ->AddOrderBy('Ch.firstName')
            ->addOrderBy('D.day')
            ->setParameter('month', $month);

    //}
    $qb->setParameter('year', $year);
       

    return $qb->getQuery()->getResult();
  }

}
