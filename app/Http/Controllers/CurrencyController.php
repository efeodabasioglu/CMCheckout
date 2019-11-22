<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{

    /**
     * @return mixed|string
     */
    public function index(){

        $client = new Client();
        //get bitcoin currency from CoinMarketAPI (bitcoin id =1)
        try {
            $response = $client->request('GET', 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => '4ff8f399-9440-4d2e-89b9-dae544dcb48e'],
                'query' => [
                    'id' => '1'],
            ]);
            //return data in json format
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            return 'exception';
        }

    }


    /**
     * @param Request $request
     * @return mixed|string
     */
    public function priceConversion(Request $request){

        $client = new Client();
        //if using coin for price conversion
        $amount = $request->get('amount');

        try {
            $response = $client->request('GET', 'https://pro-api.coinmarketcap.com/v1/tools/price-conversion', [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => '4ff8f399-9440-4d2e-89b9-dae544dcb48e'],
                'query' => [
                    'id' => '1',
                    'amount' => $amount],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }


    }

}
