<?php

use GioPHP\Interfaces\Middleware;
use GioPHP\Services\Logger;

class AuthMiddleware implements Middleware
{
	private Logger $logger;
	
	public function __construct (Logger $logger)
	{
		$this->logger = $logger;
	}
	
	public function handle ($req, $res, callable $next)
	{
		if(!isset($req->body->authBasic))
		{
			$this->logger->info("Request does not contain basic auth");
		}
		
		$response = $next($req, $res);
		
		$this->logger->info("Middleware will now check the response object");
		
		return $response;
	}
}

?>