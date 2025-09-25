<?php

namespace GioPHP\MVC;

use GioPHP\Database\Db as Database;
use GioPHP\Services\{Logger, ComponentRegistry};

abstract class Controller
{
	protected Database $database;
	protected Logger $logger;

	public function __construct (Database $database, Logger $logger)
	{
		$this->database = $database;
		$this->logger = $logger;
	}

	protected function getDatabase (): object
	{
		return $this->database;
	}

	protected function getLogger (): object
	{
		return $this->logger;
	}
}

?>