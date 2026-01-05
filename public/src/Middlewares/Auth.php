<?php

use Pabilsag\Interfaces\MiddlewareInterface;
use Pabilsag\Services\Logger;
use Pabilsag\Enums\HttpCode;

class AuthMiddleware implements MiddlewareInterface
{
	public function __construct (
		public Logger $logger
	) {}
	
	public function handle ($req, $res, callable $next)
	{
		if(!isset($req->getForm()->usr))
		{
			$this->logger->error("Request does not contain basic auth");
			
			return $res->status(HttpCode::InternalServerError)->json([
				'message' => 'Missing basic auth',
				'status' => HttpCode::InternalServerError
			]);
		}
		
		$response = $next($req, $res);
		
		$this->logger->info("Middleware will now check the response object");
		
		return $response;
	}
}

?>