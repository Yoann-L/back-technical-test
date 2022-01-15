<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\Tag;
use App\Specification\Rule\OrderIsHeavyRule;
use App\Specification\Rule\OrderIsDeliveredInFranceRule;
use App\Specification\Rule\OrderWeightIsValidRule;
use App\Specification\Rule\OrderContactEmailIsValidRule;
use App\Specification\Rule\OrderAddressIsValidRule;
use App\Specification\AndSpecification;
use App\Specification\NotSpecification;
use App\Specification\OrSpecification;
use App\Service\AddressService;
use Exception;

class OrderService
{
	/**
	 * @var EntityManagerInterface
	 **/
	private $entityManager;

	/**
	 * @var AddressService
	 **/
	private $addressService;

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param AddressService $addressService
	 **/
	public function __construct(EntityManagerInterface $entityManager, AddressService $addressService)
    {
        $this->entityManager = $entityManager;
        $this->addressService = $addressService;
    }

    /**
     * Find order by id
     * 
     * @param int $orderId
     * 
     * @return Order|null
     **/
	public function findOrder(int $orderId): ?Order
	{
		$orderRepository = $this->entityManager->getRepository(Order::class);

        return $orderRepository->find($orderId);
	}

	/**
	 * Add or remove the 'heavy' tag 
	 * 
	 * @param Order $order
	 * 
	 * @return Order 
	 **/
	public function addHeavyTag(Order $order): Order
	{
		// Allow to check if an order is heavy (>40kg)
		$orderIsHeavySpec = new OrderIsHeavyRule($this);

		$tagRepository = $this->entityManager->getRepository(Tag::class);
		$tagHeavy = $tagRepository->findOneBy(['name' => Tag::AUTO_TAG_HEAVY_NAME]);

		if (is_null($tagHeavy)) {
			throw new Exception("Tag 'heavy' doesn't exist");
		}

		// Order weight > 40 kg ?
		if ($orderIsHeavySpec->isSatisfiedBy($order)) {
			$order->addTag($tagHeavy);
		} else {
			$order->removeTag($tagHeavy);
		}

		$this->entityManager->persist($order);
		$this->entityManager->flush();

		return $order;
	}

	/**
	 * Add or remove the 'foreignWarehouse' tag 
	 * 
	 * @param Order $order
	 * 
	 * @return Order 
	 **/
	public function addInternationalTag(Order $order): Order
	{
		// Allow to check if an order is delivered out of France
		$orderIsInFranceSpec = new OrderIsDeliveredInFranceRule();

		$tagRepository = $this->entityManager->getRepository(Tag::class);
		$tagOutOfFrance = $tagRepository->findOneBy(['name' => Tag::AUTO_TAG_INTERNATIONAL_NAME]);

		if (is_null($tagOutOfFrance)) {
			throw new Exception("Tag 'foreignWarehouse' doesn't exist");
		}

		// Order is delivered out of France ?
		if (!$orderIsInFranceSpec->isSatisfiedBy($order)) {
			$order->addTag($tagOutOfFrance);
		} else {
			$order->removeTag($tagOutOfFrance);
		}

		$this->entityManager->persist($order);
		$this->entityManager->flush();

		return $order;
	}

	/**
	 * Add or remove the 'hasIssues' tag 
	 * 
	 * @param Order $order
	 * 
	 * @return Order 
	 **/
	public function addIssueTag(Order $order): Order 
	{
		$tagRepository = $this->entityManager->getRepository(Tag::class);
		$tagHasIssue = $tagRepository->findOneBy(['name' => Tag::AUTO_TAG_ISSUE_NAME]);

		if (is_null($tagHasIssue)) {
			throw new Exception("Tag 'hasIssues' doesn't exist");
		}

		// If at least one issues conditions is true: add issue tag to order
		if ($this->hasIssues($order)) {
			$order->addTag($tagHasIssue);
		} else {
			$order->removeTag($tagHasIssue);
		}

		$this->entityManager->persist($order);
		$this->entityManager->flush();

		return $order;
	}

	/**
	 * Calcul order weight (gram)
	 * 
	 * @param Order $order
	 * 
	 * @return int 
	 **/
	public function calculOrderWeight(Order $order): int 
	{
		$weight = 0;

		foreach ($order->getOrderLines() as $orderLine) {
			$weight += $orderLine->getQuantity() * $orderLine->getProduct()->getWeight();
		}

		return $weight;
	}

	/**
	 * Check order issues :
	 *  - contact_email is valid ?
	 *  - weight > 60 kg ?
	 *  - address score < 0.6 ?
	 * 
	 * @param Order $order
	 * 
	 * @return bool
	 **/
	public function hasIssues(Order $order): bool 
	{
		$orderGeneralIssuesSpec = new OrSpecification(
			new NotSpecification(new OrderContactEmailIsValidRule()), 
			new NotSpecification(new OrderWeightIsValidRule($this))
		);

		$orderAddressIssuesSpec = new AndSpecification(
			new OrderIsDeliveredInFranceRule(), 
			new NotSpecification(new OrderAddressIsValidRule($this->addressService))
		);

		if ($orderGeneralIssuesSpec->isSatisfiedBy($order)) {
			return true;
		}

		if ($orderAddressIssuesSpec->isSatisfiedBy($order)){
			return true;
		}

		return false;
	}
}