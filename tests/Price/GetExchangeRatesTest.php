<?php

namespace App\Tests\Price;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Application\Service\Price\ExchangeRatesFetcher;

class GetExchangeRatesTest extends WebTestCase
{
    public function testFetchExchangeRatesSuccessfully()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Set up the expected response from the API
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn(['USD' => 1.0, 'EUR' => 0.85]);

        // Set up the HttpClient mock to return the expected response
        $httpClient->method('request')->willReturn($response);

        // Create an instance of ExchangeRatesFetcher with the mock HttpClientInterface
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);

        // Call the getExchangeRates method
        $exchangeRates = $exchangeRatesFetcher->getExchangeRates();

        // Assert that the exchange rates are fetched successfully
        $this->assertEquals(['USD' => 1.0, 'EUR' => 0.85], $exchangeRates);
    }

    public function testFetchExchangeRatesApiRequestFails()
    {
        // Create a mock HttpClientInterface
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Set up the HttpClient mock to throw an exception when requesting the API
        $httpClient->method('request')->willThrowException(new \Exception());

        // Create an instance of ExchangeRatesFetcher with the mock HttpClientInterface
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);

        // Call the getExchangeRates method
        $exchangeRates = $exchangeRatesFetcher->getExchangeRates();

        // Assert that the exchange rates are null when API request fails
        $this->assertNull($exchangeRates);
    }

    public function testFetchExchangeRatesHandlesLargeData()
    {
        // Create a mock HttpClientInterface
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Set up the expected response from the API with a large amount of data
        $responseData = [];
        $responseData = ['USD' => 1.0];
        for ($i = 0; $i < 10000; $i++) {
            $responseData['USD' . $i] = 1.0;
        }

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn($responseData);

        // Set up the HttpClient mock to return the expected response
        $httpClient->method('request')->willReturn($response);

        // Create an instance of ExchangeRatesFetcher with the mock HttpClientInterface
        $exchangeRatesFetcher = new ExchangeRatesFetcher($httpClient);

        // Call the getExchangeRates method
        $exchangeRates = $exchangeRatesFetcher->getExchangeRates();

        // Assert that the exchange rates are fetched successfully with a large amount of data
        $this->assertCount(10001, $exchangeRates);
    }
}
