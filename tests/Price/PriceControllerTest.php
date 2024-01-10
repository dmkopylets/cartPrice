<?php

namespace App\Tests\Price;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Application\Service\Price\TotalPriceCalculator;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\PriceController;

class PriceControllerTest extends WebTestCase
{
    public function testMissingPayloadFields()
    {
        // Arrange
        $payload = [
            'items' => [],
            'checkoutCurrency' => ''
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $calculatorMock = $this->createMock(TotalPriceCalculator::class);
        $controller = new PriceController($calculatorMock);

        // Act
        $response = $controller->processPayload($request, $calculatorMock);

        // Assert
        $expectedResponse = [
            'error' => 'Invalid payload'
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUnableToFetchEchangeRates()
    {
        // Arrange
        $payload = [
            'items' => [
                [
                    'price' => 10,
                    'quantity' => 2,
                    'currency' => 'USD'
                ]
            ],
            'checkoutCurrency' => 'EUR'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $calculatorMock = $this->createMock(TotalPriceCalculator::class);
        $calculatorMock->expects($this->once())
            ->method('getExchangeRates')
            ->willReturn(null);
        $controller = new PriceController($calculatorMock);

        // Act
        $response = $controller->processPayload($request, $calculatorMock);

        // Assert
        $expectedResponse = [
            'error' => 'Unable to fetch exchange rates'
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }
}
