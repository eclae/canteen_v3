<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="cantine_Contact")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="ContactRepository")
 */
class Contact {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="firstName", type="string", length=255)
   */
  private $firstName;

  /**
   * @var string
   *
   * @ORM\Column(name="lastName", type="string", length=255)
   */
  private $lastName;

  /**
   * @var string
   *
   * @ORM\Column(name="parent", type="string", length=255)
   */
  private $parent;

  /**
   * @var string
   *
   * @ORM\Column(name="email", type="string", length=255, nullable=true)
   */
  private $email;

  /**
   * @var string
   *
   * @ORM\Column(name="homePhoneNumber", type="string", length=20, nullable=true)
   */
  private $homePhoneNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="mobilePhoneNumber", type="string", length=20)
   */
  private $mobilePhoneNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="workPhoneNumber", type="string", length=20, nullable=true)
   */
  private $workPhoneNumber;

  /**
   * @ORM\ManyToMany(targetEntity="Child", mappedBy="contacts")
   */
  protected $children;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="contacts")
   */
  protected $user;

  /**
   * Constructor
   */
  public function __construct() {
    $this->children = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Set dysplayName
   *
   * @return string
   */
  public function getDisplayName() {

    return $this->getfirstName() . ' ' . $this->getLastName();
  }

  /**
   * Set dusplayName
   *
   * @return string
   */
  public function __toString() {

    return $this->getDisplayName();
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
   * Set email aderess
   * @param string $email
   * @return Contact
   */
  public function setEmail($email) {
    $this->email = $email;

    return $this;
  }

  /**
   * Get email address
   *
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * Set firstName
   *
   * @param string $firstName
   * @return Contact
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
   * @return Contact
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
   * Set parent
   *
   * @param string $parent
   * @return Contact
   */
  public function setParent($parent) {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Get parent
   *
   * @return string
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * Set homePhoneNumber
   *
   * @param string $homePhoneNumber
   * @return Contact
   */
  public function setHomePhoneNumber($homePhoneNumber) {
    $this->homePhoneNumber = $homePhoneNumber;

    return $this;
  }

  /**
   * Get homePhoneNumber
   *
   * @return string
   */
  public function getHomePhoneNumber() {
    return $this->homePhoneNumber;
  }

  /**
   * Set mobilePhoneNumber
   *
   * @param string $mobilePhoneNumber
   * @return Contact
   */
  public function setMobilePhoneNumber($mobilePhoneNumber) {
    $this->mobilePhoneNumber = $mobilePhoneNumber;

    return $this;
  }

  /**
   * Get mobilePhoneNumber
   *
   * @return string
   */
  public function getMobilePhoneNumber() {
    return $this->mobilePhoneNumber;
  }

  /**
   * Set workPhoneNumber
   *
   * @param string $workPhoneNumber
   * @return Contact
   */
  public function setWorkPhoneNumber($workPhoneNumber) {
    $this->workPhoneNumber = $workPhoneNumber;

    return $this;
  }

  /**
   * Get workPhoneNumber
   *
   * @return string
   */
  public function getWorkPhoneNumber() {
    return $this->workPhoneNumber;
  }

  /**
   * Add child
   *
   * @param \App\Entity\Child $child
   * @return Contact
   */
  public function addChild(\App\Entity\Child $child = null) {
    if (!$this->hasChild($child)) {
      $this->children[] = $child;
    }
    return $this;
  }

  /**
   * Get children
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getChildren() {
    return $this->children;
  }

  /**
   * Has child ?
   *
   * @param \App\Entity\Child $child
   * @return boolean
   */
  public function hasChild(Child $child) {

    return $this->children->contains($child);
  }

  /**
   * Add user
   *
   * @param \App\Entity\User $user
   * @return Contact
   */
  public function setUser(\App\Entity\User $user = null) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get children
   *
   * @return \App\Entity\User

   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Remove child
   *
   * @param \App\Entity\Child $child
   */
  public function removeChild(\App\Entity\Child $child) {
    $this->children->removeElement($child);
  }

}
