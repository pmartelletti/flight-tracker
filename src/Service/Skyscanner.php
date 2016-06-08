<?php
namespace FlightTracker\Service;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Skyscanner
{
    private $client;
    private $sessionUrl;
    private $baseUri;
    private $apiKey;

    public function __construct($key = '')
    {
        // $this->client = new Client([
        //     'base_uri' => 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0'
        // ]);
        $this->apiKey = $key;
        $this->baseUri = 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0';
    }

    public function startSession($origin, $destination, Carbon $from, Carbon $to)
    {
        $client = new Client();
        $params = [
            'apiKey' => $this->apiKey,
            'country' => 'AR',
            'currency' => 'EUR',
            'locale' => 'EN',
            'adults' => 1,
            'originplace' => $origin . '-sky',
            'destinationplace' => $destination . '-sky',
            'outbounddate' => $from->format('Y-m-d'),
            'inbounddate' => $to->format('Y-m-d')
        ];

        $response = $client->request('POST', $this->baseUri, ['form_params' => $params ]);
        if($response->getStatusCode() === 201) {
            $headers = $response->getHeaders();
            $r2 = $client->get($headers['Location'][0] . '?apiKey=' . $this->apiKey);
            dump((string) $r2->getBody()); die;
            $json_results = json_decode((string) $r2->getBody());
            dump($json_results);
        }
        die;
    }
}
