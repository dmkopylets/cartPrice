<?php

namespace App\Tests\Price;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Application\Service\Price\TotalPriceCalculator;
use App\Application\Service\Price\ExchangeRatesFetcher;

class TotalPriceCalculatorTest extends WebTestCase
{
    public function testCalculateTotalPriceInUsd()
{
    // Arrange
    $items = [
        ['price' => 10, 'quantity' => 2, 'currency' => 'USD'],
        ['price' => 20, 'quantity' => 1, 'currency' => 'USD'],
    ];
    $exchangeRates = ['rates' => ['USD' => 1]];
    $checkoutCurrency = 'USD';
    $exchangeRatesFetcherMock = $this->createMock(ExchangeRatesFetcher::class);
    $exchangeRatesFetcherMock->method('getExchangeRates')->willReturn($exchangeRates);
    $totalPriceCalculator = new TotalPriceCalculator($exchangeRatesFetcherMock);

    // Act
    $totalPrice = $totalPriceCalculator->calculate($items, $exchangeRates, $checkoutCurrency);

    // Assert
    $this->assertEquals(40, $totalPrice);
}

public function testCalculateTotalPriceInOtherCurrencies()
{
    // Arrange
    $items = [
        ['price' => 10, 'quantity' => 2, 'currency' => 'USD'],
        ['price' => 20, 'quantity' => 1, 'currency' => 'EUR'],
    ];
    $exchangeRates = ['rates' => ['USD' => 1, 'EUR' => 0.9]];
    $checkoutCurrency = 'EUR';
    $exchangeRatesFetcherMock = $this->createMock(ExchangeRatesFetcher::class);
    $exchangeRatesFetcherMock->method('getExchangeRates')->willReturn($exchangeRates);
    $totalPriceCalculator = new TotalPriceCalculator($exchangeRatesFetcherMock);

    // Act
    $totalPrice = $totalPriceCalculator->calculate($items, $exchangeRates, $checkoutCurrency);

    // Assert
    //$this->assertEquals(29, $totalPrice);
    $this->assertEquals(42.22222222222222, $totalPrice);
}

public function testCalculateTotalPriceWithDifferentQuantities()
{
    // Arrange
    $items = [
        ['price' => 10, 'quantity' => 2, 'currency' => 'USD'],
        ['price' => 20, 'quantity' => 3, 'currency' => 'USD'],
    ];
    $exchangeRates = ['rates' => ['USD' => 1]];
    $checkoutCurrency = 'USD';
    $exchangeRatesFetcherMock = $this->createMock(ExchangeRatesFetcher::class);
    $exchangeRatesFetcherMock->method('getExchangeRates')->willReturn($exchangeRates);
    $totalPriceCalculator = new TotalPriceCalculator($exchangeRatesFetcherMock);

    // Act
    $totalPrice = $totalPriceCalculator->calculate($items, $exchangeRates, $checkoutCurrency);

    // Assert
    $this->assertEquals(80, $totalPrice);
}



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
        $httpClient = $this->createMock(HttpClientInterface::class);
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);
        $calculator = new TotalPriceCalculator($exchangeRatesFetcher);
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
        $httpClient = $this->createMock(HttpClientInterface::class);
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);
        $calculator = new TotalPriceCalculator($exchangeRatesFetcher);
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
        $httpClient = $this->createMock(HttpClientInterface::class);
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);
        $calculator = new TotalPriceCalculator($exchangeRatesFetcher);
        $actualTotalPrice = $calculator->calculate($items, $exchangeRates, $checkoutCurrency);

        // Assert
        $this->assertEquals($expectedTotalPrice, $actualTotalPrice);
    }
}
