<?php
namespace FlightTracker\Model;
use FlightTracker\Model\Flight;
use FlightTracker\Model\Itinerary;

class Itinerary
{
    private $price; // in EUR, for the moment. @TODO: implement Money class?
    private $outbound;
    private $inbound;
    private $bookingLinks;

    public function __construct(Flight $outbound, Flight $inbound, $price)
    {
        $this->price = $price;
        $this->outbound = $outbound;
        $this->inbound = $inbound;
        $this->bookingLinks = [];
    }

    public function nicest(Itinerary $other)
    {
        return $this->agony() < $other->agony();
    }

    public function agony()
    {
        return $this->price;
        // return ($this->inbound->agony() + $this->inbound->agony()) / 2;
    }

    /**
     * Get the value of Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of Price
     *
     * @param mixed price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of Outbound Flight
     *
     * @return mixed
     */
    public function getOutboundFlight()
    {
        return $this->outboundFlight;
    }

    /**
     * Set the value of Outbound Flight
     *
     * @param mixed outboundFlight
     *
     * @return self
     */
    public function setOutboundFlight($outboundFlight)
    {
        $this->outboundFlight = $outboundFlight;

        return $this;
    }

    /**
     * Get the value of Inbound Flight
     *
     * @return mixed
     */
    public function getInboundFlight()
    {
        return $this->inboundFlight;
    }

    /**
     * Set the value of Inbound Flight
     *
     * @param mixed inboundFlight
     *
     * @return self
     */
    public function setInboundFlight($inboundFlight)
    {
        $this->inboundFlight = $inboundFlight;

        return $this;
    }



    /**
     * Get the value of Outbound
     *
     * @return mixed
     */
    public function getOutbound()
    {
        return $this->outbound;
    }

    /**
     * Set the value of Outbound
     *
     * @param mixed outbound
     *
     * @return self
     */
    public function setOutbound($outbound)
    {
        $this->outbound = $outbound;

        return $this;
    }

    /**
     * Get the value of Inbound
     *
     * @return mixed
     */
    public function getInbound()
    {
        return $this->inbound;
    }

    /**
     * Set the value of Inbound
     *
     * @param mixed inbound
     *
     * @return self
     */
    public function setInbound($inbound)
    {
        $this->inbound = $inbound;

        return $this;
    }

    /**
     * Get the value of Booking Links
     *
     * @return mixed
     */
    public function getBookingLinks()
    {
        return $this->bookingLinks[0];
    }

    /**
     * Set the value of Booking Links
     *
     * @param mixed bookingLinks
     *
     * @return self
     */
    public function setBookingLinks($bookingLinks)
    {
        $this->bookingLinks = $bookingLinks;

        return $this;
    }

    /**
     * @return self;
     */
    public function addBookingLink($link)
    {
        $this->bookingLinks[] = $link;

        return $this;
    }
}
