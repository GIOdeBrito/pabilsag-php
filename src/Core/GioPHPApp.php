<?php

namespace GioPHP\Core;

define("GIOPHP_SRC_ROOT_PATH", __DIR__.'/..');
define("GIOPHP_IS_DEBUG", true);

require_once __DIR__.'/../Helpers/DateTime.php';
require_once __DIR__.'/../Helpers/RouteAttributes.php';
require_once __DIR__.'/../Helpers/Types.php';

use GioPHP\Routing\Router;
use GioPHP\Services\{
	Loader,
	Logger,
	ComponentRegistry,
	MiddlewarePipeline,
	DIContainer
};
use GioPHP\Interfaces\Middleware;
use GioPHP\Error\ErrorHandler;
use GioPHP\Database\Db;

if(!constant("GIOPHP_IS_DEBUG"))
{
	new ErrorHandler();
}

class GioPHPApp
{
	private ?DIContainer $container = NULL;
	private ?Router $router = NULL;
	private ?Loader $loader = NULL;
	private ?Logger $logger = NULL;
	private ?ComponentRegistry $components = NULL;
	private ?Db $db = NULL;
	private ?MiddlewarePipeline $middlewarePipeline = NULL;

	public function __construct ()
	{
		$container = = new DIContainer();
		$this->container $container;

		$container->bind(Loader::class, fn() => new Logger());
		$container->bind(Logger::class, fn() => new Loader());

		$this->components = new ComponentRegistry($this->logger);
		$this->db = new Db($this->loader, $this->logger);
		$this->middlewarePipeline = new MiddlewarePipeline($this->logger);

		$this->router = new Router($this->loader, $this->logger, $this->db, $this->components, $this->middlewarePipeline);
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

	public function use (Middleware $middleware): void
	{
		$this->middlewarePipeline->add($middleware);
	}

	public function container ()
	{
		return
	}

	public function run (): void
	{
		try
		{
			$response = $this->router->call();
		}
		catch(\ErrorException $ex)
		{
			$this->logger->error($ex->getMessage());
		}
		finally
		{
			die();
		}
	}
}

?>