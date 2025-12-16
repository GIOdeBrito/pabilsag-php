<?php

use GioPHP\Attributes\Route;

class ApiController
{
	#[Route(
		method: 'POST',
		path: '/public/api/v1/schema',
		description: 'Schema JSON test page.'
	)]
	public function schema ($req, $res): void
	{
		var_dump($req->body);
		$res->end(200);
	}

	#[Route(
		method: 'POST',
		path: '/public/api/v1/fileschema',
		description: 'Schema file upload endpoint.'
	)]
	public function schemaFile ($req, $res): void
	{
		$files = $req->files->annex;

		if(!is_array($files))
		{
			$res->status(200)->html("
				<h1>Not an array!</h1>
			");
		}

		foreach($files as $item)
		{
			?>
			<p>Name: <?= $item->name() ?></p>
			<p>Extension: <?= $item->extension() ?></p>
			<p>Type: <?= $item->contentType() ?></p>
			<p>Size: <?= $item->inKiloBytes() ?>KB</p>
			<br>
			<?php
		}

		$res->end(200);
	}

	#[Route(
		method: 'POST',
		path: '/public/api/v1/jsondump',
		description: 'JSON dump'
	)]
	public function jsonDump ($req, $res): void
	{
		var_dump($req->body->content);
	}
}

?>