<?php

namespace Hpdc;

use GuzzleHttp\Client;

class Authentication
{
	/**
	 * Guzzle Client
	 * 
	 * @var \GuzzleHttp\Client
	 */
	protected $client;

	/**
	 * API URL
	 * 
	 * @var null|string
	 */
	protected $url = null;

	/**
	 * Your OAuth Client ID
	 * 
	 * @var null|integer
	 */
	protected $api_client = null;

	/**
	 * Your OAuth Client Secret
	 * 
	 * @var null|string
	 */
	protected $api_secret = null;

	/**
	 * Instantiate the class.
	 * 
	 * @param \GuzzleHttp\Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Build the Authorize query.
	 * 
	 * @return string
	 */
	public function make()
	{
		if (is_null($this->url) || is_null($this->api_client)) {
			return "Ensure you set URL and Client ID before building a query.";
		}

		$query = http_build_query([
	        'client_id'     => $this->api_client,
	        'response_type' => 'code',
	        'scope'         => 'create-hpds edit-hpds view-hpds',
	    ]);

	    return $this->url . '/oauth/authorize?' . $query;
	}

	/**
	 * Send the token request to the API.
	 * 
	 * @param  string $code
	 * @return string
	 */
	public function send($code)
	{
		if (is_null($this->url) || is_null($this->api_client) || is_null($this->api_secret)) {
			return "Ensure you set URL, Client ID and Secret before retrieving a token.";
		}

		$response = $this->client->post($this->url . '/oauth/token', [
	        'form_params' => [
	            'grant_type'    => 'authorization_code',
	            'client_id'     => $this->api_client,
	            'client_secret' => $this->api_secret,
	            'code'          => $code,
	        ],
	    ]);

	    return json_decode((string)$response->getBody(), true);
	}

	/**
	 * Set the API url.
	 * 
	 * @param string $url
	 * @return \Hpdc\Authentication
	 */
	public function setUrl($url)
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * Set the client id.
	 * 
	 * @param integer $client_id
	 * @return \Hpdc\Authentication
	 */
	public function setClient($client_id)
	{
		$this->api_client = $client_id;

		return $this;
	}

	/**
	 * Set the client secret.
	 * 
	 * @param string $client_secret
	 * @return \Hpdc\Authentication
	 */
	public function setSecret($client_secret)
	{
		$this->api_secret = $client_secret;

		return $this;
	}
}