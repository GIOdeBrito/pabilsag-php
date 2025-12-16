<?php

namespace GioPHP\Routing;

use GioPHP\Http\{ Request, Response };
use GioPHP\Services\{ Loader, Logger, ComponentRegistry, MiddlewarePipeline, DIContainer };
use GioPHP\Routing\ControllerRoute;
use GioPHP\Enums\HttpMethod;

use function GioPHP\Helpers\RouteAttributes\get_controller_schemas;

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
		$schemas = get_controller_schemas($controller);

		foreach($schemas as $schema):

			$controllerRoute = new ControllerRoute();
			$controllerRoute->method = $schema->method;
			$controllerRoute->path = $schema->path;
			$controllerRoute->description = $schema->description;
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
		$response = new Response(
			$this->container->make(Loader::class),
			$this->container->make(Logger::class),
			$this->container->make(ComponentRegistry::class)
		);
		
		$requestMethod = $request->getMethod();
		$requestUri = $request->getUri();
		
		// Redirect to not found page
		if(!array_key_exists($requestUri, $this->routes[$requestMethod]))
		{
			return $response->redirect($this->notFoundPage);
		}

		$route = $this->routes[$requestMethod][$requestUri];

		// Self-contained route enqueued for the pipeline
		$routeQueued = function ($request, $response) use ($route): Response {
			
			// Instantiates the route controller
			$controller = $this->container->make($route->getController());
			
			// Calls controller selected method
			$controllerResponse = $controller->{$route->getControllerMethod()}($request, $response);
			
			return $controllerResponse;
		};

		// Add route-specific middlewares to the pipeline
		$this->container->make(MiddlewarePipeline::class)->addMultiple($route->middlewares);

		// Execute middleware pipeline before controller
		$middlewareResponse = $this->container->make(MiddlewarePipeline::class)->handle($request, $response, $routeQueued);

		return $middlewareResponse;
	}
}

?>