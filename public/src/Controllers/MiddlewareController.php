<?php

use GioPHP\Attributes\Route;
use GioPHP\Http\Response;

include __DIR__.'/../Middlewares/Auth.php';

class MiddlewareController
{
	#[Route(
		method: 'GET',
		path: '/public/auth',
		description: 'Testing middleware feature.'
	)]
	public function index ($req, $res): Response
	{
		return $res->status(200)->render('Auth', '_layout');
	}
	
	#[Route(
		method: 'POST',
		path: '/public/api/v1/auth',
		middlewares: [ AuthMiddleware::class ],
		description: 'Testing middleware feature.'
	)]
	public function authTest ($req, $res): Response
	{
		return $res->status(200)->plain("Verify the log output.");
	}
}

?>