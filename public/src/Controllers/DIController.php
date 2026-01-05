<?php

use Pabilsag\Enums\ContentType;
use Pabilsag\Attributes\Route;
use Pabilsag\Services\Logger;
use Pabilsag\Http\Response;

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
	public function DITest ($req, $res): Response
	{
		$this->logger->info("Logger created from the dependency injection container!");

		return $res->status(200)->html('<p>Check the log output.</p>');
	}

	#[Route(
		method: 'GET',
		path: '/public/injection/session',
		description: 'Dependency injection test.'
	)]
	public function Session ($req, $res): Response
	{
		$content = $this->manager->getSession();

		return $res->status(200)->json($content);
	}
}

?>