<?php
namespace FlightTracker\Model;

use FlightTracker\Model\Itinerary;

class SuitableItineraries implements \IteratorAggregate
{
    private $itineraries;
    private $maxResults;

    public function __construct()
    {
        $this->itineraries = [];
        $this->maxResults = 10;
    }

    public function getIterator() {
        return new \ArrayIterator($this->itineraries);
    }

    public function addIfBetter(Itinerary $itinerary)
    {
        if(count($this->itineraries) < $this->maxResults) {
            $this->itineraries[] = $itinerary;
            $this->sort();
            return $this;
        }
        // we check if it's better than last one
        $lastOne = $this->itineraries[$this->maxResults - 1];
        if($itinerary->nicest($lastOne)) {
            $this->itineraries[$this->maxResults - 1] = $itinerary;
            $this->sort();
        }

        return $this;
    }

    private function sort()
    {
        usort($this->itineraries, function($a, $b){
            return $a->agony() > $b->agony();
        });
    }
}
