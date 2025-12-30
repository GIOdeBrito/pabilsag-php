<?php 

use GioPHP\Services\Logger;
use GioPHP\Interfaces\MiddlewareInterface;
use GioPHP\Enums\HttpCode;

class GETNuke implements MiddlewareInterface
{
	public function __construct (
		public Logger $logger
	) {}
	
	public function handle ($req, $res, callable $next)
	{
		if($req->getMethod() === 'GET')
		{
			$this->logger->info("Stopped a GET request");
			
			return $res->status(HttpCode::Forbidden)->html(<<<HTML
				<h1>Stop right there</h1>
				<p>Your GET request has been nuked</p>
			HTML);
		}
		
		return $next($req, $res);
	}
}

?>