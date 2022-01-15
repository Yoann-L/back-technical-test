<?php

namespace App\Specification\Rule;

use App\Specification\InterfaceClass\SpecificationInterface;

class OrderContactEmailIsValidRule implements SpecificationInterface
{
    /**
     * @param Order $order
     * 
     * @return bool
     */
    public function isSatisfiedBy($order): bool
    {
        return filter_var($order->getContactEmail(), FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return string
     */
    public function getUnsatisfiedMessage(): string 
    {
        return "The email format is not valid";
    }
}