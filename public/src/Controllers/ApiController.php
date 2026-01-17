<?php

use Pabilsag\Attributes\Route;

class ApiController
{
	#[Route(
		method: 'POST',
		path: '/public/api/v1/json/deserialize',
		description: 'JSON deserialization'
	)]
	public function apiDeserialize ($req, $res): Response
	{
		$body = $req->getBody();



		return $res->status(200)->json();
	}

	#[Route(
		method: 'POST',
		path: '/public/api/v1/jsondump',
		description: 'JSON dump'
	)]
	public function jsonDump ($req, $res): Response
	{
		return $res->status(200)->json(
			'message' => 'JSON received',
			'data' => $req->getBody()
		);
	}
}

?>