<?php

namespace App\Specification;

use App\Specification\InterfaceClass\SpecificationInterface;

class AndSpecification implements SpecificationInterface
{
	/**
	 * @var Specification[]
	 */
	private array $specifications;

	/**
	 * @param Specification[] $specifications
	 */
	public function __construct(SpecificationInterface ...$specifications)
	{
		$this->specifications = $specifications;
	}

	public function isSatisfiedBy($candidate): bool
	{
		$satisfied = [];
		foreach ($this->specifications as $specification) {
			$satisfied[] = $specification->isSatisfiedBy($candidate);
		}

		return !in_array(false, $satisfied);
	}
}