<?php

use GioPHP\MVC\Controller;

use GioPHP\Attributes\Route;
use GioPHP\Web\CurlClient;

class WebController extends Controller
{
	#[Route(
		method: 'GET',
		path: '/public/web/curl',
		description: 'Testing the CurlClient.'
	)]
	public function curlTest ($req, $res): void
	{
		$curl = new CurlClient();
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
		$curl = new CurlClient();
		$response = $curl
			->get()
			->url('https://postman-echo.com/get')
			->setQuery([ 'name' => 'Gio', 'age' => '???' ])
			->send();

		$res->status(200)->html($response);
	}
}

?>