<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 * @ORM\Entity
 * @ORM\Table(name="cantine_Invoice")
 * @ORM\Entity(repositoryClass="InvoiceRepository")
 */
class Invoice {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="Child",inversedBy="invoices", cascade={"persist"})
   */
  protected $child;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="invoices", cascade={"persist"})
   */
  protected $user;

  /**
   * @ORM\ManyToMany(targetEntity="Day", cascade={"persist"})
   * @ORM\JoinTable(name="cantine_InvoicesDays")
   */
  protected $days;

  /**
   * @ORM\Column(type="date")
   */
  protected $date;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  protected $creationDate;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  protected $sentDate;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  protected $paidDate;

  /**
   * @ORM\Column(type="decimal", precision=5, scale=2)
   */
  protected $amount;

  /**
   * @ORM\ManyToOne(targetEntity="InvoiceType")
   */
  protected $type;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $comments;

  public function __toString() {
    return $this->getId();
  }

  public function getDisplayName() {
    return $this->__toString();
  }

  /**
   * Constructor
   */
  public function __construct() {
    $this->creationDate = new \DateTime();
    $this->days = new \Doctrine\Common\Collections\ArrayCollection();
    $this->amount = 0.0;
    //$this->type = new \App\Entity\InvoiceType();
    $this->comments = "";
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
   * Set child
   *
   * @param \App\Entity\Child $child
   * @return Invoice
   */
  public function setChild(\App\Entity\Child $child) {
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
   * Set user
   *
   * @param \App\Entity\User $user
   * @return Invoice
   */
  public function setUser(\App\Entity\User $user) {
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

  /**
   * Set creation date
   *
   * @param \DateTime $date
   * @return Invoice
   */
  public function setCreationDate(\DateTime $date) {
    $this->creationDate = $date;

    return $this;
  }

  /**
   * Get  date
   *
   * @return \DateTime
   */
  public function getCreationDate() {
    return $this->creationDate;
  }

  /**
   * Set creation date
   *
   * @param \DateTime $date
   * @return Invoice
   */
  public function setDate(\DateTime $date) {
    $this->date = $date;

    return $this;
  }

  /**
   * Get  date
   *
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * Set sent date
   *
   * @param \DateTime $date
   * @return Invoice
   */
  public function setSentDate(\DateTime $date) {
    $this->sentDate = $date;

    return $this;
  }

  /**
   * Get sent date
   *
   * @param \DateTime $date
   * @return \DateTime
   */
  public function getSentDate() {
    return $this->sentDate;
  }

  /**
   * Set paid date
   *
   * @param \DateTime $date
   * @return Invoice
   */
  public function setPaidDate(\DateTime $date) {
    $this->paidDate = $date;

    return $this;
  }

  /**
   * Get paid date
   *
   * @param \DateTime $date
   * @return \DateTime
   */
  public function getPaidDate() {
    return $this->paidDate;
  }

  /**
   * Set amount
   *
   * @param  $amount
   * @return Invoice
   */
  public function setAmount($amount) {
    if (is_numeric($amount))
      $this->amount = $amount;

    return $this;
  }

  /**
   * Get amount
   *
   * @return decimal
   */
  public function getAmount() {
    return $this->amount;
  }

  /**
   * Set comments
   *
   * @param  $comments
   * @return Invoice
   */
  public function setComments($comments) {
    if (is_string($comments))
      $this->comments = $comments;

    return $this;
  }

  /**
   * Get comments
   *
   * @return string
   */
  public function getComments() {
    return $this->comments;
  }

  /**
   * Add days
   *
   * @param \App\Entity\Day $day
   * @return Invoice
   */
  public function addDay(\App\Entity\Day $day) {
    if (!$this->days->contains($day)) {
      $this->days[] = $day;
    }

    return $this;
  }

  /**
   * Has days ?
   *
   * @param \App\Entity\Day $day
   * @return boolean
   */
  public function hasDay(\App\Entity\Day $day) {

    return $this->days->contains($day);
  }

  /**
   * Remove days
   *
   * @param \App\Entity\Day $day
   */
  public function removeDay(\App\Entity\Day $day) {
    if ($this->days->contains($day)) {
      $this->days->removeElement($day);
    }
    return $this;
  }

  /**
   * Get days
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDays() {
    return $this->days;
  }

  /**
   * Set type
   *
   * @param \App\Entity\InvoiceType $type
   * @return Invoice
   */
  public function setType(\App\Entity\InvoiceType $type) {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return \App\Entity\Invoice
   * yTpe
   */
  public function getType() {
    return $this->type;
  }

}
