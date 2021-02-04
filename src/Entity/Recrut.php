<?php

namespace App\Entity;

use App\Repository\RecrutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecrutRepository::class)
 */
class Recrut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $poste;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=ProfilRecrut::class, mappedBy="poste",orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid
     */
    private $profilRecruts;

    public function __construct()
    {
        $this->profilRecruts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|ProfilRecrut[]
     */
    public function getProfilRecruts(): Collection
    {
        return $this->profilRecruts;
    }

    public function addProfilRecrut(ProfilRecrut $profilRecrut): self
    {
        if (!$this->profilRecruts->contains($profilRecrut)) {
            $this->profilRecruts[] = $profilRecrut;
            $profilRecrut->setPoste($this);
        }

        return $this;
    }

    public function removeProfilRecrut(ProfilRecrut $profilRecrut): self
    {
        if ($this->profilRecruts->removeElement($profilRecrut)) {
            // set the owning side to null (unless already changed)
            if ($profilRecrut->getPoste() === $this) {
                $profilRecrut->setPoste(null);
            }
        }

        return $this;
    }
}
