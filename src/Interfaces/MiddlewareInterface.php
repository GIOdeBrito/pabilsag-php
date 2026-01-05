<?php

namespace Pabilsag\Interfaces;

interface MiddlewareInterface
{
	public function handle($request, $response, callable $next);
}

?>