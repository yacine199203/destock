<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\JobRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"jobName"},
 * message="Ce métier existe déja"
 * )
 */
class Job
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
    private $jobName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=JobProduct::class, mappedBy="job", orphanRemoval=true)
     */
    private $jobProducts;

    public function __construct()
    {
        $this->jobProducts = new ArrayCollection();
    }

    /** 
    *@ORM\PrePersist
    *@ORM\PreUpdate
    *@return void 
    */
    public function intialSlug(){
        if(empty($this->slug) || !empty($this->slug)){
            $slugify= new Slugify();
            $this->slug = $slugify->slugify($this->jobName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobName(): ?string
    {
        return $this->jobName;
    }

    public function setJobName(string $jobName): self
    {
        $this->jobName = ucfirst(mb_strtolower($jobName, 'UTF-8'));

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|JobProduct[]
     */
    public function getJobProducts(): Collection
    {
        return $this->jobProducts;
    }

    public function addJobProduct(JobProduct $jobProduct): self
    {
        if (!$this->jobProducts->contains($jobProduct)) {
            $this->jobProducts[] = $jobProduct;
            $jobProduct->setJob($this);
        }

        return $this;
    }

    public function removeJobProduct(JobProduct $jobProduct): self
    {
        if ($this->jobProducts->removeElement($jobProduct)) {
            // set the owning side to null (unless already changed)
            if ($jobProduct->getJob() === $this) {
                $jobProduct->setJob(null);
            }
        }

        return $this;
    }
}
