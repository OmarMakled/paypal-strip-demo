<?php

namespace App\Services\Payments;

use GuzzleHttp\Client;

class PayPal
{
    private $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function init($amount)
    {
        $response = $this->client->request('POST', env('PAYPAL_API'),
            [
                'auth' => [env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET')],
                'json' => [
                    "intent" => "sale",
                    "payer" => [
                        "payment_method" => "paypal",
                    ],
                    "transactions" => [
                        [
                            "amount" => [
                                "total" => $amount,
                                "currency" => "USD",
                            ],
                        ],
                    ],
                    "redirect_urls" => [
                        "return_url" => env('APP_URL'),
                        "cancel_url" => env('APP_URL'),
                    ],
                ],
            ]
        );

        return json_decode($response->getBody()->getContents());
    }
}
