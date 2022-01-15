<?php

namespace App\Specification\InterfaceClass;

interface SpecificationInterface
{
    public function isSatisfiedBy($candidate): bool;
}