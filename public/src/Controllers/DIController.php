<?php

use GioPHP\MVC\Controller;
use GioPHP\Enums\ContentType;

use GioPHP\Attributes\Route;

use GioPHP\Services\Logger;

class DIController
{
	protected Logger $logger;

	public function __construct (Logger $logger)
	{
		$this->logger = $logger;
	}

	#[Route(
		method: 'GET',
		path: '/public/injection',
		description: 'Dependency injection test.'
	)]
	public function DITest ($req, $res): void
	{
		//var_dump($this->logger);

		$this->logger->info("Logger created from the dependency injection container!");

		$res->status(200)->html('<p>Check the log output.</p>');
	}
}

?>