<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Child controller.
 *
 */
class ChildrenDaysController extends AbstractController {

  private function childRegisteredADay(App\Entity\Child $child, \DateTime $date) {

    $registered = false;
    $missing = false;
    $ChildrenDays = new App\Entity\ChildrenDays();

    foreach ($child->getDays() as $childrenDays) {
      if ($childrenDays->getDay()->getDay() == $date) {
        $registered = true;
        $ChildrenDays = $childrenDays;
        if (!$childrenDays->getMissing()) {
          $missing = true;
        }
      }
    }
    return array(
        'registered' => $registered,
        'missing' => $missing,
        'childrenDays' => $ChildrenDays,
    );
  }

  private function sendMail($to, $cci, $subject, $from, $body, $content_type) {
    $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBcc($cci)
            ->setBody($body)
            ->setContentType($content_type)
    ;
    return $this->get('mailer')->send($message);
  }

  /**
   * Provide the day for a child.
   *
   * @Route("/childrendays/send_days_2_parent", name="send_days_2_parent")
   * @Method({"GET"})
   * @Template()
   * @IsGranted("ROLE_ADMIN")
   */
  public function sendChildrenDays2ParentAction() {

    // TODO : better error management
    // TODO : transformer cette Action en mode Console pour l'insérer dans une crontab
    //if (idate('d') == 16) {
    $month = idate('m');
    $year = idate('Y');
    $firstDayOfNextMonth = new \DateTime("$year-$month-01");
    $firstDayOfNextMonth->add(new \DateInterval("P1M"));
    $month = $firstDayOfNextMonth->format('m');
    $year = $firstDayOfNextMonth->format('Y');

    $em = $this->getDoctrine()->getManager();

    $users = array();
    $Users = $em->getRepository('User')->findAll();
    foreach ($Users as $user) {
      $users[$user->getId()]["email"] = $user->getEmail();
      $users[$user->getId()]["name"] = $user;
      foreach ($user->getChildren() as $child) {
        $users[$user->getId()]
                ["children"]
                [$child->getId()]
                ['name'] = $child;
      }
    }

    $childrenDays = $em->getRepository('ChildrenDays')->findChildrenDaysByMonthByYear($month, $year);
    foreach ($childrenDays as $childDay) {
      foreach ($childDay->getChild()->getUsers() as $user) {

        $users[$user->getId()]
                ["children"]
                [$childDay->getChild()->getId()]
                ["days"]
                [$childDay->getDay()->getDay()->format('d')] = 1;
      }
    }

    foreach ($users as $user) {
      $body = "";
      $body .= '<h3>Bonjour,</h3><p>Vous trouverez ci-dessous les dates d\'inscriptions &agrave; la cantine prises en compte pour le mois prochain (' . $month . '/' . $year . ') pour votre (vos) enfant(s).</p><p>Si cela ne correspond pas &agrave; vos besoins, merci de faire les modifications directement sur le site http://www.restaurant-scolaire-fleurieu-sur-saone.org/ d&egrave;s r&eacute;ception de ce message.</p>';
      $body .= "<ul>";
      if (array_key_exists('children', $user)) {
        foreach ($user["children"] as $child) {

          if (array_key_exists('days', $child)) {
            $body .= "<li>" . $child['name'] . ' mangera le(s) : ';
            ksort($child["days"], SORT_NUMERIC);
            foreach ($child["days"] as $day => $value) {
              $body .= "$day ";
            }
            $body .= '</li>';
          } else {
            $body .= "<li>" . $child['name'] . ' ne mangera pas le mois prochain</li>';
          }
        }
        $body .= "</ul>";
        $body .= '<p>Cordialement,</p>';
        $body .= '<p>l\'&eacute;quipe de la cantine de Fleurieu</p>';
        $body = '<html><head></head><body>' . $body . '</body></html>';
        $this->sendMail(
                //$user["email"], null, "test mail", "gestion@restaurant-scolaire-fleurieu-sur-saone.org", $body, "text/html"
                $user['email'], "marc.michot@gmail.com", "Cantine de Fleurieu : Jours du mois prochain : $month/$year", "gestion@restaurant-scolaire-fleurieu-sur-saone.org", $body, "text/html"
        );
      }
    }

    //  return array();
    //  }
    return array();
  }

  /**
   * Provide the day for a child.
   *
   * @Route("/childrendays/add_child_day/{id}/{day}/{month}/{year}/{tabId}", requirements={"id" = "\d+", "day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]", "month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}","tabId" = "\d+"}, name="add_child_day")
   * @Method({"UPDATE","GET"})
   * @Template("ChildrenDays.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function addChildDayAction(Request $request, $id, $day, $month, $year, $tabId, TranslatorInterface $translator) {

    $em = $this->getDoctrine()->getManager();
    $child = $em->getRepository('Child')->find($id);
    $day_dt = new \DateTime("$year/$month/$day");
    $today_dt = new \DateTime();
    $childRegisteredADay = $this->childRegisteredADay($child, $today_dt);

// We add this child to the day :
// - we are sure the user is not registered to this day
// - the user is an ADMIN
// TODO: protect the day when the invoice is created
    if ($today_dt == $day_dt) {
// Maybe the day is not visible because Missing is true
      if ($childRegisteredADay["regitered"]) {
        if ($childRegisteredADay["missing"]) {
          $childRegisteredADay["childrenDays"]->setMissing(false);
          $em->flush();
        } else {
// error: at this point, the child can't be registered at this day
          $_message = $translator->trans('cantine.childrendays.flash.registered.child.already');
          $this->get('session')->getFlashBag()->set('error', $_message);
          throw $this->createNotFoundException();
        }
      }
    } else {
// TODO: forbidden if an invoice is created
// until it is allowed
//just to valid the child is not registered this day
      if (!$childRegisteredADay["registered"]) {
        $childrenDays = new App\Entity\ChildrenDays();
        $childrenDays->setChild($child);
        if ($dayArray_temp = $em->getRepository('Day')->findByDay($day_dt)) {
          $childrenDays->setDay($dayArray_temp[0]);
          $em->persist($childrenDays);
          $em->flush();
        } else {
          $_message = $translator->trans('cantine.childrendays.flash.registered.day.notopen', array('%date%' => "$day/$month/$year"));
          $this->get('session')->getFlashBag()->set('error', $_message);
          throw $this->createNotFoundException();
        }
      } else {
// error: at this point, the child can't be registered at this day
        $_message = $translator->trans('cantine.childrendays.flash.registered.child.already');
        $this->get('session')->getFlashBag()->set('error', $_message);
        throw $this->createNotFoundException();
      }
    }
    return $this->redirect($this->generateUrl('index_presence', array('day' => $day, 'month' => $month, 'year' => $year, 'tabId' => $tabId)));
  }

  /**
   * Refresh day where a child was removed from Présence list.
   *
   * @Route("/childrendays/refresh_day/{day}/{month}/{year}", requirements = {"day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]","month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="refresh_day_meal_order")
   * @Method("GET")
   * @Template("ChildrenDays/viewMacroSpanMealCounter.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function refreshDayAction($day, $month, $year) {

    $em = $this->getDoctrine()->getManager();
    $date = new \DateTime("$year/$month/$day");

    $normalMealNumber = count($em->getRepository('ChildrenDays')->findMealListOfChildrenByDayByMeal($date, 1));
    $withoutMeatMealNumber = count($em->getRepository('ChildrenDays')->findMealListOfChildrenByDayByMeal($date, 2));
    $withoutPorkMealNumber = count($em->getRepository('ChildrenDays')->findMealListOfChildrenByDayByMeal($date, 3));
    $adultMealNumber = count($em->getRepository('ChildrenDays')->findMealListOfChildrenByDayByMeal($date, 5));

    return array(
        'normalMealNumber' => $normalMealNumber,
        'withoutMeatMealNumber' => $withoutMeatMealNumber,
        'withoutPorkMealNumber' => $withoutPorkMealNumber,
        'adultMealNumber' => $adultMealNumber
    );
  }

  /**
   * Remove Child from Présence list.
   *
   * @Route("/childrendays/remove_child/{childrenDaysId}/{day}/{month}/{year}/{tabId}", defaults={"tabId"="1"}, requirements = {"childrenDaysId"="\d+","day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]","month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}", "tabId" = "\d+"}, name="remove_child_presence")
   * @Method("GET")
   * @Template()
   * @IsGranted("ROLE_MANAGE")
   */
  public function removeChildAction($childrenDaysId, $day, $month, $year, $tabId, TranslatorInterface $translator) {

    $em = $this->getDoctrine()->getManager();
    $childrenDays = $em->getRepository('ChildrenDays')->find($childrenDaysId);
    $date = new \DateTime("$year/$month/$day");
    $date->setTime(8, 0, 0);
    $now = new \DateTime();
// For test
//$now = new \DateTime("2013/09/19");
    $now->setTime(8, 0, 0);

    if ($date < $now) {
// Forbidden to remove a child for a day before now.
// TODO: allow this for Admin only if the day is not invoiced
      $_message = $translator->trans('cantine.childrendays.flash.child.remove.before.now');
      $this->get('session')->getFlashBag()->set('error', $_message);
      throw $this->createNotFoundException();
    } elseif ($date == $now) {
      $childrenDays->setMissing(true);
      $em->flush();
    } elseif ($date > $now) {
      $em->remove($childrenDays);
      $em->flush();
    }
    return $this->redirect($this->generateUrl('index_presence', array('day' => $day, 'month' => $month, 'year' => $year, 'tabId' => $tabId)));
  }

  /**
   * Index of presence.
   *
   * @Route("/childrendays/index_presence/{day}/{month}/{year}/{tabId}", defaults={"tabId"="1"}, requirements = {"day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]","month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}", "tabId" = "\d"}, name="index_presence")
   * @Method("GET")
   * @Template("ChildrenDays/presence.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function indexPresenceAction($day, $month, $year, $tabId, TranslatorInterface $translator) {

    $em = $this->getDoctrine()->getManager();
    $grades = $em->getRepository('App:Grade')->findAll();
    $childNumber = 0;
    $childByGrade = array();
    $date = new \DateTime("$year/$month/$day");

// Beginning of listing the children who are not listed in a day
    $children_day = new \Doctrine\Common\Collections\ArrayCollection();
    $children_temp = new \Doctrine\Common\Collections\ArrayCollection();
    $children = $em->getRepository('App:Child')->findAll();
//var_dump($children);
//die;

    for ($i = 1; $i <= count($grades); $i++) {
      $childByGrade[$i] = $em->getRepository('App:ChildrenDays')->findMealListOfChildrenByDayByGrade($date, $i);
      foreach ($childByGrade[$i] as $childrendays) {
        $children_day[] = $childrendays->getChild();
      }
      $childNumber += count($childByGrade[$i]);
    }

    foreach ($children as $child) {
      if (!$children_day->contains($child))
        $children_temp[] = $child;
    }


    $badgeByMeal[1] = 'text-info';
    $badgeByMeal[2] = 'text-error';
    $badgeByMeal[3] = 'text-success';
    $badgeByMeal[4] = 'text-warning';
    $badgeByMeal[5] = 'muted';

    $_message = $translator->trans('cantine.childrendays.flash.presence.date', ['%date%' => $date->format('d/m/Y')]);
    $_message .= " - $childNumber enfants";
    $this->get('session')->getFlashBag()->set('notice', $_message);

    return array(
        'title' => 'cantine.title.childrendays.index',
        'tabId' => $tabId,
        'grades' => $grades,
        'childByGrade' => $childByGrade,
        'date' => $date,
        'badgeByMeal' => $badgeByMeal,
        'childrenNotRegisteredThatDay' => $children_temp,
    );
  }

  /**
   * Index of presence.
   *
   * @Route("/childrendays/index_presence_printable/{day}/{month}/{year}", requirements = {"day" = "0?[0-9]|1[0-9]|2[0-9]|3[0-1]","month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="index_presence_printable")
   * @Method("GET")
   * @Template("ChildrenDays/presencePrintable.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function indexPresencePrintableAction($day, $month, $year) {

    return $this->indexPresenceAction($day, $month, $year, 1);
  }

  /**
   * Index of meal order.
   *
   * @Route("/childrendays/index_meal_order", name="index_meal_order")
   * @Method("GET")
   * @Template("ChildrenDays/mealOrder.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function indexMealOrderAction() {
    return array();
  }

  /**
   * Lists all Meal per Day.
   *
   * @Route("/childrendays/show_meal_order/{month}/{year}",requirements = {"month" = "0?[0-9]|1[0-2]", "year" = "20\d{2}"}, name="show_meal_order")
   * @Method("GET")
   * @Template("ChildrenDays/showMealOrder.html.twig")
   * @IsGranted("ROLE_MANAGE")
   */
  public function showMealOrderAction($month, $year) {

    setlocale(LC_TIME,"fr_FR.UTF-8");
    $em = $this->getDoctrine()->getManager();
//$user = $this->getUser();
    $normalMealNumber = array();
    $adultMealNumber = array();
    $withoutMeatMealNumber = array();
    $withoutPorkMealNumber = array();
    $classes = array();
    $firstDayOfMonth_dt = new \DateTime("$year-$month-01");
    $lastDayOfMonth_dt = new \DateTime($firstDayOfMonth_dt->format('Y-m-t'));
    for ($i = $firstDayOfMonth_dt->format('j'); $i <= $lastDayOfMonth_dt->format('j'); $i++) {
      $classes[$i] = '';
    }

    $allOpenedDays = $em->getRepository('App:Day')->findAll();
    foreach ($allOpenedDays as $openedDay) {

      if ($openedDay->getDay()->format('n') == $month and $openedDay->getDay()->format('Y') == $year) {
        $classes[$openedDay->getDay()->format('j')] = 'vide';
        $normalMealNumber[$openedDay->getDay()->format('j')] = count($em->getRepository('App:ChildrenDays')->findMealListOfChildrenByDayByMeal($openedDay->getDay(), 1));
        $adultMealNumber[$openedDay->getDay()->format('j')] = count($em->getRepository('App:ChildrenDays')->findMealListOfChildrenByDayByMeal($openedDay->getDay(), 5));
        $withoutMeatMealNumber[$openedDay->getDay()->format('j')] = count($em->getRepository('App:ChildrenDays')->findMealListOfChildrenByDayByMeal($openedDay->getDay(), 2));
        $withoutPorkMealNumber[$openedDay->getDay()->format('j')] = count($em->getRepository('App:ChildrenDays')->findMealListOfChildrenByDayByMeal($openedDay->getDay(), 3));
      }
    }

    $previousMonthDate_dt = new \DateTime("$year-$month-01");
    $nextMonthDate_dt = new \DateTime("$year-$month-01");
    $nextMonthDate_dt->add(new \DateInterval('P1M'));
    $previousMonthDate_dt->sub(new \DateInterval('P1M'));

    return array(
        'classes' => $classes,
        'normalMealNumber' => $normalMealNumber,
        'adultMealNumber' => $adultMealNumber,
        'withoutMeatMealNumber' => $withoutMeatMealNumber,
        'withoutPorkMealNumber' => $withoutPorkMealNumber,
        'firstDayOfTheMonth' => $firstDayOfMonth_dt->format('N') - 1,
        'lastDayOfTheMonth' => $lastDayOfMonth_dt->format('j'),
        'nextMonthYear' => $nextMonthDate_dt,
        'previousMonthYear' => $previousMonthDate_dt,
        'actualMonthYear' => strftime('%B %Y', $firstDayOfMonth_dt->getTimestamp()),
        'actualMonthYear2' => $firstDayOfMonth_dt,
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
  }

}
