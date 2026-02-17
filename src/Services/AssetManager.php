<?php

namespace Pabilsag\Services;

final class AssetManager
{
	private array $scripts = [];
	private array $stylesheets = [];

	public function __construct (
		private Logger $logger
	) {}

	public function hasFilesToEnqueue (): bool
	{
		return (count($this->scripts) > 0 || count($this->stylesheets) > 0);
	}

	public function addScript (string $path, string $version = "1.0.0", bool $isModule = false): void
	{
		$this->scripts[] = [
			'path' => $path,
			'module' => $isModule,
			'version' => $version
		];
	}

	public function addStyleSheet (string $path, string $version = "1.0.0"): void
	{
		$this->stylesheets[] = [
			'path' => $path,
			'version' => $version
		];
	}

	public function renderStyleSheets (): void
	{
		foreach($this->stylesheets as $item):

			$path = $item['path'];
			$version = $item['version'];

			?>

			<link rel="stylesheet" href="<?= $item['path'] ?>?v=<?= $version ?>">

			<?
		endforeach;
	}

	public function renderScripts (): void
	{
		foreach($this->scripts as $item):

			$path = $item['path'];
			$version = $item['version'];

			?>

			<script
				type="<?= $item['isModule'] === true ? "module" : "text/javascript" ?>"
				src="<?= $path ?>?v=<?= $version ?>">
			</script>

			<?
		endforeach;
	}
}

