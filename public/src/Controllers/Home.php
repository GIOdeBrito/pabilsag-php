<?php

use Pabilsag\Attributes\Route;
use Pabilsag\Http\Response;
use Pabilsag\Database\Database;
use Pabilsag\Services\AssetManager;

class Home
{
	public function __construct (
		private Database $database,
		private AssetManager $assets
	) {}

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

		$this->assets->addStyleSheet("/public/assets/main.css");

		return $res->status(200)->render('Home', '_layout', $viewData);
	}

	#[Route(
		method: 'GET',
		path: '/public/utf8',
		description: 'Home page'
	)]
	public function utf8View ($req, $res): Response
	{
		$viewData = [
			'title' => 'UTF-8'
		];

		return $res->status(200)->render('Utf8', '_layout', $viewData);
	}

	#[Route(
		method: 'GET',
		path: '/public/database',
		description: 'Database test page'
	)]
	public function db ($req, $res): Response
	{
		$db = $this->db;

		$db->connect('sqlite_db');
		$queryResult = $db->query("SELECT * FROM USERS");

		return $res->status(200)->json($queryResult);
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
		return $res->status(404)->html(
			<<<HTML
				<h1>Not Found</h1>
			HTML
		);
	}
}

?>