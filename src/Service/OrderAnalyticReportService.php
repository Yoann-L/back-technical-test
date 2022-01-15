<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderAnalyticReport;
use App\Specification\Rule\OrderIsDeliveredInFranceRule;
use App\Specification\Rule\OrderWeightIsValidRule;
use App\Specification\Rule\OrderContactEmailIsValidRule;
use App\Specification\Rule\OrderAddressIsValidRule;
use App\Specification\AndSpecification;
use App\Specification\NotSpecification;
use App\Service\OrderService;
use App\Service\AddressService;

class OrderAnalyticReportService
{
	/**
     * @var EntityManagerInterface
     **/
	private $entityManager;

	/**
     * @var OrderService
     **/
	private $orderService;

	/**
     * @var AddressService
     **/
	private $addressService;

	public function __construct(EntityManagerInterface $entityManager, OrderService $orderService, AddressService $addressService)
    {
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->addressService = $addressService;
    }

    /**
     * Update the OrderAnalyticReport of an Order
     * 
     * @param Order $order
     * 
     * @return OrderAnalyticReport
     **/
	public function manageOrderAnalyticReport(Order $order): OrderAnalyticReport 
	{
		$orderAnalyticReport = is_null($order->getOrderAnalyticReport()) ? new OrderAnalyticReport() : $order->getOrderAnalyticReport();

		$report = [];

		$orderWeightIsValidSpec = new OrderWeightIsValidRule($this->orderService);
		if (!$orderWeightIsValidSpec->isSatisfiedBy($order)) {
			$report['issues'][] = $orderWeightIsValidSpec->getUnsatisfiedMessage();
		}

		$orderContactEmailIsValidSpec = new OrderContactEmailIsValidRule();
		if (!$orderContactEmailIsValidSpec->isSatisfiedBy($order)) {
			$report['issues'][] = $orderContactEmailIsValidSpec->getUnsatisfiedMessage();
		}

		$orderAddressIsValidSpec = new OrderAddressIsValidRule($this->addressService);
		$addressSpec = new AndSpecification(
	        new OrderIsDeliveredInFranceRule(),
	       	new NotSpecification($orderAddressIsValidSpec)
	    );
		if ($addressSpec->isSatisfiedBy($order)) {
			$report['issues'][] = $orderAddressIsValidSpec->getUnsatisfiedMessage();
		}

		$orderAnalyticReport->setReport($report);
		$order->setOrderAnalyticReport($orderAnalyticReport);

		$this->entityManager->persist($order);
		$this->entityManager->flush();

		return $orderAnalyticReport;
	}
}