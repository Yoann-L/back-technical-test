<?php

namespace App\Specification\Rule;

use App\Specification\InterfaceClass\SpecificationInterface;
use App\Service\OrderService;
use App\Entity\Order;

class OrderIsHeavyRule implements SpecificationInterface
{
    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param Order $order
     * 
     * @return bool
     */
    public function isSatisfiedBy($order): bool
    {
        return ($this->orderService->calculOrderWeight($order) > Order::HEAVY) ? true : false;
    }

    /**
     * @return string
     */
    public function getUnsatisfiedMessage(): string 
    {
        return "The order is less than " . (Order::HEAVY / 1000) . "kg";
    }
}