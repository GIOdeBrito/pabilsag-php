<?php 

namespace GioPHP\Middlewares;

use GioPHP\Services\Logger;
use GioPHP\Interfaces\MiddlewareInterface;

// Parse the Request's body into a proper JSON
// object and also prints its contents to the Log

class JSONParse implements MiddlewareInterface
{
	public function __construct (
		public Logger $logger
	) {}
	
	public function handle ($req, $res, callable $next)
	{
		if($req->getMethod() !== 'POST')
		{
			return $next($req, $res);
		}
		
		// Associative is set as false as we want
		// to make sure the parsed JSON is an object
		$body = json_decode($req->getBody(), false);
		
		if(is_null($body))
		{
			$this->logger->error("Request's body is null, empty or invalid");
			return $next($req, $res);
		}
		
		$req->setParsedBody($body);
		
		// Logs the pretty JSON
		$this->logger->info(
			sprintf("JSON request body: %s", json_encode($body, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR))
		);
		
		return $next($req, $res);
	}
}

?>