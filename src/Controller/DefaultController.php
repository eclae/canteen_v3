<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends AbstractController {

  /**
   * @Route("/",name="Calendar_Home")
   * @Template()
   */
  public function indexAction() {
    return array('name' => 'temp');
  }

  /**
   * @Route("/flash",name="flash")
   * @Template()
   */
  public function flashAction() {
    return(array());
  }

  /**
   * @Route("/flashDefault",name="flashDefault")
   * @Template()
   */
  public function flashDefaultAction() {
    return(array());
  }

}
