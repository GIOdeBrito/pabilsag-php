<?php

namespace GioPHP\Services;

use GioPHP\Interface\Middleware;
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

	public function add (Middleware $mw): void
	{
		array_push($this->middlewares, $mw);
	}

	public function addMultiple (array $middlewares): void
	{
		$this->middlewares = array_merge($this->middlewares, $middlewares);
	}

	public function handle ($request, $response, $route): void
	{
		$queue = $this->middlewares;
		array_push($queue, $route);

		$runMiddleware = function () use (&$queue, $request, $response, &$runMiddleware) {

			$current = current($queue);

		    if($current === false) {
		        return; // no more middleware
		    }

		    next($queue); // advance pointer

			$next = function() use (&$runMiddleware) {
		        $runMiddleware();
			};

			if(gettype($current) === 'string')
			{
				// Call current middleware
				(new $current())->handle($request, $response, $next);
				return;
			}

			$current($request, $response, $next);
		};

		$runMiddleware();
	}

	private function runMiddleware ($middleware, $next): void
	{
		call_user_func($middleware, );
	}
}

?>