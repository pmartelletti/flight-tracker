<?php
namespace FlightTracker\Model;
use Carbon\Carbon;

class Flight
{
    private $departure;
    private $arrival;
    private $from;
    private $to;
    private $duration;
    private $stops;

    public function __construct($from, $to, Carbon $departure, Carbon $arrival, $duration, $stops)
    {
        $this->from = $from;
        $this->to = $to;
        $this->departure = $departure;
        $this->arrival = $arrival;
        $this->duration = $duration;
        $this->stops = $stops;
    }

    public function getArrayDetails()
    {
        return [
            $this->from, $this->to, $this->departure->format('d-m-Y H:i'), $this->arrival->format('d-m-Y H:i'), $this->duration, $this->stops
        ];
    }

    public function agony()
    {
        // custom algorithm using duration, price, etc
    }

    public function getStops()
    {
        return $this->stops;
    }

    public function setStops($stops)
    {
        $this->stops = $stops;

        return $this;
    }

    /**
     * Get the value of Departure
     *
     * @return mixed
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * Set the value of Departure
     *
     * @param mixed departure
     *
     * @return self
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;

        return $this;
    }

    /**
     * Get the value of Arrival
     *
     * @return mixed
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * Set the value of Arrival
     *
     * @param mixed arrival
     *
     * @return self
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;

        return $this;
    }

    /**
     * Get the value of From
     *
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the value of From
     *
     * @param mixed from
     *
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the value of To
     *
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the value of To
     *
     * @param mixed to
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get the value of Duration
     *
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set the value of Duration
     *
     * @param mixed duration
     *
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

}
