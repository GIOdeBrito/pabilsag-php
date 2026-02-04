<?php

namespace Pabilsag\Services;

use Pabilsag\Interfaces\MiddlewareInterface;
use Pabilsag\Services\DIContainer;
use Pabilsag\Http\{ Request, Response };

class MiddlewarePipeline
{
	private array $middlewares = [];
	private Logger $logger;

	public function __construct (
		private DIContainer $container
	) {
		$this->logger = $container->make(Logger::class);
	}

	public function add (string $middlewareClass): void
	{
		$this->addMultiple([ $middlewareClass ]);
	}

	public function addMultiple (array $middlewares = []): void
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
		return (
			(is_string($target) && class_exists($target) && is_a($target, MiddlewareInterface::class, true))
			|| ($target instanceof Pabilsag\Interfaces\MiddlewareInterface)
		);
	}
}

?>