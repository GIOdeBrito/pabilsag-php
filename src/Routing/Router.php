<?php

namespace GioPHP\Routing;

use GioPHP\Http\{ Request, Response };
use GioPHP\Services\{ Loader, Logger, ComponentRegistry, MiddlewarePipeline, DIContainer };
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

	public function call (Request $request): Response
	{
		$requestMethod = $request->getMethod();
		$requestUri = $request->getUri();

		if(!array_key_exists($requestUri, $this->routes[$requestMethod]))
		{
			$res->redirect($this->notFoundPage);
		}

		$route = $this->routes[$requestMethod][$requestUri];

		// Get the route schema i.e. the expected variables
		//$request->getSchema($route->schema);

		// Instantiates the route controller
		$controller = $this->container->make($route->getController());

		// Self-contained route enqueued for the pipeline
/*		$routeQueued = function () use ($request, $route, $controller) {

			$response = new Response(
				$this->container->make(Loader::class),
				$this->container->make(Logger::class),
				$this->container->make(ComponentRegistry::class)
			);

			$controller->{$route->getControllerMethod()}($request, $response);
		};*/

		// Add route-specific middlewares to the pipeline
		$this->container->make(MiddlewarePipeline::class)->addMultiple($route->middlewares);

		// Execute middleware pipeline before controller
		$response = $this->container->make(MiddlewarePipeline::class)->handle($request, $routeQueued);

		$controller->{$route->getControllerMethod()}($request, $response);

		return $response;
	}
}

?>