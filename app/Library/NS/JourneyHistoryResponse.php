<?php

namespace App\Library\NS;


use Illuminate\Support\Collection;
use JsonSerializable;

class JourneyHistoryResponse implements JsonSerializable
{
    protected $count;
    protected $journeys;

    public function __construct(Collection $journeys)
    {
        $this->setJourneys($journeys);
    }

    /**
     * @return mixed
     */
    public function getJourneys()
    {
        return $this->journeys;
    }

    /**
     * @param mixed $journeys
     */
    public function setJourneys(Collection $journeys)
    {
        $this->journeys = $journeys->values();
        $this->count = $journeys->count();
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return get_object_vars($this);
    }
}