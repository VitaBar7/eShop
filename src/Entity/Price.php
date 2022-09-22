<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\OneToMany(mappedBy: 'price', targetEntity: Reference::class)]
    private Collection $references;

    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

     /**
     * @return Collection<int, Reference>
     */
    public function getReferences(): Collection
    {
        return $this->references;
    }

    public function addReference(Reference $reference): self
    {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->setPrice($this);
        }

        return $this;
    }

    public function removeReference(Reference $reference): self
    {
        if ($this->refs->removeElement($reference)) {
            // set the owning side to null (unless already changed)
            if ($reference->getPrice() === $this) {
                $reference->setPrice(null);
            }
        }

        return $this;
    }
}


