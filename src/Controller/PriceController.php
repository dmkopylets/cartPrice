<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    #[Route('/process-payload', name: 'process_payload', methods: ['POST'])]
    public function processPayload(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        if (empty($payload['items']) || empty($payload['checkoutCurrency'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $checkoutCurrency = $payload['checkoutCurrency'];

        return $this->json([
            'items' => $payload['items'],
            'checkoutCurrency' => $checkoutCurrency,
        ]);
    }
}
