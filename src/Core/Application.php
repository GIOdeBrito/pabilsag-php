<?php

namespace Pabilsag\Core;

define("Pabilsag_SRC_ROOT_PATH", __DIR__.'/..');

require Pabilsag_SRC_ROOT_PATH.'/Helpers/String.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/Object.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/DateTime.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/RouteAttributes.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/Polyfill.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/Json.php';
require Pabilsag_SRC_ROOT_PATH.'/Helpers/Http.php';

use Pabilsag\Http\Request;
use Pabilsag\Routing\Router;
use Pabilsag\Services\{
	Loader,
	Logger,
	MiddlewarePipeline,
	DIContainer
};
use Pabilsag\Infrastructure\ConnectionFactory;
use Pabilsag\Infrastructure\Serialization\{ JsonDeserializer, JsonSerializer };
use Pabilsag\Web\CurlClient;
use Pabilsag\Interfaces\MiddlewareInterface;
use Pabilsag\Error\ErrorHandler;
use Pabilsag\Database\Database;

use function Pabilsag\Helpers\Http\get_request_headers;

class Application
{
	private DIContainer $container;
	private ErrorHandler $errHandler;

	public function __construct ()
	{
		$container = new DIContainer();
		$this->container = $container;

		$this->errHandler = new ErrorHandler($container->make(Logger::class));

		$container->bind(Logger::class, fn() => new Logger());
		$container->bind(CurlClient::class, fn() => new CurlClient());

		$container->bind(Request::class, fn() => new Request(
			get_request_headers(),
			$_GET,
			$_POST,
			$_COOKIE,
			file_get_contents('php://input')
		));

		$container->bind(Database::class, fn($container) => new Database(
			$container->make(ConnectionFactory::class)
		));

		$container->singleton(Loader::class, fn() => new Loader());

		$container->singleton(ConnectionFactory::class, fn($container) => new ConnectionFactory(
			$container->make(Loader::class),
			$container->make(Logger::class)
		));

		$container->bind(JsonSerializer::class, fn($container) => new JsonSerializer(
			$container->make(Logger::class)
		));

		$container->bind(JsonDeserializer::class, fn($container) => new JsonDeserializer(
			$container->make(Logger::class)
		));

		$container->singleton(Router::class, fn($container) => new Router($container));

		$container->singleton(MiddlewarePipeline::class, fn($container) => new MiddlewarePipeline($container));

		$container->singleton(Router::class, fn($container) => new Router($container));
	}

	public function router (): Router
	{
		return $this->container->make(Router::class);
	}

	public function loader (): Loader
	{
		return $this->container->make(Loader::class);
	}

	public function middleware (): MiddlewarePipeline
	{
		return $this->container->make(MiddlewarePipeline::class);
	}

	public function container (): DIContainer
	{
		return $this->container;
	}

	public function error (): ErrorHandler
	{
		return $this->errHandler;
	}

	public function run (): void
	{
		$request = $this->container->make(Request::class);

		$router = $this->container->make(Router::class);
		$response = $router->call($request);

		// Send stuff to browser
		$response->send();

		die();
	}
}

?>