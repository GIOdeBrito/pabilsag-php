<?php

use GioPHP\MVC\Controller;

use GioPHP\Attributes\Route;

include __DIR__.'/../Middlewares/Auth.php';

class MiddlewareController extends Controller
{
	#[Route(
		method: 'GET',
		path: '/public/mdw',
		middlewares: [ authMiddleware::class ],
		description: 'Testing middleware feature.'
	)]
	public function index ($req, $res): void
	{
		$res->status(200)->plain("Verify the log output.");
	}
}

?>