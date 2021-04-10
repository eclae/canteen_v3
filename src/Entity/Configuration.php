<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration
 * @ORM\Entity
 * @ORM\Table(name="cantine_Configuration")
 */
class Configuration {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="text")
   */
  protected $emailVerificationPattern;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  protected $emailVerificationPatternDefinition;

  /**
   * @ORM\Column(type="integer", nullable=true)
   */
  protected $limitHourParentMayChange;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  protected $limitHourParentMayChangeDefinition;

  /**
   * @ORM\Column(type="float")
   */
  protected $mealSurcharge;

  /**
   * @ORM\Column(type="float")
   */
  protected $subscription;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set emailVerificationPattern
   *
   * @param string $emailVerificationPattern
   * @return Configuration
   */
  public function setEmailVerificationPattern($emailVerificationPattern) {
    $this->emailVerificationPattern = $emailVerificationPattern;

    return $this;
  }

  /**
   * Get emailVerificationPattern
   *
   * @return string
   */
  public function getEmailVerificationPattern() {
    return $this->emailVerificationPattern;
  }

  /**
   * Set emailVerificationPatternDefinition
   *
   * @param string $emailVerificationPatternDefinition
   * @return Configuration
   */
  public function setEmailVerificationPatternDefinition($emailVerificationPatternDefinition) {
    $this->emailVerificationPatternDefinition = $emailVerificationPatternDefinition;

    return $this;
  }

  /**
   * Get emailVerificationPatternDefinition
   *
   * @return string
   */
  public function getEmailVerificationPatternDefinition() {
    return $this->emailVerificationPatternDefinition;
  }


    /**
     * Set limitHourParentMayChange
     *
     * @param integer $limitHourParentMayChange
     * @return Configuration
     */
    public function setLimitHourParentMayChange($limitHourParentMayChange)
    {
        $this->limitHourParentMayChange = $limitHourParentMayChange;

        return $this;
    }

    /**
     * Get limitHourParentMayChange
     *
     * @return integer
     */
    public function getLimitHourParentMayChange()
    {
        return $this->limitHourParentMayChange;
    }

    /**
     * Set limitHourParentMayChangeDefinition
     *
     * @param string $limitHourParentMayChangeDefinition
     * @return Configuration
     */
    public function setLimitHourParentMayChangeDefinition($limitHourParentMayChangeDefinition)
    {
        $this->limitHourParentMayChangeDefinition = $limitHourParentMayChangeDefinition;

        return $this;
    }

    /**
     * Get limitHourParentMayChangeDefinition
     *
     * @return string
     */
    public function getLimitHourParentMayChangeDefinition()
    {
        return $this->limitHourParentMayChangeDefinition;
    }

    /**
     * Set mealSurcharge
     *
     * @param float mealSurcharge
     * @return Configuration
     */
    public function setMealSurcharge($mealSurcharge)
    {
        $this->mealSurcharge = $mealSurcharge;

        return $this;
    }

    /**
     * Get mealSurcharge
     *
     * @return float
     */
    public function getMealSurcharge()
    {
        return $this->mealSurcharge;
    }

     /**
     * Set subscription
     *
     * @param float subscription
     * @return Configuration
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
     * @return float
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

}

