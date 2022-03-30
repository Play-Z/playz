<?php
namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaypalService {

    private $client;
    private $token;

    public function __construct()
    {
        $this->client = new Client();
    }

    protected function login(){
        try {
            $response = $this->client->request(
                'POST',
                'https://api-m.sandbox.paypal.com/v1/oauth2/token',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                        //                    'Authorization' => base64_encode('Basic AfacXPAMghvRJMdVwY_2C_yvYE92wK2ygsIROGyc3m02m8Dup3i1mtL--gOfx8jE8dkVpyng8REoXNz7:EF35ajhoW6lXorwqbKXiQYf1xUsAPMBiMEd4xV8iJ5SSUoOvHyuquW4SbSWncZkjNqiAnDaNTR9bfNbb')
                    ],
                    'body' => "grant_type=client_credentials",
                    'auth' => [
                        'AfacXPAMghvRJMdVwY_2C_yvYE92wK2ygsIROGyc3m02m8Dup3i1mtL--gOfx8jE8dkVpyng8REoXNz7',
                        'EF35ajhoW6lXorwqbKXiQYf1xUsAPMBiMEd4xV8iJ5SSUoOvHyuquW4SbSWncZkjNqiAnDaNTR9bfNbb',
                        'basic'
                    ]
                ]
            );
            $this->token = json_decode($response->getBody()->getContents())->access_token;

        } catch (GuzzleException $e) {
            dd($e);
        }
    }

    public function verifyOrder($id): bool{
        $this->login();
        $response = $this->client->request(
            'GET',
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/'.$id,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$this->token
                ]
            ]
        );
        $orderData = json_decode($response->getBody()->getContents());
        return $orderData->status === 'COMPLETED';
    }
}
