<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $dimension;

    /**
     * @ORM\Column(type="float")
     */
    private $price1;

    /**
     * @ORM\Column(type="float")
     */
    private $price2;

    /**
     * @ORM\Column(type="float")
     */
    private $price3;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="product", orphanRemoval=true,cascade={"persist"})
     */
    private $carts;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
    }

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

    public function getDimension(): ?int
    {
        return $this->dimension;
    }

    public function setDimension(int $dimension): self
    {
        $this->dimension = $dimension;

        return $this;
    }

    public function getPrice1(): ?float
    {
        return $this->price1;
    }

    public function setPrice1(float $price1): self
    {
        $this->price1 = $price1;

        return $this;
    }

    public function getPrice2(): ?float
    {
        return $this->price2;
    }

    public function setPrice2(float $price2): self
    {
        $this->price2 = $price2;

        return $this;
    }

    public function getPrice3(): ?float
    {
        return $this->price3;
    }

    public function setPrice3(float $price3): self
    {
        $this->price3 = $price3;

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setProduct($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getProduct() === $this) {
                $cart->setProduct(null);
            }
        }

        return $this;
    }
}
