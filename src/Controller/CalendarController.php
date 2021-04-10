<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Child;
use App\Entity\User;
use App\Entity\InvoiceType;
use App\Entity\Day;
use App\Entity\Invoice;
use App\Entity\ChildrenDays;

/**
 * Calendar controller.
 *
 * @Route("/calendar")
 */
class CalendarController extends AbstractController {

  private $authDiffDay = 1; //days : PAY ATTENTION NORMALLY THE RIGHT VALUE IS 1
  private $authHour = 8;

  private function getNextOpenedDay(\DateTime $date, $interval) {
    $today_dt = new \DateTime();
    $today_dt->setTime(0, 0, 0);
    
        if ($today_dt < new \DateTime($today_dt->format('Y')."-06-16") or $today_dt > new \DateTime($today_dt->format('Y')."-08-15")) {

            $__em = $this->getDoctrine()->getManager();
            $_allOpenedDays = $__em->getRepository(Day::class)->findAllOlderDays($date);
            $_todayDay = current($_allOpenedDays);
            $_lastOpenedDay = clone(end($_allOpenedDays)->getDay());
            reset($_allOpenedDays);
            $Interval_dt = new \DateInterval("P${interval}D");
            $_oneDayInterval = new \DateInterval("P1D");

            if ($_todayDay->getDay() == $today_dt) {
                if (date('H') >= $this->authHour) {
                    $interval++;
                } 
                
            }
            
            for ($i=0; $i<$interval; $i++) {
                        next($_allOpenedDays);
            }
            $return_value = current($_allOpenedDays);
            
            #if ($return_value->getDay()->format('w') == 3) {
            #    # RPC does not deliver food on Wednesday
            #    next($_allOpenedDays);  
            #}
            
            if ($return_value) {
                return $return_value->getDay();
            }

            return $_lastOpenedDay->add($_oneDayInterval);
        } else {
            // no change until september
            $return_date = $today_dt;
            $return_date->setDate($today_dt->format('Y'), 8, 15);
            return $return_date;
        }
 
  }

  /**
   * Lists all Child entities.
   *
   * @Route("/admin/openedDays", name="admin_opened_days")
   * @Method("GET")
   * @Template("Calendar/indexAdminOpenedDays.html.twig")
   * @IsGranted("ROLE_USER_ENABLED")
   */
  public function indexAdminOpenedDaysAction() {
//$em = $this->getDoctrine()->getManager();

    return array();
  }

  /**
   * Lists all Child entities.
   *
   * @Route("/", name="show_child_list_calendar")
   * @Method("GET")
   * @Template("Calendar/indexShowChildCalendar.html.twig")
   * @IsGranted("ROLE_USER_ENABLED")
   */
  public function indexShowChildCalendarAction() {
    return array(
        'entities' => $this->getUser()->getChildren(),
    );
  }

  /**
   * Provide the calendar for a child.
   *
   * @Route("/{id}/{month}/{year}", requirements={"id" = "\d+", "month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="show_child_calendar")
   * @Method("GET")
   * @Template("Calendar/showChildCalendar.html.twig")
   * @IsGranted("ROLE_USER_ENABLED")
   */
  public function showChildCalendarAction(Request $request, $id, $month, $year) {


	  setlocale(LC_TIME,"fr_FR.UTF-8");
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $child = $em->getRepository(Child::class)->find($id);
    $is_invoiced = false;
    $invoiceType_day = $em->getRepository(InvoiceType::class)->findByName('day');

    if ($user->hasChild($child) or $this->isGranted('ROLE_ADMIN')) {

      
      $firstDayOfMonth_dt = new \DateTime("$year-$month-01");
      //if (count($em->getRepository('Invoice')->findBy(array('date' => $firstDayOfMonth_dt, 'child' => $child))) == 1) {
      if (count($em->getRepository(Invoice::class)->findBy(array('date' => $firstDayOfMonth_dt, 'type' => $invoiceType_day))) > 0) {
        $is_invoiced = true;
      }

      $lastDayOfMonth_dt = new \DateTime($firstDayOfMonth_dt->format('Y-m-t'));

      $today_dt = new \DateTime();
      $today_dt->setTime(8, 0, 0);
      $authDay_dt = $this->getNextOpenedDay($today_dt, $this->authDiffDay);
      $noOpenedDay = true;

      if ($lastDayOfMonth_dt > $authDay_dt) {
        $inTheFutur = 1;
      } else {
        $inTheFutur = 0;
      }

      for ($i = $firstDayOfMonth_dt->format('j'); $i <= $lastDayOfMonth_dt->format('j'); $i++) {
        $classesSpan[$i] = '';
        $classesTd[$i] = '';
      }


      $allOpenedDays = $em->getRepository(Day::class)->findBy(array(), array('day' => 'ASC'));
      foreach ($allOpenedDays as $openedDay) {

        if ($openedDay->getDay()->format('n') == $month and $openedDay->getDay()->format('Y') == $year) {
          if (!$is_invoiced and ($openedDay->getDay() >= $authDay_dt or $this->isGranted('ROLE_ADMIN'))) {
// CHANGE THIS THE FIRST DAY of THE YEAR
            //if (1) {
            $classesTd[$openedDay->getDay()->format('j')] = 'toggleDay';
            $noOpenedDay = false;
            $classesSpan[$openedDay->getDay()->format('j')] = 'badge rounded-pill';
          } else {
            $classesSpan[$openedDay->getDay()->format('j')] = ' badge rounded-pill bg-passed';
          }
        }
      }



      $allEatenDays = $em->getRepository(ChildrenDays::class)->findBy(array('child' => $id));
      foreach ($allEatenDays as $eatenDay) {
        if ($eatenDay->getDay()->getDay()->format('n') == $month and $eatenDay->getDay()->getDay()->format('Y') == $year) {
          if ($eatenDay->getMissing()) {
            $classesSpan[$eatenDay->getDay()->getDay()->format('j')] = 'badge rounded-pill bg-important';
          } elseif ($eatenDay->getSurcharge()) {
            $classesSpan[$eatenDay->getDay()->getDay()->format('j')] = 'badge rounded-pill bg-primary';
          } else {
            $classesSpan[$eatenDay->getDay()->getDay()->format('j')] = 'badge rounded-pill bg-success';
          }
          if (!$is_invoiced and ($eatenDay->getDay()->getDay() >= $authDay_dt or $this->isGranted('ROLE_ADMIN'))) {
            $classesTd[$eatenDay->getDay()->getDay()->format('j')] = 'toggleDay';
            $noOpenedDay = false;
          } else {
            $classesSpan[$eatenDay->getDay()->getDay()->format('j')] .= '-passed';
          }
        }
      }
//$_oneMonth = 36 * 24 * 60 * 60;
      $previousMonthDate_dt = new \DateTime("$year-$month-01");
      $nextMonthDate_dt = new \DateTime("$year-$month-01");
      $nextMonthDate_dt->add(new \DateInterval('P1M'));
      $previousMonthDate_dt->sub(new \DateInterval('P1M'));

      return array(
          'classesSpan' => $classesSpan,
          'classesTd' => $classesTd,
          'firstDayOfTheMonth' => $firstDayOfMonth_dt->format('N') - 1,
          'lastDayOfTheMonth' => $lastDayOfMonth_dt->format('j'),
          'nextMonthYear' => $nextMonthDate_dt,
          'previousMonthYear' => $previousMonthDate_dt,
          'actualMonthYear' => strftime('%B %Y', $firstDayOfMonth_dt->getTimestamp()),
          '_month' => $lastDayOfMonth_dt->format('m'),
          '_year' => $lastDayOfMonth_dt->format('Y'),
          'weekDayName' => array(
              'cantine.calendar.dayname.short.mon',
              'cantine.calendar.dayname.short.tue',
              'cantine.calendar.dayname.short.wed',
              'cantine.calendar.dayname.short.thu',
              'cantine.calendar.dayname.short.fri',
              'cantine.calendar.dayname.short.sat',
              'cantine.calendar.dayname.short.sun'),
          'childID' => $id,
          'childName' => $child->__toString(),
          'inTheFutur' => $inTheFutur,
          'noOpenedDay' => $noOpenedDay,
      );
    } else {
      throw new AccessDeniedException();
    }
  }

  /**


   * Provide the a calendar for the admin managing the opened.
   *
   * @Route("/admin/openedDays/{month}/{year}", requirements = {"month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name = "admin_opened_days_calendar")
   * @Method("GET")
   * @Template("Calendar/showOpenedDaysCalendar.html.twig")
   * @IsGranted("ROLE_ADMIN")
   */
  public function showOpenedDaysCalendarAction(Request $request, $month, $year) {

    setlocale(LC_TIME,"fr_FR.UTF-8");
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();

   // if ($user->isAdmin()) {

      $classes = array();
      $firstDayOfMonth_dt = new \DateTime("$year-$month-01");
      $lastDayOfMonth_dt = new \DateTime($firstDayOfMonth_dt->format('Y-m-t'));
      for ($i = $firstDayOfMonth_dt->format('j'); $i <= $lastDayOfMonth_dt->format('j'); $i++) {
        $classes[$i] = 'badge rounded-pill';
      }

      $allOpenedDays = $em->getRepository(Day::class)->findAll();
      foreach ($allOpenedDays as $openedDay) {

        if ($openedDay->getDay()->format('n') == $month and $openedDay->getDay()->format('Y') == $year) {
          $classes[$openedDay->getDay()->format('j')] = 'badge rounded-pill bg-success';
        }
      }



      $previousMonthDate_dt = new \DateTime("$year-$month-01");
      $nextMonthDate_dt = new \DateTime("$year-$month-01");
      $nextMonthDate_dt->add(new \DateInterval('P1M'));
      $previousMonthDate_dt->sub(new \DateInterval('P1M'));

      return array(
          'classes' => $classes,
          'firstDayOfTheMonth' => $firstDayOfMonth_dt->format('N') - 1,
          'lastDayOfTheMonth' => $lastDayOfMonth_dt->format('j'),
          'nextMonthYear' => $nextMonthDate_dt,
          'previousMonthYear' => $previousMonthDate_dt,
          'actualMonthYear' => strftime('%B %Y', $firstDayOfMonth_dt->getTimestamp()),
          '_month' => $lastDayOfMonth_dt->format('m'),
          '_year' => $lastDayOfMonth_dt->format('Y'),
          'weekDayName' => array(
              'cantine.calendar.dayname.short.mon',
              'cantine.calendar.dayname.short.tue',
              'cantine.calendar.dayname.short.wed',
              'cantine.calendar.dayname.short.thu',
              'cantine.calendar.dayname.short.fri',
              'cantine.calendar.dayname.short.sat',
              'cantine.calendar.dayname.short.sun'),
      );
   /* } else {
      throw new AccessDeniedException();
    }*/
  }

  /**
   * Provide the day for a child.
   *
   * @Route("/{id}/{day}/{month}/{year}", requirements={"id" = "\d+", "day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]", "month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="toggle_child_day")
   * @Method({"UPDATE","GET"})
   * @Template("Calendar/toggleDay.html.twig")
   * @IsGranted("ROLE_USER_ENABLED")
   */
  public function toggleChildDayAction(Request $request, $id, $day, $month, $year) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $child = $em->getRepository(Child::class)->find($id);
    $day_dt = new \DateTime("$day-$month-$year");
    $today_dt = new \DateTime();
    $today_dt->setTime(0, 0, 0);
    $authDay_dt = $this->getNextOpenedDay($today_dt, $this->authDiffDay);
    $is_invoiced = false;

    $response = new JsonResponse();
    $spanClass = '';

    $firstDayOfMonth_dt = new \DateTime("$year-$month-01");
     
    if (count($em->getRepository(Invoice::class)->findBy(array('date' => $firstDayOfMonth_dt, 'child' => $child))) == 1) {
      $is_invoiced = true;
    }

//CHANGE THIS THE FIRST DAY OF THE YEAR  ____________________________________________________________________
    // if ($user->isAdmin() or ($user->hasChild($child))) { // and $day_dt >= $authDay_dt)) {
    if (($this->isGranted('ROLE_ADMIN') and !$is_invoiced) or ($user->hasChild($child) and $day_dt >= $authDay_dt and !$is_invoiced)) {
      // log action
      $childrenDaysChangeLog = new App\Entity\ChildrenDaysChangeLog();

      $childrenDaysChangeLog->setChild($child);
      $childrenDaysChangeLog->setUser($user);

      foreach ($child->getDays() as $_day) {
        if ($_day->getDay()->getDay() == $day_dt) {
          if ($this->isGranted('ROLE_ADMIN')) {
            if ($day_dt <= $today_dt) { //and ($_day->getMissing() == false)) {
              if ($_day->getMissing() == true) {
                $spanClass = 'badge rounded-pill';
                $em->remove($_day);
                $childrenDaysChangeLog->setDay($_day->getDay());
                $childrenDaysChangeLog->setDeletion(true);
              } elseif ($_day->getSurcharge() == true) {
                $_day->setMissing(false);
                $_day->setSurcharge(false);
                $childrenDaysChangeLog->setMissing(false);
                $childrenDaysChangeLog->setSurcharge(false);
                $childrenDaysChangeLog->setCreation(true);
                $spanClass = 'badge rounded-pill bg-success';
              } elseif ($_day->getMissing() == false and $_day->getSurcharge() == false) {
                $_day->setMissing(true);
                $childrenDaysChangeLog->setMissing(true);
                $spanClass = 'badge rounded-pill bg-important';
              }
              $childrenDaysChangeLog->setDay($_day->getDay());
            } else {
              $spanClass = 'badge rounded-pill';
              $em->remove($_day);
              $childrenDaysChangeLog->setDay($_day->getDay());
              $childrenDaysChangeLog->setDeletion(true);
            }
          } else { // is not Admin
            if ($day_dt >= $authDay_dt) {
              $spanClass = 'badge rounded-pill';
              $em->remove($_day);
              $childrenDaysChangeLog->setDay($_day->getDay());
              $childrenDaysChangeLog->setDeletion(true);
            } else {
              $spanClass = 'badge rounded-pill bg-success-passed';
            }
          }
          $em->persist($childrenDaysChangeLog);
          $em->flush();
          $response->setData(array(
              'span' => $this->renderView('Calendar:viewMacroSpanDay.html.twig', array('class' => $spanClass, 'value' => $day)
              ),
          ));
          return $response;
        }
      }

      if (count($openedDay = $em->getRepository(Day::class)->findByDay($day_dt)) == 1) {
        $childrenDays = new App\Entity\ChildrenDays();
        $childrenDays->setChild($child);
        $childrenDays->setDay($openedDay[0]);
        $em->persist($childrenDays);
        $childrenDaysChangeLog->setDay($openedDay[0]);
        $childrenDaysChangeLog->setCreation(true);
        $em->persist($childrenDaysChangeLog);
        $spanClass = 'badge rounded-pill bg-success';
        if ($this->isGranted('ROLE_ADMIN') and $day_dt <= $today_dt) {
          $childrenDays->setSurcharge(true);
          $spanClass = 'badge rounded-pill bg-primary';
          $childrenDaysChangeLog->setSurcharge(true);
        }
        $em->flush();

        $response->setData(array(
            'span' => $this->renderView('Calendar:viewMacroSpanDay.html.twig', array('class' => $spanClass, 'value' => $day)
            ),
        ));
        return $response;
      }


      $response->setData(array(
          'span' => $this->renderView('Calendar:viewMacroSpanDay.html.twig', array('class' => $spanClass, 'value' => $day)
          ),
      ));
      return $response;
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Provide the day for a child.
   *
   * @Route("/all/{id}/{month}/{year}", requirements={"id" = "\d+", "month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="toggle_child_days")
   * @Method({"UPDATE","GET"})
   * @IsGranted("ROLE_USER_ENABLED")
   */
  public function toggleChildDaysOfMonthAction(Request $request, $id, $month, $year) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $child = $em->getRepository(Child::class)->find($id);


    $today_dt = new \DateTime();
    $authDay_dt = $this->getNextOpenedDay($today_dt, $this->authDiffDay);
    $firstDayOfTheMonth = new \DateTime("$year-$month-01");
    $lastDayOfTheMonth = new \DateTime($firstDayOfTheMonth->format('Y-m-t'));
    $alreadyAllDaysSelected = 1;
    $is_invoiced = false;
    
    $firstDayOfMonth_dt = new \DateTime("$year-$month-01");
    
    if (count($em->getRepository(Invoice::class)->findBy(array('date' => $firstDayOfMonth_dt, 'child' => $child))) == 1) {
      $is_invoiced = true;
    }

    if (!$is_invoiced and ($user->hasChild($child) or $this->isGranted('ROLE_ADMIN'))) {
      if ($lastDayOfTheMonth <= $authDay_dt) {
//$this->get('session')->getFlashBag()->add('error', 'Demande dans le passé : impossible !');
      } else {
        $allOpenedDays = $em->getRepository(Day::class)->findAll();
        foreach ($allOpenedDays as $openedDay) {
          if ($openedDay->getDay()->format('n') == $month and $openedDay->getDay()->format('Y') == $year) {
            if ($openedDay->getDay() >= $authDay_dt) {
              $childrenDays = $em->getRepository(ChildrenDays::class)->findBy(array('child' => $child, 'day' => $openedDay));
              if (!count($childrenDays)) {
                $childrenDays_temp = new ChildrenDays($child, $openedDay);
                $child->addDay($childrenDays_temp);
                // log action
                $childrenDaysChangeLog = new ChildrenDaysChangeLog();
                $childrenDaysChangeLog->setChild($child);
                $childrenDaysChangeLog->setUser($user);
                $childrenDaysChangeLog->setCreation(true);
                $childrenDaysChangeLog->setDay($childrenDays_temp->getDay());
                $em->persist($childrenDaysChangeLog);


                $alreadyAllDaysSelected = 0;
              } else {

              }
            }
          }
        }
//$em->persist($child);
        $em->flush();
        if ($alreadyAllDaysSelected) {
          $this->get('session')->getFlashBag()->add('notice', 'cantine.calendar.flash.noMoreSelectableDay');
        } else {
          $this->get('session')->getFlashBag()->add('notice', 'cantine.calendar.flash.update');
        }
      }
      return $this->redirect($this->generateUrl('show_child_calendar', array('id' => $id, 'month' => $month, 'year' => $year)));
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Provide the day for opened days
   *
   * @Route("/admin/openedDays/{day}/{month}/{year}", requirements={"day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]", "month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="toggle_opened_day")
   * @Method("UPDATE")
   * @Template("Calendar/toggleDay.html.twig")
   * @IsGranted("ROLE_ADMIN")
   */
  public function toggleOpenedDayAction(Request $request, $day, $month, $year) {

    $em = $this->getDoctrine()->getManager();
    $day_dt = new \DateTime("$day-$month-$year");


    if (count($openedDay = $em->getRepository(Day::class)->findByDay($day_dt)) == 1) {

      if (count($children = $em->getRepository(ChildrenDays::class)->findByDay($openedDay))) {
        return array(
            'response' => 'badge rounded-pill bg-success',
            'day' => $day,
            'children' => $children
        );
      } else {
        if (count($logs = $em->getRepository(ChildrenDaysChangeLog::class)->findByDay($openedDay))) {
            foreach ($logs as $log) {
                $em->remove($log);
            }
        }
        $em->remove($openedDay[0]);
        $em->flush();
        return array(
            'response' => 'badge rounded-pill',
            'day' => $day,
        );
      }
    } else {
      $newOpenedDay = new Day();
      $newOpenedDay->setDay($day_dt);
      $em->persist($newOpenedDay);
      $em->flush();
      return array(
          'response' => 'badge rounded-pill bg-success',
          'day' => $day,
      );
    }
  }

}
