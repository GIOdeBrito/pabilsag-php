<?php

use GioPHP\MVC\Controller;

use GioPHP\Attributes\Route;
use GioPHP\Web\CurlClient;

class WebController extends Controller
{
	private ?CurlClient $curl = NULL;

	public function __construct ($database, $logger)
	{
		parent::__construct($database, $logger);

		$this->curl = new CurlClient();
	}

	#[Route(
		method: 'GET',
		path: '/public/web/curl',
		description: 'Testing the CurlClient.'
	)]
	public function curlTest ($req, $res): void
	{
		$curl = $this->curl;
		$response = $curl->get()->url('https://www.pudim.com.br')->send();

		$res->status(200)->html($response);
	}

	#[Route(
		method: 'GET',
		path: '/public/web/curlqp',
		description: 'Curl request with query parameters.'
	)]
	public function curlQP ($req, $res): void
	{
		$curl = $this->curl;
		$response = $curl
			->get()
			->url('https://postman-echo.com/get')
			->setQuery([ 'origin' => 'GioPHP\\CurlClient', 'date' => date('Y-m-d H:i:s') ])
			->send();

		$res->status(200)->json($response);
	}
}

?>