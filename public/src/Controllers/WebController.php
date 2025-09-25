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
}

?>