<?php

use Pabilsag\Services\Logger;

class SessionManager
{
	public function __construct (
		private Logger $logger
	) {
		$logger->info("Session Manager was start'd! :D");
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