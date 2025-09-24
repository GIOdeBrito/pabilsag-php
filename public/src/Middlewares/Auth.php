<?php

use GioPHP\Interface\Middleware;

class authMiddleware implements Middleware
{
	public function handle ($req, $res, $next)
	{
		if(!isset($req->body->authBasic))
		{
			error_log("Request does not contain basic auth");
		}

		$next();
	}
}

?>