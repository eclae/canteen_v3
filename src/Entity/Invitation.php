<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 * @ORM\Entity
 * @ORM\Table(name="cantine_Invitation")
 * @ORM\Entity(repositoryClass="InvitationRepository")
 */
class Invitation {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;
  
  /**
   * @ORM\ManyToOne(targetEntity="Day")
   * @ORM\JoinColumn(nullable=false)
   */
  protected $day;

  /**
   * @ORM\ManyToMany(targetEntity="Grade", cascade={"persist"})
   * @ORM\JoinTable(name="cantine_InvitationGrade")
   */
  protected $grades;

  /**
   * Constructor
   */
  public function __construct() {
    $this->grades = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set day
   *
   * @param \App\Entity\Day $day
   * @return Invitation
   */
  
  public function setDay(\App\Entity\Day $day) {
    $this->day = $day;

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
   * Add grade
   *
   * @param \App\Entity\Grade $grade
   * @return Invitation
   */
  public function addGrade(\App\Entity\Grade $grade) {
    if (!$this->grades->contains($grade)) {
      $this->grades[] = $grade;
    }

    return $this;
  }

  /**
   * Has grade ?
   *
   * @param \App\Entity\Grade $grade
   * @return boolean
   */
  public function hasGrade(\App\Entity\Grade $grade) {

    return $this->grades->contains($grade);
  }

  /**
   * Remove grade
   *
   * @param \App\Entity\Grade $grade
   */
  public function removeGrade(\App\Entity\GRade $grade) {
    if ($this->grades->contains($grade)) {
      $this->grades->removeElement($grade);
    }
    return $this;
  }

  /**
   * Get Grades
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getGrades() {
    return $this->grades;
  }

  /**
   * Set Grade
   *
   * @param \App\Entity\Grade $grade
   * @return Invitation
   */
  
  public function setGrade(\App\Entity\Grade $grade) {
    $this->grade = $grade;

    return $this;
  }

  
  
}
