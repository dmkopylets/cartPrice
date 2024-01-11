<?php

declare(strict_types=1);

namespace App\Application\Service\Price;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesFetcher
{
    private string $openExchangeRatesApiKey;

    public function __construct(private HttpClientInterface $httpClient)
    {
        $this->openExchangeRatesApiKey = $_ENV['OPENEXCHANGERATES_API_KEY'];
    }

    public function getExchangeRates(): ?array
    {
        try {
            $oxrUrl = "https://openexchangerates.org/api/latest.json?app_id=" . $this->openExchangeRatesApiKey;
            $response = $this->httpClient->request('GET', $oxrUrl);

            // Ensure a successful response before decoding
            //$response->getStatusCode();
            $oxrLatest = $response->toArray();

            return $oxrLatest;
        } catch (\Exception $e) {
            return null;
        }
    }
}
