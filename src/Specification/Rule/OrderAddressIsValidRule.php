<?php

namespace App\Specification\Rule;

use App\Specification\InterfaceClass\SpecificationInterface;
use App\Service\AddressService;

class OrderAddressIsValidRule implements SpecificationInterface
{
    /**
     * @var AddressService
     */
    private $addressService;

    /**
     * @param AddressService $addressService
     */
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * @param Order $order
     * 
     * @return bool
     */
    public function isSatisfiedBy($order): bool
    {
        return ($this->addressService->getAddressScore($order->getShippingAddress()) >= 0.6) ? true : false;
    }

    /**
     * @return string
     */
    public function getUnsatisfiedMessage(): string 
    {
        return "The order address is not valid";
    }
}