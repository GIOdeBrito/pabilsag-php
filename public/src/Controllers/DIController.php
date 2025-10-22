<?php

use GioPHP\Enums\ContentType;
use GioPHP\Attributes\Route;
use GioPHP\Services\Logger;

class DIController
{
	protected Logger $logger;
	protected SessionManager $manager;

	public function __construct (Logger $logger, SessionManager $manager)
	{
		$this->logger = $logger;
		$this->manager = $manager;
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

	#[Route(
		method: 'GET',
		path: '/public/injection/session',
		description: 'Dependency injection test.'
	)]
	public function Session ($req, $res): void
	{
		$content = $this->manager->getSession();

		$res->status(200)->json($content);
	}
}

?>