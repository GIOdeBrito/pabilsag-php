<?php

namespace GioPHP\Services;

class Loader
{
	private string $views = "";
	private string $layout = "";

	private ?string $dbLogin;
	private ?string $dbPwd;

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
}

?>