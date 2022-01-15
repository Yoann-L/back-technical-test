<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrderService;
use App\Service\OrderAnalyticReportService;

class OrderAnalyticReportController extends AbstractController
{
    private $serializer;

    private $orderService;

    private $orderAnalyticReportService;

    public function __construct(SerializerInterface $serializer, OrderService $orderService, OrderAnalyticReportService $orderAnalyticReportService)
    {
        $this->serializer = $serializer;
        $this->orderService = $orderService;
        $this->orderAnalyticReportService = $orderAnalyticReportService;
    }

    /**
     * @Route("/orders/{orderId}/order-analytic-report", name="order-analytic-report", methods={"POST","PATCH","PUT"}, requirements={"orderId"="\d+"})
     */
    public function generateOrderAnalyticReport(int $orderId): Response
    {
        $order = $this->orderService->findOrder($orderId);

        if (is_null($order)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $this->orderAnalyticReportService->manageOrderAnalyticReport($order);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);

        return $response;
    }

    /**
     * @Route("/orders/{orderId}/order-analytic-report", name="get-order-analytic-report", methods={"GET"}, requirements={"orderId"="\d+"})
     */
    public function getOrderAnalyticReport(int $orderId): Response
    {
        $order = $this->orderService->findOrder($orderId);

        if (is_null($order)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $data = $this->serializer->serialize($order->getOrderAnalyticReport(), 'json', [
            'groups' => "order_analytic_report"
        ]);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
