<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\OrderService;
use App\Entity\Order;
use Exception;

class OrderControllerTest extends WebTestCase
{
    public function testOrderAutoTagsOrderNotFound(): void
    {
        $client = static::createClient();
        $container = self::$container;

        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOrder'])
            ->getMock();
        $orderServiceMock->method('findOrder')->willReturn(null);

        $container->set(OrderService::class, $orderServiceMock);

        $client->request('POST', '/orders/1/auto-tags');

        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testOrderAutoTagsException(): void
    {
        $client = static::createClient();
        $container = self::$container;

        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addHeavyTag', 'findOrder'])
            ->getMock();
        $orderServiceMock->method('findOrder')->willReturn(new Order());
        $orderServiceMock->method('addHeavyTag')->willThrowException(new Exception());

        $container->set(OrderService::class, $orderServiceMock);

        $client->request('POST', '/orders/1/auto-tags');

        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testOrderAutoTagsSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/orders/1/auto-tags');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(200);
    }
}
