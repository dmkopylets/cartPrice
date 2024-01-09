<?php

declare(strict_types=1);

namespace App\Application\Service\Price;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TotalPriceCalculator
{
    private string $openExchangeRatesApiKey;

    public function __construct()
    {
        $this->openExchangeRatesApiKey = $_ENV['OPENEXCHANGERATES_API_KEY'];
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
        try {
            $oxrUrl = "https://openexchangerates.org/api/latest.json?app_id=" . $this->openExchangeRatesApiKey;

            // Open CURL session:
            $ch = curl_init($oxrUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // Get the data:
            $response = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $oxrLatest = json_decode($response, true);

            return $oxrLatest;
        } catch (\Exception $e) {
            return null;
        }
    }
}
