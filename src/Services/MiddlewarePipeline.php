<?php

namespace GioPHP\Services;

use GioPHP\Interfaces\Middleware;
use GioPHP\Services\DIContainer;
use GioPHP\Http\{ Request, Response };

class MiddlewarePipeline
{
	private array $middlewares = [];
	private DIContainer $container;
	private Logger $logger;

	public function __construct (DIContainer $container)
	{
		$this->logger = $container->make(Logger::class);
		$this->container = $container;
	}

	public function add (Middleware $middleware): void
	{
		array_push($this->middlewares, $middleware);
	}

	public function addMultiple (array $middlewares): void
	{
		// Check if the middlewares are properly of instance
		$collection = array_filter($middlewares, fn($item) => $this->isMiddlewareInstance($item));

		$this->middlewares = array_merge($this->middlewares, $collection);
	}

	public function handle (Request $request, Response $response, callable $route): Response
	{
		$queue = $this->middlewares;
		$index = 0;
		
		$container = $this->container;

		$dispatcher = function () use (
			&$dispatcher,
			$queue,
			&$index,
			$request,
			$response,
			$route,
			$container
		) {
			// Still have middleware to run
			if (isset($queue[$index])) {
				$middleware = $queue[$index++];

				return $this->container->make($middleware)->handle(
					$request,
					$response,
					fn ($request, $response) => $dispatcher()
				);
			}

			// If no middleware left, proceeds for main route
			return $route($request, $response);
		};
		
		$finalResponse = $dispatcher();

		return $finalResponse;
	}

	private function isMiddlewareInstance (string|object $target): bool
	{
		if(is_string($target) && is_a($target, 'GioPHP\Interfaces\Middleware', true))
		{
			return true;
		}

		if($target instanceof GioPHP\Interfaces\Middleware)
		{
			return true;
		}

		return false;
	}
}

?>