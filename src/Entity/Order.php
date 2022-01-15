<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    const HEAVY = 40000;
    const HEAVY_ISSUE = 60000;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"auto_tags", "order_analytic_report"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auto_tags", "order_analytic_report"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $contactEmail;

    /**
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="order")
     */
    private $orderLines;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="orders")
     * @Groups({"auto_tags"})
     */
    private $tags;

    /**
     * @ORM\OneToOne(targetEntity=OrderAnalyticReport::class, mappedBy="order", cascade={"persist", "remove"})
     */
    private $orderAnalyticReport;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $shippingAddress;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return Collection|OrderLines[]
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addOrder($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeOrder($this);
        }

        return $this;
    }

    public function getOrderAnalyticReport(): ?OrderAnalyticReport
    {
        return $this->orderAnalyticReport;
    }

    public function setOrderAnalyticReport(OrderAnalyticReport $orderAnalyticReport): self
    {
        // set the owning side of the relation if necessary
        if ($orderAnalyticReport->getOrder() !== $this) {
            $orderAnalyticReport->setOrder($this);
        }

        $this->orderAnalyticReport = $orderAnalyticReport;

        return $this;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }
}