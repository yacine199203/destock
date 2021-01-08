<?php

namespace App\Entity;

use App\Entity\JobProduct;
use App\Repository\JobProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobProductRepository::class)
 */
class JobProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="jobProducts")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="jobProducts")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $job;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
}
