<?php

use GioPHP\Attributes\Route;
use GioPHP\Http\Response;

class Home
{
	#[Route(
		method: 'GET',
		path: '/public/',
		description: 'Home page'
	)]
	public function index ($req, $res): Response
	{
		$viewData = [
			'title' => 'Home'
		];
		
		return $res->status(200)->render('Home', '_layout', $viewData);
	}
	
	#[Route(
		method: 'GET',
		path: '/public/database',
		description: 'Database test page'
	)]
	public function db ($req, $res): Response
	{
		$viewData = [
			'title' => 'Db'
		];
		
		$db = $this->getDatabase();
		$db->open();
		//$db->exec("INSERT INTO USERS VALUES (:idd, :name, :num)", [ 'idd' => 2, 'name' => 'BRUNO', 'num' => 123 ]);
		$res = $db->query("SELECT * FROM USERS");
		
		var_dump($res);
		die();
		
		$res->status(200)->render('Home', '_layout', $viewData);
	}
	
	#[Route(
		method: 'GET',
		path: '/public/upload',
		description: 'File upload page'
	)]
	public function indexUpload ($req, $res): Response
	{
		return $res->status(200)->render('FileUpload', '_layout', [ 'title' => 'Upload' ]);
	}
	
	#[Route(
		method: 'GET',
		path: '/public/404',
		description: 'Default not found page',
		isFallbackRoute: true
	)]
	public function notFound ($req, $res): Response
	{	
		return $res->status(404)->html("<h1>Not Found</h1>");
	}
}

?>