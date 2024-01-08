<?php

namespace App\Tests\Price;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\PriceController;

class PriceTest extends WebTestCase
{
    public function testShouldDecodeJsonPayload()
    {
        $payload = [
            'items' => ['item1', 'item2'],
            'checkoutCurrency' => 'USD'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($payload['items'], $responseData['items']);
        $this->assertEquals($payload['checkoutCurrency'], $responseData['checkoutCurrency']);
    }

    public function testShouldReturnJsonResponseWithFields()
    {
        $payload = [
            'items' => ['item1', 'item2'],
            'checkoutCurrency' => 'USD'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('items', $responseData);
        $this->assertArrayHasKey('checkoutCurrency', $responseData);
    }

    public function testShouldReturnJsonResponseWithErrorMessageAndStatusCode()
    {
        $payload = [
            'items' => ['item1', 'item2']
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals('Invalid payload', $responseData['error']);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturnJsonResponseWithErrorMessageAndStatusCodeForMissingItemsField()
    {
        $payload = [
            'checkoutCurrency' => 'USD'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals('Invalid payload', $responseData['error']);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturnJsonResponseWithErrorMessageAndStatusCodeForMissingCheckoutCurrencyField()
    {
        $payload = [
            'items' => ['item1', 'item2']
        ];
        $request = new Request([], [], [], [], [], [], json_encode($payload));
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals('Invalid payload', $responseData['error']);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturnJsonResponseWithErrorMessageAndStatusCodeForInvalidJson()
    {
        $payload = 'invalid json';
        $request = new Request([], [], [], [], [], [], $payload);
        $controller = new PriceController();
        $response = $controller->processPayload($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals('Invalid payload', $responseData['error']);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
