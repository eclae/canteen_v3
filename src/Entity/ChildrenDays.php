<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cantine_ChildrenDays",uniqueConstraints={@ORM\UniqueConstraint(name="idxUnique_ChildDay", columns={"child_id", "day_id"})})
 * @ORM\Entity(repositoryClass="ChildrenDaysRepository")
 */
class ChildrenDays {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Child", inversedBy="days")
   */
  private $child;

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
  private $surcharge;
  


  /**
   * @ORM\Column(type="boolean")
   */
  private $invoiced;

  public function __construct(Child $child = null, Day $day = null) {
    $this->missing = false;
    $this->invoiced = false;
    $this->surcharge = false;
    $this->child = $child;
    $this->day = $day;
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
   * Set surcharge
   *
   * @param boolean $surcharge
   * @return ChildrenDays
   */
  public function setSurcharge($surcharge) {
    if ($surcharge)
      $this->surcharge = true;
    else
      $this->surcharge = false;

    return $this;
  }

  /**
   * Get surcharge
   *
   * @return boolean
   */
  public function getSurcharge() {
    return $this->surcharge;
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
   * @param \SI\CantineBundle\Entity\Child $child
   * @return ChildrenDays
   */
  public function setChild(Child $child = null) {
    $this->child = $child;

    return $this;
  }

  /**
   * Get child
   *
   * @return \SI\CantineBundle\Entity\Child
   */
  public function getChild() {
    return $this->child;
  }

  /**
   * Set day
   *
   * @param \SI\CantineBundle\Entity\Day $day
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
   * @return \SI\CantineBundle\Entity\Day
   */
  public function getDay() {
    return $this->day;
  }


}
