<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cantine_Meal")
 */
class Meal
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="float")
     */
    protected $dayCost;
   
    public function __toString() {
        return $this->name;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Meal
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dayCost
     *
     * @param float $dayCost
     * @return Meal
     */
    public function setDayCost($dayCost)
    {
        $this->dayCost = $dayCost;
    
        return $this;
    }

    /**
     * Get dayCost
     *
     * @return float 
     */
    public function getDayCost()
    {
        return $this->dayCost;
    }
}
