<?php

namespace Pabilsag\Routing;

use Pabilsag\Http\{ Request, Response };
use Pabilsag\Services\{ Loader, Logger, MiddlewarePipeline, DIContainer };
use Pabilsag\Routing\ControllerRoute;
use Pabilsag\Enums\HttpMethod;

use function Pabilsag\Helpers\RouteAttributes\get_controller_schemas;

class Router
{
	private array $routes = [];
	private array $controllers = [];
	private string $notFoundPage = "";

	public function __construct (
		private DIContainer $container
	) {
		$this->routes = [
			HttpMethod::GET 		=> [],
			HttpMethod::POST 		=> [],
			HttpMethod::PUT 		=> [],
			HttpMethod::DELETE 		=> []
		];
	}

	public function addController (string $controller): void
	{
		$schemas = get_controller_schemas($controller);

		foreach($schemas as $schema):

			$controllerRoute = new ControllerRoute(
				method: $schema->method,
				path: $schema->path,
				description: $schema->description,
				middlewares: $schema->middlewares,
				controller: [$controller, $schema->functionName]
			);

			if($schema->isFallbackRoute)
			{
				$this->notFoundPage = $controllerRoute->path;
			}

			$this->routes[$schema->method][$schema->path] = $controllerRoute;

		endforeach;
	}

	public function call (Request $request): Response
	{
		$response = $this->container->make(Response::class);

		$requestMethod = $request->getMethod();
		$requestUri = $request->getUri();

		// Redirect to not found page
		if(!array_key_exists($requestUri, $this->routes[$requestMethod]))
		{
			return $response->redirect($this->notFoundPage);
		}

		$route = $this->routes[$requestMethod][$requestUri];

		// Self-contained route enqueued for the pipeline
		$routeQueued = function ($request, $response) use ($route): Response
		{
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

