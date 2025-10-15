<?php

use GioPHP\MVC\Controller;
use GioPHP\Enums\ContentType;

use GioPHP\Attributes\Route;

//use GioPHP\Services\Logger;

class DIController
{
	protected Logger $logger;

	public function __construct (GioPHPApp\Services\Logger $logger)
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
		var_dump($this->logger);

		$res->end(200);
	}
}

?>