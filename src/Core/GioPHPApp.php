<?php

namespace GioPHP\Core;

define("GIOPHP_SRC_ROOT_PATH", __DIR__.'/..');
define("GIOPHP_IS_DEBUG", false);

require_once __DIR__.'/../Helpers/DateTime.php';
require_once __DIR__.'/../Helpers/RouteAttributes.php';
require_once __DIR__.'/../Helpers/Types.php';

use GioPHP\Routing\Router;
use GioPHP\Services\{Loader, Logger, ComponentRegistry};
use GioPHP\Error\ErrorHandler;
use GioPHP\Database\Db;

if(!constant("GIOPHP_IS_DEBUG"))
{
	ini_set('display_errors', '0');
	error_reporting(E_ALL);
	new ErrorHandler();
}

class GioPHPApp
{
	private ?Router $router = NULL;
	private ?Loader $loader = NULL;
	private ?Logger $logger = NULL;
	private ?ComponentRegistry $components = NULL;
	private ?Db $db = NULL;

	public function __construct ()
	{
		$this->logger = new Logger();
		$this->loader = new Loader();

		$this->components = new ComponentRegistry($this->logger);
		$this->db = new Db($this->loader, $this->logger);
		$this->router = new Router($this->loader, $this->logger, $this->db, $this->components);
	}

	public function logger (): object
	{
		return $this->logger;
	}

	public function router (): object
	{
		return $this->router;
	}

	public function loader (): object
	{
		return $this->loader;
	}

	public function components (): object
	{
		return $this->components;
	}

	public function run (): void
	{
		try
		{
			$response = $this->router->call();
			die();
		}
		catch(\ErrorException $ex)
		{
			$this->logger->error($ex->getMessage());
			die();
		}
	}
}

?>