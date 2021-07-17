<?php


namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Bot
{

    private $apiUrl = "https://api.telegram.org/bot";


    public function method(string $methodName, array $attributes): void
    {
        $baseUri = $this->apiUrl . env("TELEGRAM_BOT_TOKEN") . "/";
        $client = new Client(["base_uri" => $baseUri]);
        try {
            $client->post($methodName, ["query" => $attributes]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }
}
