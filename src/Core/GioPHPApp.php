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
use GioPHP\Database\Db as Database;

if(!constant("GIOPHP_IS_DEBUG"))
{
	new ErrorHandler();
}

class GioPHPApp
{
	private ?DIContainer $container = NULL;

	public function __construct ()
	{
		$container = new DIContainer();
		$this->container = $container;

		$container->bind(Logger::class, fn() => new Logger());

		$container->singleton(Loader::class, fn() => new Loader());
		$container->singleton(ComponentRegistry::class, fn($container) => new ComponentRegistry(
			$container->make(Logger::class)
		));

		$container->singleton(Database::class, fn($container) => new Database(
			$container->make(Loader::class),
			$container->make(Logger::class)
		));

		$container->singleton(MiddlewarePipeline::class, fn($container) => new MiddlewarePipeline(
			$container->make(Logger::class)
		));

		$container->singleton(Router::class, fn($container) => new Router($container));
	}

	public function logger (): object
	{
		return $this->container->make(Logger::class);;
	}

	public function router (): object
	{
		return $this->container->make(Router::class);
	}

	public function loader (): object
	{
		return $this->container->make(Loader::class);
	}

	public function components (): object
	{
		return $this->container->make(ComponentRegistry::class);;
	}

	public function use (Middleware $middleware): void
	{
		$pipeline = $this->container->make(middlewarePipeline::class);
		$pipeline->add($middleware);
	}

	public function container ()
	{
		return $this->container;
	}

	public function run (): void
	{
		$router = $this->container->make(Router::class);
		$response = $router->call();
	}
}

?>