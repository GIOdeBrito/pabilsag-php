<?php

namespace GioPHP\Services;

use GioPHP\Interfaces\Middleware;
use GioPHP\Services\Logger;
use GioPHP\Http\{Request, Response};

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

	public function handle ($request, $response, $route): void
	{
		$queue = $this->middlewares;
		array_push($queue, $route);

		$runMiddleware = function () use (&$queue, $request, $response, &$runMiddleware) {

			$current = current($queue);

			if(is_null($current) OR $current === false)
			{
		        return;
		    }

			// Advance array pointer
		    next($queue);

			$next = function() use (&$runMiddleware) {
		        $runMiddleware();
			};

			// Instantiate if string is a class
			if($this->isMiddlewareInstance($current))
			{
				// Call current middleware
				(new $current())->handle($request, $response, $next);
				return;
			}

			// Spawn the route
			$current($request, $response, $next);
		};

		$runMiddleware();
	}

	private function isMiddlewareInstance (string|object $target): bool
	{
		if(is_string($target) AND is_a($target, 'GioPHP\Interfaces\Middleware', true))
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