<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use App\Repository\CustomerRepository;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("customer")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = "expr('/api/customer/' ~ object.getId())",
 *      exclusion = @Hateoas\Exclusion(groups="getCustomers")
 * )
 *
 * @Hateoas\Relation(
 *      "update",
 *      href = "expr('/api/customer/' ~ object.getId())",
 *      exclusion = @Hateoas\Exclusion(groups="getCustomers")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = "expr('/api/customer/' ~ object.getId())",
 *      exclusion = @Hateoas\Exclusion(groups="getCustomers")
 * )
 */
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getCustomers"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getCustomers", "addCustomer", "updateCustomer"])]
    #[Assert\NotBlank(
        message: "The customer's email address is required."
    )]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.'
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getCustomers", "addCustomer", "updateCustomer"])]
    #[Assert\NotBlank(
        message: "The customer's first name is required."
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "The customer's first name must be at least {{ limit }} characters long",
        maxMessage: "The customer's first name cannot be longer than {{ limit }} characters"
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getCustomers", "addCustomer", "updateCustomer"])]
    #[Assert\NotBlank(
        message: "The customer's last name is required."
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "The customer's last name must be at least {{ limit }} characters long",
        maxMessage: "The customer's last name cannot be longer than {{ limit }} characters"
    )]
    private ?string $lastName = null;

    #[ORM\Column]
    #[Groups(["getCustomers"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["getCustomers"])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
