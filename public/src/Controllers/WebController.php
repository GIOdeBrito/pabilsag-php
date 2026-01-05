<?php

use Pabilsag\Attributes\Route;
use Pabilsag\Web\CurlClient;
use Pabilsag\Http\Response;

class WebController
{
	private ?CurlClient $curl = NULL;

	public function __construct (CurlClient $client)
	{
		$this->curl = $client;
	}

	#[Route(
		method: 'GET',
		path: '/public/web/curl',
		description: 'Testing the CurlClient.'
	)]
	public function curlTest ($req, $res): Response
	{
		$curl = $this->curl;
		$response = $curl->get()->url('https://www.pudim.com.br')->send();

		return $res->status(200)->html($response);
	}

	#[Route(
		method: 'GET',
		path: '/public/web/curlqp',
		description: 'Curl request with query parameters.'
	)]
	public function curlQP ($req, $res): Response
	{
		$curl = $this->curl;
		$response = $curl
			->get()
			->url('https://postman-echo.com/get')
			->setQuery([ 'origin' => 'Pabilsag\\CurlClient', 'date' => date('Y-m-d H:i:s') ])
			->send();

		return $res->status(200)->json($response);
	}
}

?>