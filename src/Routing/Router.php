<?php

namespace GioPHP\Routing;

use GioPHP\Http\{Request, Response};
use GioPHP\Services\{Loader, Logger, ComponentRegistry, MiddlewarePipeline, DIContainer};
use GioPHP\Database\Db;
use GioPHP\Routing\ControllerRoute;
use GioPHP\Enums\HttpMethod;

use function GioPHP\Helpers\getControllerSchemas;

class Router
{
	private array $routes = [];
	private array $controllers = [];

	private string $notFoundPage = "";

	private DIContainer $container;

	public function __construct (DIContainer $container)
	{
		$this->routes = [
			HttpMethod::GET 		=> [],
			HttpMethod::POST 		=> [],
			HttpMethod::PUT 		=> [],
			HttpMethod::DELETE 		=> []
		];

		$this->container = $container;
	}

	public function addController (string $controller): void
	{
		$schemas = getControllerSchemas($controller);

		foreach($schemas as $schema):

			if(!$this->methodExists($schema->method))
			{
				throw new \Exception("Method: '{$schema->method}' is not valid.");
			}

			$controllerRoute = new ControllerRoute();
			$controllerRoute->method = $schema->method;
			$controllerRoute->path = $schema->path;
			$controllerRoute->description = $schema->description;
			$controllerRoute->schema = $schema->schema;
			$controllerRoute->middlewares = $schema->middlewares;
			$controllerRoute->controller = [$controller, $schema->functionName];

			if($schema->isFallbackRoute)
			{
				$this->notFoundPage = $controllerRoute->path;
			}

			$this->routes[$schema->method][$schema->path] = $controllerRoute;

		endforeach;
	}

	public function call (): void
	{
		$req = new Request($this->container->make(Logger::class));
		$res = new Response(
			$this->container->make(Loader::class),
			$this->container->make(Logger::class),
			$this->container->make(ComponentRegistry::class)
		);

		$requestMethod = $req->method;

		if(!$this->methodExists($requestMethod))
		{
			$res->redirect("/");
		}

		$requestPath = $req->path;

		if(!array_key_exists($requestPath, $this->routes[$requestMethod]))
		{
			$res->redirect($this->notFoundPage);
		}

		$route = $this->routes[$requestMethod][$requestPath];

		// Get the route schema i.e. the expected variables
		$req->getSchema($route->schema);

		// Instantiates the route controller
		$controller = $this->container->make($route->getController());

		// Self-contained route enqueued for the pipeline
		$routeQueued = function () use ($req, $res, $route, $controller) {
			$controller->{$route->getControllerMethod()}($req, $res);
		};

		// Add route-specific middlewares to the pipeline
		$this->container->make(middlewarePipeline::class)->addMultiple($route->middlewares);

		// Middleware pipeline prepare and execute
		$this->container->make(middlewarePipeline::class)->handle($req, $res, $routeQueued);
	}

	// Checks whether a method exists in this router
	private function methodExists (string $method): bool
	{
		if(array_key_exists($method, $this->routes))
		{
			return true;
		}

		return false;
	}
}

?>