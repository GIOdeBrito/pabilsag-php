<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Services\{ AssetManager };
use Pabilsag\View\ViewRenderer;
use Pabilsag\Enums\ContentType;
use Pabilsag\Interfaces\ResponseInterface;

final class RenderResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $view,
		private array|object $viewData,
		private string $layout,
		private string $viewDirectory,
		private string $layoutDirectory,
		private AssetManager $assetManager
	) {}

	public function getStatus (): int
	{
		return $this->status;
	}

	public function setStatus (int $code): void
	{
		$this->status = $code;
	}

	public function getContentType (): string
	{
		return ContentType::Html;
	}

	public function send (): void
	{
		$viewPath = $this->viewDirectory;

		if(empty($viewPath))
		{
			throw new \Exception("Views path was not set.");
		}

		$viewrenderer = new ViewRenderer();
		$viewFilePath = "{$viewPath}/{$this->view}.php";

		if(!file_exists($viewFilePath))
		{
			throw new \Exception("Could not find view file.");
		}

		// Extract params as proper variables
		extract($this->viewData);

		// Capture view's content
		$viewrenderer->beginCapture();

		include $viewFilePath;

		$viewrenderer->endCapture();

		$body = $viewrenderer->getHtml();

		// Extract the framework's parameters
		extract([
			'Pabilsag' => (object) [
				'assets' => $this->assetManager
			]
		]);

		// Load layout
		include "{$this->layoutDirectory}/{$this->layout}.php";
	}
}

