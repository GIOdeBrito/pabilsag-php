<?php 

use GioPHP\Services\Logger;
use GioPHP\Interfaces\MiddlewareInterface;
use GioPHP\Enums\HttpCode;

// This is a jest middleware
// it simply ceases any GET requests
// and prints a silly message

class GETNuke implements MiddlewareInterface
{
	public function __construct (
		public Logger $logger
	) {}
	
	public function handle ($req, $res, callable $next)
	{
		if($req->getMethod() === 'GET')
		{
			$this->logger->info("Stopped a GET request }:)");
			
			return $res->status(HttpCode::Forbidden)->html(<<<HTML
				<h1>Halt, Rogue!</h1>
				<p>Thy pitiful GET request hath been obliterate'd!</p>
			HTML);
		}
		
		return $next($req, $res);
	}
}

?>