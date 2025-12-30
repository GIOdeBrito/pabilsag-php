<?php

namespace GioPHP\Interfaces;

interface MiddlewareInterface
{
	public function handle($request, $response, callable $next);
}

?>