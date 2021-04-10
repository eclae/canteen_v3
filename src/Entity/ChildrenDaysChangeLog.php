<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cantine_ChildrenDaysChangeLog")
 */
class ChildrenDaysChangeLog {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Child", cascade={"persist", "remove"})
   */
  private $child;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   */
  private $user;

  /**
   * @ORM\ManyToOne(targetEntity="Day")
   */
  private $day;

  /**
   * @ORM\Column(type="boolean")
   */
  private $missing;

  /**
   * @ORM\Column(type="boolean")
   */
  private $invoiced;

  /**
   * @ORM\Column(type="boolean")
   */
  private $creation;

  /**
   * @ORM\Column(type="boolean")
   */
  private $deletion;

  /**
   * @ORM\Column(type="boolean")
   */
  private $surcharged;

  /**
   * @ORM\Column(type="date")
   */
  private $date;

  public function __construct() {
    $this->missing = false;
    $this->invoiced = false;
    $this->creation = false;
    $this->deletion = false;
    $this->surcharged = false;
    $this->date = new \DateTime();
    $this->child = new Child();
    //$this->day = null;
    $this->user = new User();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set missing
   *
   * @param boolean $missing
   * @return ChildrenDays
   */
  public function setMissing($missing = true) {
    if ($missing)
      $this->missing = true;
    else
      $this->missing = false;

    return $this;
  }

  /**
   * Get missing
   *
   * @return boolean
   */
  public function getMissing() {
    return $this->missing;
  }

  /**
   * Set invoiced
   *
   * @param boolean $invoiced
   * @return ChildrenDays
   */
  public function setInvoiced($invoiced) {
    if ($invoiced)
      $this->invoiced = true;
    else
      $this->invoiced = false;

    return $this;
  }

  /**
   * Get missing
   *
   * @return boolean
   */
  public function getInvoiced() {
    return $this->invoiced;
  }

  /**
   * Set child
   *
   * @param \App\Entity\Child $child
   * @return ChildrenDays
   */
  public function setChild(Child $child = null) {
    $this->child = $child;

    return $this;
  }

  /**
   * Get child
   *
   * @return \App\Entity\Child
   */
  public function getChild() {
    return $this->child;
  }

  /**
   * Set day
   *
   * @param \App\Entity\Day $day
   * @return ChildrenDays
   */
  public function setDay(Day $day = null) {
    $this->day = $day;
//$child->addDay($day);

    return $this;
  }

  /**
   * Get day
   *
   * @return \App\Entity\Day
   */
  public function getDay() {
    return $this->day;
  }

  /**
   * Set create
   *
   * @param boolean $create
   * @return ChildrenDaysChangeLog
   */
  public function setCreation($create) {
    $this->creation = $create;

    return $this;
  }

  /**
   * Get create
   *
   * @return boolean
   */
  public function getCreation() {
    return $this->creation;
  }

  /**
   * Set delete
   *
   * @param boolean $delete
   * @return ChildrenDaysChangeLog
   */
  public function setDeletion($delete) {
    $this->deletion = $delete;

    return $this;
  }

  /**
   * Get delete
   *
   * @return boolean
   */
  public function getDeletion() {
    return $this->deletion;
  }

  /**
   * Set surcharge
   *
   * @param boolean $surcharge
   * @return ChildrenDaysChangeLog
   */
  public function setSurcharge($surcharge) {
    $this->surcharged = $surcharge;

    return $this;
  }

  /**
   * Get surcharge
   *
   * @return boolean
   */
  public function getSurcharge() {
    return $this->surcharged;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   * @return ChildrenDaysChangeLog
   */
  public function setDate($date) {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * Set user
   *
   * @param \App\Entity\User $user
   * @return ChildrenDaysChangeLog
   */
  public function setUser(\App\Entity\User $user = null) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return \App\Entity\User
   */
  public function getUser() {
    return $this->user;
  }

}
