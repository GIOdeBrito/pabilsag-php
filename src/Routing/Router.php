<?php

namespace GioPHP\Routing;

use GioPHP\Http\{Request, Response};
use GioPHP\Services\{Loader, Logger, ComponentRegistry};
use GioPHP\Database\Db;
use GioPHP\Routing\ControllerRoute;
use GioPHP\Services\MiddlewarePipeline;

use function GioPHP\Helpers\getControllerSchemas;

class Router
{
	private array $routes = [];
	private array $controllers = [];

	private string $notFoundPage = "";

	private Loader $loader;
	private Logger $logger;
	private Db $db;
	private ComponentRegistry $components;
	private MiddlewarePipeline $middlewarePipeline;

	public function __construct (Loader $loader, Logger $logger, Db $db, ComponentRegistry $components, middlewarePipeline $middlewarePipeline)
	{
		$this->routes = [
			'GET' 		=> [],
			'POST' 		=> [],
			'PUT' 		=> [],
			'DELETE' 	=> []
		];

		$this->loader = $loader;
		$this->logger = $logger;
		$this->db = $db;
		$this->components = $components;
		$this->middlewarePipeline = $middlewarePipeline;
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
			$this->middlewarePipeline->addMultiple($schema->middlewares);
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
		$req = new Request($this->logger);
		$res = new Response($this->loader, $this->logger, $this->components);

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

		$req->getSchema($route->schema);

		$controller = $this->controllerInstantiator($route->getController());

		// Self contained route enqueued for the pipeline
		$routeQueued = function () use ($req, $res, $route, $controller) {
			$controller->{$route->getControllerMethod()}($req, $res);
		};

		// Middleware pipeline prepare and execute
		$this->middlewarePipeline->handle($req, $res, $routeQueued);

		//$controller->{$route->getControllerMethod()}($req, $res);
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

	private function controllerInstantiator (string $className): object
	{
		$reflection = new \ReflectionClass($className);
		$constructor = $reflection->getConstructor();

		if(is_null($constructor))
		{
			return new $className();
		}

		// Available parameters for the controller's constructor
		$possibleParameters = [
			'database' 		=> $this->db,
			'logger' 		=> $this->logger,
			'components' 	=> $this->components
		];

		$controllerParams = [];

		foreach($constructor->getParameters() as $param):

			$paramName = $param->getName();

			if(!array_key_exists($paramName, $possibleParameters))
			{
				continue;
			}

			$controllerParams[$paramName] = $possibleParameters[$paramName];

		endforeach;

		return new $className(...$controllerParams);
	}
}

?>