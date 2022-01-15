<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Address;

class AddressService
{
	private $addressAPIBaseUrl;

	private $client;

	/**
	 * @param string $addressAPIBaseUrl
	 **/
	public function __construct(string $addressAPIBaseUrl, HttpClientInterface $client)
    {
        $this->addressAPIBaseUrl = $addressAPIBaseUrl;
        $this->client = $client;
    }

	/**
	 * Get the address score
	 * 
	 * @param Address $address
	 * 
	 * @return float 
	 **/
	public function getAddressScore(Address $address): float
	{
		$url = $this->addressAPIBaseUrl . "search/";

		$query = urlencode($address->getFullInline());
		$query .= (!is_null($address->getPostalCode())) ? "&postcode=" . $address->getPostalCode() : "";
		$query .= "&limit=1";
		$query .= "&autocomplete=0";

		$response = $this->client->request('GET', $url, [
            'query' => [
                'q' => $query
            ]
        ]);

        $result = json_decode($response->getContent());

		return isset($result->features[0]->properties->score) ? $result->features[0]->properties->score : 0;
	}
}