<?php 

use GioPHP\Services\Logger;
use GioPHP\Interfaces\MiddlewareInterface;

// Parse the Request's body into a JSON
// and prints its contents to the Log

class JSONBody implements MiddlewareInterface
{
	public function __construct (
		public Logger $logger
	) {}
	
	public function handle ($req, $res, callable $next)
	{
		$body = json_decode($req->getBodyAsString());
		
		if($req->getMethod() === 'POST' && !is_null($body))
		{
			$this->logger->info(
				"JSON Input: "
				.
				json_encode($body, JSON_PRETTY_PRINT)
			);
		}
		else
		{
			$this->logger->info("Request body is null or empty. Skipping JSON output.");
		}
		
		return $next($req, $res);
	}
}

?>