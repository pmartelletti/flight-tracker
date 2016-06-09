<?php
namespace FlightTracker\Service;

use GuzzleHttp\Client;
use Carbon\Carbon;
use FlightTracker\Model\Flight;
use FlightTracker\Model\Itinerary;
use FlightTracker\Model\SuitableItineraries;

class Skyscanner
{
    private $baseUri;
    private $apiKey;
    private $suitableItineraries;

    public function __construct($key = '')
    {
        $this->apiKey = $key;
        $this->baseUri = 'http://partners.api.skyscanner.net/apiservices/pricing/v1.0';
        $this->suitableItineraries = new SuitableItineraries(2);
    }

    public function trackFlights($origin, $destination, Carbon $from, Carbon $to)
    {
        $client = new Client();
        $params = [
            'apiKey' => $this->apiKey,
            'country' => 'AR',
            'currency' => 'EUR',
            'locale' => 'EN',
            'adults' => 1,
            'originplace' => $origin,
            'destinationplace' => $destination,
            'locationschema' => 'Iata', // use IATA codes instead of skyscanner ID's
            'outbounddate' => $from->format('Y-m-d'),
            'inbounddate' => $to->format('Y-m-d')
        ];

        $response = $client->request('POST', $this->baseUri, ['form_params' => $params ]);
        if($response->getStatusCode() === 201) {
            $headers = $response->getHeaders();
            $url = $headers['Location'][0];
            $queryParams = [
                'apiKey' => $this->apiKey,
                'duration' => 180, // max 5 hours of travelling. @TODO: make it configurable
                // 'outbounddepartstarttime' => '18:30', // departure time. @TODO: make it configurable,
                // 'stops' => 1, // max stops allowed. @TODO: make it configurable
                // 'pageindex' => 0,
                // 'pagesize' => 10
            ];
            $retry = true;
            while($retry) {
                $r2 = $client->request('GET', $url, ['query' => $queryParams]);
                if($r2->getStatusCode() === 200) {
                    $headers = $r2->getHeaders();
                    $url = $headers['Location'][0]; // new header
                    // request was succesfull, we parse data
                    $json_results = json_decode((string) $r2->getBody(), true);
                    $status = $json_results['Status'];
                    $itineraries = $json_results['Itineraries'];
                    $legs = $json_results['Legs'];
                    $this->parseTrips($itineraries, $legs, $origin, $destination);
                    if($status === 'UpdatesComplete') $retry = false;
                } else {
                    $retry = false;
                }
            }
        }
    }

    private function parseTrips($itineraries, $legs, $from, $to)
    {
        if(count($itineraries) > 0) {
            foreach($itineraries as $itinerary) {
                $key = array_search($itinerary['OutboundLegId'], array_column($legs, 'Id'));
                $outbound = $legs[$key];
                $key = array_search($itinerary['InboundLegId'], array_column($legs, 'Id'));
                $inbound = $legs[$key];
                $this->parseItinerary($itinerary, $inbound, $outbound, $from, $to);
            }
        }
        return;
    }

    private function parseItinerary($itineray, $inboundLeg, $outboundLeg, $from, $to)
    {
        $outbound = new Flight($from, $to, new Carbon($outboundLeg['Departure']), new Carbon( $outboundLeg['Arrival']), $outboundLeg['Duration'] / 60, count($outboundLeg['Stops']));
        $inbound = new Flight($to, $from, new Carbon($inboundLeg['Departure']), new Carbon( $inboundLeg['Arrival']), $inboundLeg['Duration'] / 60, count($inboundLeg['Stops']));
        $itinerary = new Itinerary($outbound, $inbound, $itineray['PricingOptions'][0]['Price']);
        if(array_key_exists('DeeplinkUrl', $itineray['PricingOptions'][0])) {
            $itinerary->addBookingLink($itineray['PricingOptions'][0]['DeeplinkUrl']);
        }
        $this->suitableItineraries->addIfBetter($itinerary);
    }

    /**
     * @return SuitableItineraries
     */
    public function getSuitableItineraries()
    {
        return $this->suitableItineraries;
    }
}
