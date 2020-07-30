<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
final class Telephone
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private User $user;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="O número de telefone é obrigatório")
     */
    private string $number;

    public function __construct(string $number, User $user)
    {
        $this->number = $number;
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }
}
