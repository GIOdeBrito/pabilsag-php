<?php

use Pabilsag\Attributes\Route;

use Pabilsag\Http\Response;

use function Pabilsag\Helpers\Http\{ get_ip_addr, is_ip_valid };

class ApiController
{
	#[Route(
		method: 'POST',
		path: '/public/api/v1/json/deserialize'
	)]
	public function apiDeserialize ($req, $res): Response
	{
		$body = $req->getBody();
		
		// TODO: Implement...

		return $res->status(200)->json();
	}
	
	#[Route(
		method: 'POST',
		path: '/public/api/v1/session'
	)]
	public function sessionDump ($req, $res): Response
	{
		session_start();
		
		return $res->status(200)->json([
			'message' => 'Session test',
			'ip' => get_ip_addr(),
			'ip_valid' => is_ip_valid(get_ip_addr())
		]);
	}

	#[Route(
		method: 'POST',
		path: '/public/api/v1/jsondump'
	)]
	public function jsonDump ($req, $res): Response
	{
		return $res->status(200)->json([
			'message' => 'JSON received',
			'data' => $req->getBody()
		]);
	}
	
	#[Route(
		method: 'POST',
		path: '/public/api/v1/publicenvs'
	)]
	public function getEnvs ($req, $res): Response
	{
		$data = [
			'app_name' => getenv('APP_NAME'),
			'version' => getenv('VERSION'),
			'secret_key' => getenv('SUPER_SECRET_KEY')
		];
		
		return $res->status(200)->json([
			'message' => 'Public environment variables',
			'data' => $data
		]);
	}
}

?>