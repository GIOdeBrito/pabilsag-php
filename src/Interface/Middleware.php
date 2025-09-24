<?php

namespace GioPHP\Interface;

interface Middleware
{
	public function handle($request, $response, callable $next);
}

?>