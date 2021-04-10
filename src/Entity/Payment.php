<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 * @ORM\Entity
 * @ORM\Table(name="cantine_Payment")
 */
class Payment {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="payments", cascade={"persist"})
   */
  protected $payingUser;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   */
  protected $creatingUser;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  protected $creationDate;

  /**
   * @ORM\Column(type="decimal", precision=5, scale=2)
   *
   */
  protected $amount;

  /**
   * @ORM\ManyToOne(targetEntity="PaymentType")
   */
  protected $type;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $lastNameOnCheck;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $bankName;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $checkId;

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
    $this->amount = 0;
    //$this->type = new \App\Entity\PaymentType();
    //$this->payingUser = new \App\Entity\User();
    //$this->creatingUser = new \App\Entity\User();
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
   * Set paying user
   *
   * @param \App\Entity\User $user
   * @return Invoice
   */
  public function setPayingUser(User $user) {
    $this->payingUser = $user;

    return $this;
  }

  /**
   * Get paying user
   *
   * @return \App\Entity\User
   */
  public function getPayingUser() {
    return $this->payingUser;
  }

  /**
   * Set creating user
   *
   * @param \App\Entity\User $user
   * @return Invoice
   */
  public function setCreatingUser(User $user) {
    $this->creatingUser = $user;

    return $this;
  }

  /**
   * Get creating user
   *
   * @return \App\Entity\User
   */
  public function getCreatingUser() {
    return $this->creatingUser;
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
   * Set type
   *
   * @param \App\Entity\PaymentType $type
   * @return Invoice
   */
  public function setType(PaymentType $type) {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return \App\Entity\PaymentType
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Set invoice
   *
   * @param \App\Entity\Invoice $invoice
   * @return Payment
   */
  public function setInvoice(\App\Entity\Invoice $invoice = null) {
    $this->invoice = $invoice;

    return $this;
  }

  /**
   * Get invoice
   *
   * @return \App\Entity\Invoice
   */
  public function getInvoice() {
    return $this->invoice;
  }

  /**
   * Set checkId
   *
   * @param checkId
   * @return Payment
   */
  public function setCheckId($checkId) {
    $this->checkId = $checkId;

    return $this;
  }

  /**
   * Get checkId
   *
   * @return checkId
   */
  public function getCheckId() {
    return $this->checkId;
  }

  /**
   * Set bankName
   *
   * @param bankName
   * @return Payment
   */
  public function setBankName($bankName) {
    $this->bankName = $bankName;

    return $this;
  }

  /**
   * Get bankName
   *
   * @return bankName
   */
  public function getBankName() {
    return $this->bankName;
  }

  /**
   * Set lastNameOnCheck
   *
   * @param lastNameOnCheck
   * @return Payment
   */
  public function setLastNameOnCheck($lastNameOnCheck) {
    $this->lastNameOnCheck = $lastNameOnCheck;

    return $this;
  }

  /**
   * Get lastNameOnCheck
   *
   * @return lastNameOnCheck
   */
  public function getLastNameOnCheck() {
    return $this->lastNameOnCheck;
  }

}
