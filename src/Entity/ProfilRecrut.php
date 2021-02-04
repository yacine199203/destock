<?php

namespace App\Entity;

use App\Repository\ProfilRecrutRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfilRecrutRepository::class)
 */
class ProfilRecrut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Recrut::class, inversedBy="profilRecruts")
     */
    private $poste;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $conditions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoste(): ?Recrut
    {
        return $this->poste;
    }

    public function setPoste(?Recrut $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }
}
