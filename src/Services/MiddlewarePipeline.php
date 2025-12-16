<?php

namespace GioPHP\Services;

use GioPHP\Interfaces\Middleware;
use GioPHP\Services\Logger;
use GioPHP\Http\{ Request, Response };

class MiddlewarePipeline
{
	private array $middlewares = [];
	private Logger $logger;

	public function __construct (Logger $logger)
	{
		$this->logger = $logger;
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

		$dispatcher = function () use (
			&$dispatcher,
			$queue,
			&$index,
			$request,
			$response,
			$route
		) {
			// Still have middleware to run
			if (isset($queue[$index])) {
				$middleware = $queue[$index++];

				return (new $middleware())->handle(
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