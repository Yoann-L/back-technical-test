<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrderService;
use App\Service\OrderAnalyticReportService;
use Exception;

class OrderController extends AbstractController
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
     * @Route("/orders/{orderId}/auto-tags", name="order-auto-tags", methods={"POST","PATCH","PUT"}, requirements={"orderId"="\d+"})
     */
    public function orderAutoTags(int $orderId): Response
    {
        $order = $this->orderService->findOrder($orderId);

        if (is_null($order)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        try {
            $this->orderService->addHeavyTag($order);
            $this->orderService->addInternationalTag($order);
            $this->orderService->addIssueTag($order);
        } catch (Exception $e) {
            $response = new Response($e->getMessage());
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $data = $this->serializer->serialize($order, 'json', [
            'groups' => "auto_tags"
        ]);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
