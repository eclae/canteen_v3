<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cantine_BlackList")
 */
class BlackList {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $IPAddress;

    public function __toString() {
        return $this->IPAddress;
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
     * @param string $IPAddress
     * @return BlackList
     */
    public function setIPAddress($IPAddress) {
        $this->IPAddress = $IPAddress;

        return $this;
    }

    /**
     * Get IPAddress
     *
     * @return string
     */
    public function getIPAddress() {
        return $this->IPAddress;
    }

}
