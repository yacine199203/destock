<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RealisationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RealisationRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Realisation
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
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="realisations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @ORM\OneToMany(targetEntity=RealisationImages::class, mappedBy="customer", orphanRemoval=true, cascade={"persist"})
     */
    private $realisationImages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $adresse;

    public function __construct()
    {
        $this->realisationImages = new ArrayCollection();
    }

    /** 
    *@ORM\PrePersist
    *@ORM\PreUpdate
    *@return void 
    */
    public function intialSlug(){
        if(empty($this->slug) || !empty($this->slug)){
            $slugify= new Slugify();
            $this->slug = $slugify->slugify($this->customer);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = ucfirst(mb_strtolower($customer, 'UTF-8'));

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection|RealisationImages[]
     */
    public function getRealisationImages(): Collection
    {
        return $this->realisationImages;
    }

    public function addRealisationImage(RealisationImages $realisationImage): self
    {
        if (!$this->realisationImages->contains($realisationImage)) {
            $this->realisationImages[] = $realisationImage;
            $realisationImage->setCustomer($this);
        }

        return $this;
    }

    public function removeRealisationImage(RealisationImages $realisationImage): self
    {
        if ($this->realisationImages->removeElement($realisationImage)) {
            // set the owning side to null (unless already changed)
            if ($realisationImage->getCustomer() === $this) {
                $realisationImage->setCustomer(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
}
