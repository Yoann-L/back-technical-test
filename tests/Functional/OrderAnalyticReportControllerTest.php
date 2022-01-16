<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\OrderService;
use App\Entity\Order;
use Exception;

class OrderAnalyticReportControllerTest extends WebTestCase
{
    public function testGenerateOrderAnalyticReportOrderNotFound(): void
    {
        $client = static::createClient();
        $container = self::$container;

        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOrder'])
            ->getMock();
        $orderServiceMock->method('findOrder')->willReturn(null);

        $container->set(OrderService::class, $orderServiceMock);

        $client->request('POST', '/orders/1/order-analytic-report');

        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGenerateOrderAnalyticReportSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/orders/1/order-analytic-report');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testGetOrderAnalyticReportOrderNotFound(): void
    {
        $client = static::createClient();
        $container = self::$container;

        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOrder'])
            ->getMock();
        $orderServiceMock->method('findOrder')->willReturn(null);

        $container->set(OrderService::class, $orderServiceMock);

        $client->request('GET', '/orders/1/order-analytic-report');

        $this->assertFalse($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetOrderAnalyticReportSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/orders/1/order-analytic-report');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertResponseStatusCodeSame(200);
    }
}
