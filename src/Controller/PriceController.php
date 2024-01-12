<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Service\Price\TotalPriceCalculator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Price")
 **/
class PriceController extends AbstractController
{

    /**
     * @OA\Info(
     *     title="Swagger API documentation for test task",
     *     version="1.0.0",
     *     @OA\Contact(
     *         name="Dmytro",
     *         email="dm.kopylets@gmail.com"
     *         )
     * )
     *
     */
    public function __construct(public TotalPriceCalculator $calculator)
    {
    }


    /**
     * @OA\Post(
     *     path="/process-payload",
     *     summary="main action",
     *     operationId="processPayload",
     *     tags={"Price"},
     *     @OA\RequestBody(
     *         description="Client side request",
     *         required=true,
     *         @OA\MediaType(
    *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="object",
     *                     additionalProperties={
     *                         "type"="object",
     *                         "properties"={
     *                             "currency"={"type"="string"},
     *                             "price"={"type"="number"},
     *                             "quantity"={"type"="integer"}
     *                         }
     *                     }
     *                 ),
     *                 @OA\Property(
     *                     property="checkoutCurrency",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    #[Route('/process-payload', name: 'process_payload', methods: ['POST'])]
    public function processPayload(Request $request, TotalPriceCalculator $calculator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (empty($payload['items']) || empty($payload['checkoutCurrency'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $checkoutCurrency = $payload['checkoutCurrency'];

        $exchangeRates = $this->calculator->getExchangeRates();

        if ($exchangeRates === null) {
            return new JsonResponse(['error' => 'Unable to fetch exchange rates'], 500);
        }

        $totalPrice = $this->calculator->calculate($payload['items'], $exchangeRates, $payload['checkoutCurrency']);

        return $this->json([
            'checkoutPrice' => round($totalPrice, 2),
            'checkoutCurrency' => $checkoutCurrency,
        ]);
    }
}
