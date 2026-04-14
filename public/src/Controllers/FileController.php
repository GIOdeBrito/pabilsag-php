<?php

use Pabilsag\Http\Response;
use Pabilsag\Enums\ContentType;
use Pabilsag\Attributes\Route;

class FileController
{
	#[Route(
		method: 'GET',
		path: '/public/download',
		description: 'File download test.'
	)]
	public function fileDownload ($req, $res): Response
	{
		$path = ABSPATH.'/assets/hipopotamo.jpg';

		if(!file_exists($path))
		{
			$res->redirect('/public/404');
		}

		return $res->status(200)->file($path, filename: 'Hipopotamo.jpg');
	}

	#[Route(
		method: 'GET',
		path: '/public/image',
		description: 'Image display test.'
	)]
	public function fileDisplay ($req, $res): Response
	{
		$path = ABSPATH.'/assets/hipopotamo.jpg';

		if(!file_exists($path))
		{
			$res->redirect('/public/404');
		}

		return $res->status(200)->file($path, ContentType::ImageJpg);
	}

	#[Route(
		method: 'GET',
		path: '/public/image64',
		description: 'Outputs image as base64.'
	)]
	public function fileBase64 ($req, $res): Response
	{
		$path = ABSPATH.'/assets/hipopotamo.jpg';

		if(!file_exists($path))
		{
			$res->redirect('/public/404');
		}

		$content = base64_encode(file_get_contents($path));

		return $res->status(200)->plain($content);
	}
}

