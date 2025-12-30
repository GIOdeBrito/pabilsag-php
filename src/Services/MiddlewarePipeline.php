<?php

namespace GioPHP\Services;

use GioPHP\Interfaces\MiddlewareInterface;
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

	public function add (string $middlewareClass): void
	{
		array_push($this->middlewares, $middlewareClass);
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
		if(is_string($target) && is_a($target, MiddlewareInterface::class, true))
		{
			return true;
		}

		if($target instanceof GioPHP\Interfaces\MiddlewareInterface)
		{
			return true;
		}

		return false;
	}
}

?>