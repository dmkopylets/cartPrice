<?php

namespace App\Tests\Price;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Application\Service\Price\TotalPriceCalculator;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\PriceController;

class TotalPriceCalculatorTest extends WebTestCase
{
    public function testCalculateTotalPriceWithValidExchangeRatesAndCheckoutCurrency()
    {
        // Arrange
        $items = [
            [
                'price' => 10,
                'quantity' => 2,
                'currency' => 'USD'
            ],
            [
                'price' => 20,
                'quantity' => 1,
                'currency' => 'EUR'
            ],
            [
                'price' => 15,
                'quantity' => 3,
                'currency' => 'GBP'
            ]
        ];
        $exchangeRates = [
            'rates' => [
                'USD' => 1,
                'EUR' => 0.85,
                'GBP' => 0.75
            ]
        ];
        $checkoutCurrency = 'EUR';
        $expectedTotalPrice = (10 * 2 + 20 * 0.85 + 15 * 0.75 * 3) / 0.85;

        // Act
        $calculator = new TotalPriceCalculator();
        $actualTotalPrice = $calculator->calculate($items, $exchangeRates, $checkoutCurrency);

        // Assert
        $this->assertEquals($expectedTotalPrice, $actualTotalPrice);
    }


    public function testCalculateTotalPriceWithOneItemAndValidExchangeRatesAndCheckoutCurrency()
    {
        // Arrange
        $items = [
            [
                'price' => 10,
                'quantity' => 1,
                'currency' => 'USD'
            ]
        ];
        $exchangeRates = [
            'rates' => [
                'USD' => 1,
                'EUR' => 0.85
            ]
        ];
        $checkoutCurrency = 'EUR';
        $expectedTotalPrice = 10 * 1 / 0.85;

        // Act
        $calculator = new TotalPriceCalculator();
        $actualTotalPrice = $calculator->calculate($items, $exchangeRates, $checkoutCurrency);

        // Assert
        $this->assertEquals($expectedTotalPrice, $actualTotalPrice);
    }

    public function testCalculateTotalPriceWithEmptyItemsArrayAndValidExchangeRatesAndCheckoutCurrency()
    {
        // Arrange
        $items = [];
        $exchangeRates = [
            'rates' => [
                'USD' => 1,
                'EUR' => 0.85
            ]
        ];
        $checkoutCurrency = 'EUR';
        $expectedTotalPrice = 0;

        // Act
        $calculator = new TotalPriceCalculator();
        $actualTotalPrice = $calculator->calculate($items, $exchangeRates, $checkoutCurrency);

        // Assert
        $this->assertEquals($expectedTotalPrice, $actualTotalPrice);
    }
}
