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
		//echo "aaaaaaaaaa";
		$curl = new CurlClient('https://www.pudim.com.br/');

		$response = $curl->send();

		//echo "aaaaa";
		var_dump($response);

		$res->end();
	}
}

?>