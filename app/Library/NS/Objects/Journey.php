<?php

namespace App\Library\NS\Objects;

class Journey
{
	public $checkinTime;
	public $checkoutTime;
	public $date;
	public $checkinLocation;
	public $checkoutLocation;
    public $moneyBackUrl;

	/**
	 * @param mixed $checkinTime
	 * @return Journey
	 */
	public function setCheckinTime($checkinTime)
	{
		$this->checkinTime = $checkinTime;
		return $this;
	}

	/**
	 * @param mixed $checkoutTime
	 * @return Journey
	 */
	public function setCheckoutTime($checkoutTime)
	{
		$this->checkoutTime = $checkoutTime;
		return $this;
	}

	/**
	 * @param mixed $date
	 * @return Journey
	 */
	public function setDate($date)
	{
		$this->date = $date;
		return $this;
	}

	/**
	 * @param mixed $checkinLocation
	 * @return Journey
	 */
	public function setCheckinLocation($checkinLocation)
	{
		$this->checkinLocation = $checkinLocation;
		return $this;
	}

	/**
	 * @param mixed $checkoutLocation
	 * @return Journey
	 */
	public function setCheckoutLocation($checkoutLocation)
	{
		$this->checkoutLocation = $checkoutLocation;
		return $this;
	}

    /**
     * @return mixed
     */
    public function getMoneyBackUrl()
    {
        return $this->moneyBackUrl;
    }

    /**
     * @param mixed $moneyBackUrl
     * @return Journey
     */
    public function setMoneyBackUrl($moneyBackUrl)
    {
        $this->moneyBackUrl = $moneyBackUrl;
        return $this;
    }

}