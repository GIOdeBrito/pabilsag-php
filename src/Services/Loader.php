<?php

namespace GioPHP\Services;

class Loader
{
	private string $views = "";
	private string $layout = "";
	
	private array $connectionMetadata = [];

	public function __construct ()
	{
		$this->layout = constant("GIOPHP_SRC_ROOT_PATH")."/Template/";
	}

	public function setViewDirectory (string $path): void
	{
		$this->views = $path;
	}

	public function setLayoutDirectory (string $path): void
	{
		$this->layout = $path;
	}

	public function getViewDirectory (): string
	{
		return $this->views;
	}

	public function getLayoutDirectory (): string
	{
		return $this->layout;
	}
	
	public function importConnectionMetadata (string $path): void
	{
		if(!file_exists($path))
		{
			throw new \Exception("Connection metadata file at '{$path}' does not exist");
		}
		
		$this->connectionMetadata = require $path;
	}
	
	public function getConnectionMetadata (): array
	{
		return $this->connectionMetadata;
	}
}

?>