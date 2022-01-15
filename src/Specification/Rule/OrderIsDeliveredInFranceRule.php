<?php

namespace App\Specification\Rule;

use App\Specification\InterfaceClass\SpecificationInterface;

class OrderIsDeliveredInFranceRule implements SpecificationInterface
{
    /**
     * @param Order $order
     * 
     * @return bool
     */
    public function isSatisfiedBy($order): bool
    {
        return ($order->getShippingAddress()->getCountry()->getName() === "France");
    }

    /**
     * @return string
     */
    public function getUnsatisfiedMessage(): string 
    {
        return "The order is out of France";
    }
}