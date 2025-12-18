<?php

namespace GioPHP\Helpers\RouteAttributes;

function get_controller_schemas (string $controller): array
{
	$reflect = new \ReflectionClass($controller);
	$routeAttributes = [];

	foreach($reflect->getMethods() as $method):

		$attributes = $method->getAttributes();

		if(empty($attributes))
		{
			continue;
		}

		foreach($attributes as $attribute):

			$route = $attribute->newInstance();
			$route->functionName = $method->getName();

			array_push($routeAttributes, $route);

		endforeach;

	endforeach;

	return $routeAttributes;
}z

?>