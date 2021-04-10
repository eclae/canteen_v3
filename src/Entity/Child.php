<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Child
 * @ORM\Entity
 * @ORM\Table(name="cantine_Child")
 */
class Child {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=64)
   */
  protected $firstName;

  /**
   * @ORM\Column(type="string", length=64)
   */
  protected $lastName;

  /**
   * @ORM\ManyToMany(targetEntity="User", mappedBy="children")
   */
  protected $users;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   */
  protected $principalUser;

  /**
   * @ORM\ManyToOne(targetEntity="Grade")
   */
  protected $grade;

  /**
   * @ORM\ManyToOne(targetEntity="Meal")
   */
  protected $meal;

  /**
   * @ORM\Column(type="text")
   */
  protected $comment;

  /**
   * @ORM\OneToMany(targetEntity="ChildrenDays", mappedBy="child", cascade={"persist","remove"})
   */
  protected $days;

  /**
   * @ORM\OneToMany(targetEntity="Invoice", mappedBy="child")
   */
  protected $invoices;

  /**
   * @ORM\ManyToMany(targetEntity="Contact", inversedBy="children", cascade={"persist","remove"})
   * @ORM\JoinTable(name="cantine_ChildrenContacts")
   */
  protected $contacts;

  public function __toString() {
    return $this->getFirstName() . ' ' . $this->getLastName();
  }

  public function getDisplayName() {
    return $this->getFirstName() . ' ' . $this->getLastName();
  }

  /**
   * Constructor
   */
  public function __construct() {
    $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    $this->days = new \Doctrine\Common\Collections\ArrayCollection();
    $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
    $this->meal = 1;
    $this->comment = "sans commentaire";
    $this->principalUser = null;
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
   * Set firstName
   *
   * @param string $firstName
   * @return Child
   */
  public function setFirstName($firstName) {
    $this->firstName = $firstName;

    return $this;
  }

  /**
   * Get firstName
   *
   * @return string
   */
  public function getFirstName() {
    return $this->firstName;
  }

  /**
   * Set lastName
   *
   * @param string $lastName
   * @return Child
   */
  public function setLastName($lastName) {
    $this->lastName = $lastName;

    return $this;
  }

  /**
   * Get lastName
   *
   * @return string
   */
  public function getLastName() {
    return $this->lastName;
  }

  /**
   * Add user
   *
   * @param \App\Entity\User $user
   * @return Child
   */
  public function addUser($user) {
    if (!is_null($user)) {
      if ($this->users->contains($user))
        return;

      $this->users[] = $user;
      $user->addChild($this);
      if (is_null($this->getPrincipalUser()))
        $this->setPrincipalUser($user);
    }
    return $this;
  }

  /**
   * Remove users
   *
   * @param \App\Entity\User $user
   */
  public function removeUser(\App\Entity\User $user) {
    if ($this->users->contains($user)) {
      $this->users->removeElement($user);
      $user->removeChild($this);
    }
    if ($user == $this->getPrincipalUser()) {
      if (count($_users = $this->getUsers()) > 0) {
        $this->setPrincipalUser($_users->first());
      } else {
        $this->setPrincipalUser(null);
      }
    }
  }

  /**
   * Get users
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUsers() {
    return $this->users;
  }

  /**
   * Get principalUser
   *
   * @return \App\Entity\User $user
   */
  public function getPrincipalUser() {
    return $this->principalUser;
  }

  /**
   * Set principalUser
   *
   * @param \App\Entity\User $user
   */
  public function setPrincipalUser($user) {

    $this->addUser($user);
    $this->principalUser = $user;

    return $this;
  }

  /**
   * Set grade
   *
   * @param \App\Entity\Grade $grade
   * @return Child
   */
  public function setGrade(Grade $grade = null) {
    $this->grade = $grade;

    return $this;
  }

  /**
   * Get grade
   *
   * @return \App\Entity\Grade
   */
  public function getGrade() {
    return $this->grade;
  }

  /**
   * Get comment
   *
   * @return text
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Set comment
   *
   * @param text
   * @return Child
   */
  public function setComment($comment = null) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Set meal
   *
   * @param \App\Entity\Meal $meal
   * @return Child
   */
  public function setMeal(Meal $meal = null) {
    $this->meal = $meal;

    return $this;
  }

  /**
   * Get meal
   *
   * @return \App\Entity\Meal
   */
  public function getMeal() {
    return $this->meal;
  }

  /**
   * Add days
   *
   * @param \App\Entity\ChildrenDays $day
   * @return Child
   */
  public function addDay(ChildrenDays $day) {
    if (!$this->days->contains($day)) {
      $this->days[] = $day;
    }

    return $this;
  }

  /**
   * Has days ?
   *
   * @param \App\Entity\ChildrenDays $day
   * @return boolean
   */
  public function hasDay(ChildrenDays $day) {

    return $this->days->contains($day);
  }

  /**
   * Remove days
   *
   * @param \App\Entity\ChildrenDays $day
   */
  public function removeDay(ChildrenDays $day) {
    $this->days->removeElement($day);
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
   * Remove contact
   *
   * @param \App\Entity\Contact $contact
   */
  public function removeContact(\App\Entity\Contact $contact) {
    $this->contacts->removeElement($contact);
    return $this;
  }

  /**
   * Get contacts
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getContacts() {
    return $this->contacts;
  }

  /**
   * Add contact
   *
   * @param \App\Entity\Contact $contact
   * @return Child
   */
  public function addContact(\App\Entity\Contact $contact) {
    $this->contacts[] = $contact;

    return $this;
  }

  /**
   * Has contact
   *
   * @param \App\Entity\Contact $contact
   */
  public function hasContact(\App\Entity\Contact $contact) {
    return $this->contacts->contains($contact);
  }

  /**
   * Add invoice
   *
   * @param \App\Entity\Invoice $invoice
   * @return child
   */
  public function addInvoice(Invoice $invoice) {
    if (!$this->invoices->contains($invoice)) {
      $this->invoices[] = $invoice;
    }
    return $this;
  }

  /**
   * Get invoices
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getInvoices() {
    return $this->invoices;
  }

  /**
   * Remove invoices
   *
   * @param \App\Entity\Invoice $invoice
   */
  public function removeInvoice(\App\Entity\Invoice $invoice) {
    if ($this->invoices->contains($invoice)) {
      $this->invoices->removeElement($invoice);
    }
    return $this;
  }

}
