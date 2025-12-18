<?php

namespace GioPHP\Routing;

class ControllerRoute
{
	public function __construct (
		public string $method,
		public string $path,
		public string $description,
		public array $controller,
		public array $middlewares
	) {}

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