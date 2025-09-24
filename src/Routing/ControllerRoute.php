<?php

namespace GioPHP\Routing;

class ControllerRoute
{
	public string $method;
	public string $path;
	public array $schema;
	public string $description;
	public array $controller;
	public array $middlewares;

	public function getController (): string
	{
		return $this->controller[0];
	}

	public function getControllerMethod (): string
	{
		return $this->controller[1];
	}
}

?>