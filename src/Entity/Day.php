<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cantine_Day")
 * @ORM\Entity(repositoryClass="DayRepository")
 */
class Day {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="date")
   */
  protected $day;
  
  /**
   * @ORM\Column(type="boolean")
   */
  private $invitation;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set day
   *
   * @param \DateTime $day
   * @return Calendar
   */
  public function setDay($day) {
    $this->day = $day;

    return $this;
  }
  
 
  /**
   * Get day
   *
   * @return \DateTime
   */
  public function getDay() {
    return $this->day;
  }

  /**
   * __toString
   * @return string
   */
  public function __toString() {
    return $this->getDay()->format('d/m/Y');
  }
  
   /**
   * Get invitation
   *
   * @return boolean
   */
  public function getInvitation() {
    return $this->invitation;
  }

  /**
   * Set invitation
   *
   * @param \Boolean $invitation
   * @return Day
   */
  public function setInvitation($invitation) {
    if ($invitation) {
        $this->invitation = true;
    } else {
        $this->invitation = false;
    }

    return $this;
  }
  
  
   public function __construct() {
    $this->invitation = false;
  }
}

