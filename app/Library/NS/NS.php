<?php

namespace App\Library\NS;

use App\Library\NS\Objects\Journey;
use Carbon\Carbon;
use Goutte\Client as GoutteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class NS
{
	protected $cookieKey = 'ns-cookie';
	protected $credentials;

	public function __construct()
	{
		$this->credentials = [
			'email'      => config('ns.email'),
			'password'   => config('ns.password'),
			'cardNumber' => config('ns.card_number'),
//		'JSESSIONID' => 'A9C8488182AF61CF05203EFC69AD8DD6.su025v281-2'
		];

		$cookieJar = new CookieJar();

		if (isset($this->credentials['JSESSIONID'])) {
			$this->saveCookie($this->credentials['JSESSIONID']);
		}

		if ($this->isLoggedIn()) {
			$cookieJar->set(session($this->cookieKey));
		}

		$this->client = new GoutteClient([], null, $cookieJar);
	}

	public function login()
	{
		if (!$this->isLoggedIn()) {
			$crawler = $this->client->request('GET', 'https://login.ns.nl/login');
			$form = $crawler->selectButton('Inloggen')->form();
			$this->client->submit($form,
				['email' => $this->credentials['email'], 'password' => $this->credentials['password']]);

			$this->saveCookie();
		}
	}

	/**
	 * Login the user, go to the start page and press "continue". The end result is that the user is on the startpage
	 *
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected function start()
	{
		$this->login();

		$crawler = $this->client->request('GET', 'https://www.ns.nl/selfservice/home');

		$continueButton = $crawler->selectButton('Continue');
		if ($continueButton->count()) {
			$form = $continueButton->form();
			return $this->client->submit($form);
		}

		return $crawler;
	}

	public function showReishistorie()
	{
		$crawler = $this->start();

		/*
		 * Click on "Mijn reishistorie (trein)"
		 */
		$link = $crawler->selectLink('Mijn reishistorie (trein)')->link();
		$crawler = $this->client->click($link);

		/*
		 * Select the right card number and press the search button
		 */
		$form = $crawler->selectButton('Zoeken')->form();
		$crawler = $this->client->submit($form, [
			'ovcpKaart' => $this->credentials['cardNumber'],
			'search'    => 'Zoeken'
		]);


		$journeys = $this->recursivelyClickNext($crawler, function(Crawler $crawler) {
		    return $this->journeyInformation($crawler);
        });

        $journeyCollection = collect($journeys)
            ->filter(function($journey) { // Filter null values
            return $journey;
        })
            ->filter(function(Journey $journey) { // Filter journeys which do have the same location for checkin as checkout
            return $journey->checkinLocation != $journey->checkoutLocation;
        });

        return new JourneyHistoryResponse($journeyCollection);
	}

    /**
     * Recursively clicks on the "volgende pagina" untill there is no
     *
     * @param Crawler $crawler
     * @param \Closure $fn
     * @return array|mixed
     */
	protected function recursivelyClickNext(Crawler $crawler, \Closure $fn)
    {
        $journeys = call_user_func($fn, $crawler);

        /*
         * Click on "volgende pagina"
         */
        $nextPage = $crawler->selectLink('volgende pagina');

        if (!$nextPage->count()) {
            return $journeys;
        } else {
            $link = $nextPage->link();
            $crawler = $this->client->click($link);

            return array_merge($journeys, $this->recursivelyClickNext($crawler, $fn));
        }
    }

	protected function journeyInformation(Crawler $crawler)
	{
	    return $crawler->filter('.history tbody')->each(function (Crawler $node, $i) {

            // Grab the first tr element with the class "journey". This is the element which contains all the information about the journey
            $journeyInformation = $node->filterXPath("(//tr[contains(concat(' ', @class, ' '), ' journey ')])[1]");

            // If the element was not found. There is no journey information in this element. It could be a transaction or something.
            if ($journeyInformation->count()) {

                $locationInformation = $journeyInformation->filterXPath('//td[2]');
                $checkinLocationInformation = trim($locationInformation->filterXPath('//span[1]')->text());
                $checkoutLocationInformation = trim($locationInformation->filterXPath('//span[2]')->text());

                // Check for the format "12:12 City"
                $locationInformationRegex = "/([0-9]){2}:([0-9]){2} ([a-z].*|[A-Z].*)/";

                if (preg_match($locationInformationRegex, $checkinLocationInformation)) {
                    $checkinTime = explode(' ', $checkinLocationInformation)[0];
                    $checkinLocation = explode(' ', $checkinLocationInformation)[1];
                } else {
                    $checkinTime = null;
                    $checkinLocation = $checkinLocationInformation;
                }

                if (preg_match($locationInformationRegex, $checkoutLocationInformation)) {
                    $checkoutTime = explode(' ', $checkoutLocationInformation)[0];
                    $checkoutLocation = explode(' ', $checkoutLocationInformation)[1];
                } else {
                    $checkoutTime = null;
                    $checkoutLocation = $checkoutLocationInformation;
                }

                return (new Journey())
                    ->setDate(Carbon::createFromFormat('d-m-Y',
                        trim($journeyInformation->filterXPath('//td[1]')->text())))
                    ->setCheckinTime($checkinTime)
                    ->setCheckinLocation($checkinLocation)
                    ->setCheckoutTime($checkoutTime)
                    ->setCheckoutLocation($checkoutLocation);

            }

            return null;
        });
	}

	protected function isLoggedIn()
	{
		return request()->session()->has($this->cookieKey);
	}

	protected function saveCookie($sessionId = null)
	{
		if (!$sessionId) {
			// Get the cookie Jar
			$cookieJar = $this->client->getCookieJar();
			$sessionId = $cookieJar->get('JSESSIONID');
		} else {
			$sessionId = new Cookie('JSESSIONID', $sessionId);
		}

		session([$this->cookieKey => $sessionId]);
	}
}