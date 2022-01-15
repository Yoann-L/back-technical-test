<?php

namespace App\Tests\Unit\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Country;
use App\Entity\Address;
use App\Entity\OrderLine;
use App\Entity\Tag;
use App\Service\OrderService;
use App\Service\AddressService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TagRepository;
use Exception;

class OrderServiceTest extends KernelTestCase
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

	protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
     
        $this->addressService = $this->createMock(AddressService::class);
    
        $this->orderService = New OrderService($this->entityManager, $this->addressService);
    }

    public function testCalculOrderWeight()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();

        $this->assertSame(41000, $this->orderService->calculOrderWeight($order));
    }

    public function testAddHeavyTag()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();

        $tagHeavy = new Tag();
        $tagHeavy->setName(Tag::AUTO_TAG_HEAVY_NAME);
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn($tagHeavy);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addHeavyTag($order);

        $this->assertCount(1, $order->getTags());
        $this->assertTrue($order->getTags()->contains($tagHeavy));
    }

    public function testAddHeavyTagException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Tag 'heavy' doesn't exist");

        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addHeavyTag($order);
    }

    public function testAddInternationalTag()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();

        $tagOutOfFrance = new Tag();
        $tagOutOfFrance->setName(Tag::AUTO_TAG_INTERNATIONAL_NAME);
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn($tagOutOfFrance);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addInternationalTag($order);

        $this->assertCount(1, $order->getTags());
        $this->assertTrue($order->getTags()->contains($tagOutOfFrance));
    }

    public function testAddInternationalTagException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Tag 'foreignWarehouse' doesn't exist");

        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addInternationalTag($order);
    }

    public function testIssueTagAddress()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        $order->getShippingAddress()->getCountry()->setName("France");
        
        $addressService = $this->addressService;
        $addressService->expects($this->any())
            ->method('getAddressScore')
            ->willReturn(0.4);

        $tagHasIssue = new Tag();
        $tagHasIssue->setName(Tag::AUTO_TAG_ISSUE_NAME);
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn($tagHasIssue);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $addressService);

        $orderService->addIssueTag($order);

        $this->assertCount(1, $order->getTags());
        $this->assertTrue($order->getTags()->contains($tagHasIssue));
    }

    public function testIssueTagEmail()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        $order->setContactEmail("wrong email");

        $tagHasIssue = new Tag();
        $tagHasIssue->setName(Tag::AUTO_TAG_ISSUE_NAME);
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn($tagHasIssue);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addIssueTag($order);

        $this->assertCount(1, $order->getTags());
        $this->assertTrue($order->getTags()->contains($tagHasIssue));
    }

    public function testIssueTagWeight()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        $order->getOrderLines()[0]->getProduct()->setWeight(61000);

        $tagHasIssue = new Tag();
        $tagHasIssue->setName(Tag::AUTO_TAG_ISSUE_NAME);
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn($tagHasIssue);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addIssueTag($order);

        $this->assertCount(1, $order->getTags());
        $this->assertTrue($order->getTags()->contains($tagHasIssue));
    }

    public function testIssueTagException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Tag 'hasIssues' doesn't exist");

        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        
        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository
          ->expects($this->once())
          ->method('findOneBy')
          ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method("getRepository")->willReturn($tagRepository);

        $orderService = New OrderService($entityManager, $this->addressService);

        $orderService->addIssueTag($order);
    }

    public function testHasIssuesGeneralIssues()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        $order->getOrderLines()[0]->getProduct()->setWeight(61000);
        $order->setContactEmail("wrong email");

        $orderService = New OrderService($this->entityManager, $this->addressService);

        $this->assertTrue($orderService->hasIssues($order));
    }

    public function testHasIssuesAddressIssues()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();
        $order->getShippingAddress()->getCountry()->setName("France");

        $addressService = $this->addressService;
        $addressService->expects($this->any())
            ->method('getAddressScore')
            ->willReturn(0.4);

        $orderService = New OrderService($this->entityManager, $addressService);

        $this->assertTrue($orderService->hasIssues($order));
    }

    public function testHasIssuesWithoutIssues()
    {
        $order = $this->createOrderHeavyOutOfFranceWithoutIssue();

        $orderService = New OrderService($this->entityManager, $this->addressService);

        $this->assertFalse($orderService->hasIssues($order));
    }

	private function createOrderHeavyOutOfFranceWithoutIssue(): Order
    {
    	$country = new Country();
    	$country->setName("Germany");

    	$address = new Address();
        $address->setCity("Paris");
        $address->setCountry($country);
        $address->setPostalCode("75002");
        $address->setState("Ile de france");
        $address->setStreetName("Rue de la Paix");
        $address->setStreetNumber("8");

        $product = new Product();
        $product->setWeight(41000);

        $order = new order();
        $order->setName("#45962");
        $order->setShippingAddress($address);
        $order->setContactEmail("test@gmail.com");

        $orderLine = new OrderLine();
        $orderLine->setQuantity(1);
        $orderLine->setProduct($product);
        $orderLine->setOrder($order);

        $order->addOrderLine($orderLine);

        return $order;
    }
}