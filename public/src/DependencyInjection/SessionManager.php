<?php

use GioPHP\Services\Logger;

class SessionManager
{
	private Logger $logger;

	public function __construct (Logger $logger)
	{
		$this->logger = $logger;
		$logger->info("Session Manager was start'd. Yahoooo!");
		session_start();
	}

	public function __destruct ()
	{
		$this->logger->info("No more purpose for Session Manager. Farewell!");
		session_destroy();
	}

	public function dumpSession ()
	{
		var_dump($_SESSION);
	}

	public function getSession ()
	{
		return (object) $_SESSION;
	}
}

?>