<?php

declare(strict_types=1);

namespace App\Application\Service\Price;

class TotalPriceCalculator
{
    private ExchangeRatesFetcher $exchangeRatesFetcher;

    public function __construct(ExchangeRatesFetcher $exchangeRatesFetcher)
    {
        $this->exchangeRatesFetcher = $exchangeRatesFetcher;
    }

    public function calculate(array $items, array $exchangeRates, string $checkoutCurrency): float
    {
        $totalPrice = 0;

        foreach ($items as $item) {
            $priceInUSD = $item['price'];
            $quantity = $item['quantity'];
            $currency = $item['currency'];

            $priceInCheckoutCurrency = $priceInUSD / $exchangeRates['rates']['USD'] * $exchangeRates['rates'][$currency];
            $totalPrice += $priceInCheckoutCurrency * $quantity;
        }

        $totalPrice = $totalPrice / $exchangeRates['rates'][$checkoutCurrency];
        return $totalPrice;
    }

    public function getExchangeRates(): ?array
    {
        return $this->exchangeRatesFetcher->getExchangeRates();
    }
}
