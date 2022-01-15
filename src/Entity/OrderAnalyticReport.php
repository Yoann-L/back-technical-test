<?php

namespace App\Entity;

use App\Repository\OrderAnalyticReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderAnalyticReportRepository::class)
 */
class OrderAnalyticReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="orderAnalyticReport", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"order_analytic_report"})
     */
    private $order;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"order_analytic_report"})
     */
    private $report = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getReport(): ?array
    {
        return $this->report;
    }

    public function setReport(?array $report): self
    {
        $this->report = $report;

        return $this;
    }
}
