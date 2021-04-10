<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceType
 * @ORM\Entity
 * @ORM\Table(name="cantine_InvoiceType")
 */
class InvoiceType {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=64)
   */
  protected $name;

  /**
   * @ORM\Column(type="string", length=128)
   */
  protected $displayText;

  public function __toString() {
    return $this->getName();
  }

  public function getDisplayName() {
    return $this->__toString();
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
   * Set name
   *
   * @param string $name
   * @return InvoiceType
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set displayText
   *
   * @param string $displayText
   * @return InvoiceType
   */
  public function setDisplayText($displayText) {
    $this->displayText = $displayText;

    return $this;
  }

  /**
   * Get displayText
   *
   * @return string
   */
  public function getDisplayText() {
    return $this->displayText;
  }

}
