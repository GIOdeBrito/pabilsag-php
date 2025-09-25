<?php

namespace GioPHP\Interfaces;

interface Middleware
{
	public function handle($request, $response, callable $next);
}

?>