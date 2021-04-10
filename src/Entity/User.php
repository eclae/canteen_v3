<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`cantine_User`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $account;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

  /**
   * @ORM\ManyToMany(targetEntity="Child", inversedBy="users", cascade={"persist"})
   * @ORM\JoinTable(name="cantine_UsersChildren")
   */
  private $children;

  /**
   * @ORM\OneToMany(targetEntity="Contact", mappedBy="user", cascade={"persist"})
   */
  private $contacts;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $rulesAcceptedAt;

  /**
   * @ORM\Column(type="integer")
   */
  private $registrationStep2;

  /**
   * @ORM\Column(type="decimal", precision=5, scale=2)
   */
  private $amount;

  /**
   * @ORM\OneToMany(targetEntity="Invoice", mappedBy="user", cascade={"persist"})
   */
  private $invoices;

  /**
   * @ORM\OneToMany(targetEntity="Payment", mappedBy="payingUser")
   */
  private $payments;

  public function getDisplayName() {
    return $this->getUsername();
  }
  public function __toString() {
    return $this->getUsername();
  }
  /**
   * Get expiresAt
   *
   * @return \DateTime
   */
  public function getExpiresAt() {
    return $this->expiresAt;
  }

  /**
   * Get rulesAcceptedAt
   *
   * @return \DateTime
   */
  public function getRulesAcceptedAt() {
    return $this->rulesAcceptedAt;
  }

  /**
   * Set rulesAcceptedAt
   *
   * @param \DateTime
   */
  public function setRulesAcceptedAt(\DateTime $date = null) {
    $this->rulesAcceptedAt = $date;
    return $this;
  }

  /**
   * Set expiresAt
   *
   * @param \DateTime
   */
  public function setExpiresAt(\DateTime $date = null) {
    $this->expiresAt = $date;
    return $this;
  }

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
    $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
    $this->invoices = new \Doctrine\Common\Collections\ArrayCollection();
    $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    $this->registrationStep2 = 0;
    $this->amount = 0.0;
  }

  /**
   * Add children
   *
   * @param \App\Entity\Child $children
   * @return User
   */
  public function addChild(Child $child) {
    if (!$this->children->contains($child)) {
      $this->children[] = $child;
      if (is_null($child->getPrincipalUser())) {
        $child->setPrincipalUser($this);
      }
    }
    return $this;
  }

  /**
   * Remove children
   *
   * @param \App\Entity\Child $children
   * @return User
   */
  public function removeChild(Child $child) {
    if ($this->children->contains($child)) {
      $this->children->removeElement($child);
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
   * Has child
   *
   * @param \App\Entity\Child $child
   */
  public function hasChild(\App\Entity\Child $child) {
    return $this->children->contains($child);
  }

  /**
   * Add contacts
   *
   * @param \App\Entity\Contact $contacts
   * @return User
   */
  public function addContact(\App\Entity\Contact $contact) {
    if (!$this->contacts->contains($contact)) {
      $this->contacts[] = $contact;
    }

    return $this;
  }

  /**
   * Remove contacts
   *
   * @param \App\Entity\Contact $contacts
   * @return User
   */
  public function removeContact(\App\Entity\Contact $contact) {
    if ($this->contacts->contains($contact)) {
      $this->contacts->removeElement($contact);
    }
    return $this;
  }

  /**
   * Get contact
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getContacts() {
    return $this->contacts;
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
   * Has no child
   *
   * TODO : !!!!!
   */
  public function hasNoChild() {
    if ($this->children->count()) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Get registrationStep2
   *
   * @return integer
   */
  public function getRegistrationStep2() {
    return $this->registrationStep2;
  }

  /**
   * Set registrationStep2
   *
   * @param integer
   */
  public function setRegistrationStep2($bool) {
    if ($bool) {
      $this->registrationStep2 = 1;
    } else {
      $this->registrationStep2 = 0;
    }
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
   * Set amount
   *
   * @param decimal
   */
  public function setAmount($amount) {

    $this->amount = round($amount, 2);

    return $this;
  }

  /**
   * Add invoices
   *
   * @param \App\Entity\Invoice $invoice
   * @return User
   */
  public function addInvoice(\App\Entity\Invoice $invoice) {
    if (!$this->invoices->contains($invoice)) {
      $this->invoices[] = $invoice;
    }

    return $this;
  }

  /**
   * Remove invoice
   *
   * @param \App\Entity\Invoice $invoice
   * @return User
   */
  public function removeInvoice(\App\Entity\Invoice $invoice) {
    if ($this->invoices->contains($invoice)) {
      $this->invoices->removeElement($invoice);
    }
    return $this;
  }

  /**
   * Get $invoices
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getInvoices() {
    return $this->invoices;
  }

  /**
   * Add payments
   *
   * @param \App\Entity\Payment $payment
   * @return User
   */
  public function addPayment(\App\Entity\Payment $payment) {
    if (!$this->payments->contains($payment)) {
      $this->payments[] = $payment;
    }

    return $this;
  }

  /**
   * Remove payment
   *
   * @param \App\Entity\Payment $payment
   * @return User
   */
  public function removePayment(\App\Entity\Payment $payment) {
    if ($this->payments->contains($payment)) {
      $this->payments->removeElement($payment);
    }
    return $this;
  }

  /**
   * Get $payments
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPayments() {
    return $this->payments;
  }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(string $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->account;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
