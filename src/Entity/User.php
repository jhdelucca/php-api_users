<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private ?int $id = null;

    /**
     * @ORM\Column()
     * @Assert\NotBlank(message="O nome do usuário é obrigatório")
     * @Assert\Length(
     *     min="5",
     *     minMessage="O nome do usuário deve conter pelo menos {{ limit }} caracteres",
     *     max="10",
     *     maxMessage="O nome do usuário deve conter no máximo {{ limit }} caracters"
     * )
     */
    private ?string $name = null;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTime $createdDate = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Telephone", mappedBy="user", cascade={"ALL"})
     * @Assert\Count(min="2")
     * @Assert\Valid()
     */
    private Collection $telephones;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->telephones = new ArrayCollection();
        $this->createdDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedDate(): ?\DateTime
    {
        return $this->createdDate;
    }

    public function addTelephone(string $number): void
    {
        $this->telephones[] = new Telephone($number, $this);
    }

    public function getTelephones(): Collection
    {
        return $this->telephones;
    }
}



 