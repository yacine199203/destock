<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Product
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
    private $productName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $png;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pdf;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=ProductImages::class, mappedBy="product", orphanRemoval=true, cascade={"persist"})
     */
    private $productImages;

    /**
     * @ORM\OneToMany(targetEntity=Characteristics::class, mappedBy="product",orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $characteristics;

    /**
     * @ORM\OneToMany(targetEntity=JobProduct::class, mappedBy="product", orphanRemoval=true,cascade={"persist"})
     * @Assert\Valid
     */
    private $jobProducts;

    /**
     * @ORM\OneToMany(targetEntity=Price::class, mappedBy="product", orphanRemoval=true)
     */
    private $prices;

    

    public function __construct()
    {
        $this->productImages = new ArrayCollection();
        $this->characteristics = new ArrayCollection();
        $this->jobProducts = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    /** 
    *@ORM\PrePersist
    *@ORM\PreUpdate
    *@return void 
    */
    public function intialSlug(){
        if(empty($this->slug) || !empty($this->slug)){
            $slugify= new Slugify();
            $this->slug = $slugify->slugify($this->productName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = ucfirst(mb_strtolower($productName, 'UTF-8'));

        return $this;
    }

    public function getPng(): ?string
    {
        return $this->png;
    }

    public function setPng(string $png): self
    {
        $this->png = $png;

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    /**
     * @return Collection|ProductImages[]
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImages $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages[] = $productImage;
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImages $productImage): self
    {
        if ($this->productImages->removeElement($productImage)) {
            // set the owning side to null (unless already changed)
            if ($productImage->getProduct() === $this) {
                $productImage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Characteristics[]
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    public function addCharacteristic(Characteristics $characteristic): self
    {
        if (!$this->characteristics->contains($characteristic)) {
            $this->characteristics[] = $characteristic;
            $characteristic->setProduct($this);
        }

        return $this;
    }

    public function removeCharacteristic(Characteristics $characteristic): self
    {
        if ($this->characteristics->removeElement($characteristic)) {
            // set the owning side to null (unless already changed)
            if ($characteristic->getProduct() === $this) {
                $characteristic->setProduct(null);
            }
        }

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
            $jobProduct->setProduct($this);
        }

        return $this;
    }

    public function removeJobProduct(JobProduct $jobProduct): self
    {
        if ($this->jobProducts->removeElement($jobProduct)) {
            // set the owning side to null (unless already changed)
            if ($jobProduct->getProduct() === $this) {
                $jobProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Price[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setProduct($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getProduct() === $this) {
                $price->setProduct(null);
            }
        }

        return $this;
    }
}
