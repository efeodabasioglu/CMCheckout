<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyController extends Controller
{

    /**
     * @return mixed|string
     */
    public function index(){

        $client = new Client();

        try {
            $response = $client->request('GET', 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest', [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => '4ff8f399-9440-4d2e-89b9-dae544dcb48e']
            ]);
            //return data in json format
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            return 'exception';
        }

    }

    public function login(){

        return 'login success';

    }

}
