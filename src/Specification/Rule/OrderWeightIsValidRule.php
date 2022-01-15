<?php

namespace App\Specification\Rule;

use App\Specification\InterfaceClass\SpecificationInterface;
use App\Service\OrderService;
use App\Entity\Order;

class OrderWeightIsValidRule implements SpecificationInterface
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
        return ($this->orderService->calculOrderWeight($order) > Order::HEAVY_ISSUE) ? false : true;
    }

    /**
     * @return string
     */
    public function getUnsatisfiedMessage(): string 
    {
        return "The order weight is up to " . (Order::HEAVY_ISSUE / 1000) . "kg";
    }
}