<?php

namespace Pabilsag\Http;

use Pabilsag\Enums\ContentType;
use Pabilsag\Services\{ Loader, Logger, AssetManager };
use Pabilsag\Interfaces\ResponseInterface;
use Pabilsag\Http\Response\{ FileResponse, GenericResponse, HtmlResponse, JsonResponse, PlainResponse, RenderResponse };

class Response
{
	private int $code = 200;
	private ResponseInterface $prepared;

	public function __construct (
		private Loader $loader,
		private Logger $logger,
		private AssetManager $assetManager
	) {}

	public function status (int $code = 200): Response
	{
		$this->code = $code;
		return $this;
	}

	public function render (string $view, string $layout = '_layout', array $params = []): Response
	{
		$this->prepared = new RenderResponse(
			status: $this->code,
			view: $view,
			layout: $layout,
			viewData: $params,
			viewDirectory: $this->loader->getViewDirectory(),
			layoutDirectory: $this->loader->getLayoutDirectory(),
			assetManager: $this->assetManager
		);
		return $this;
	}

	public function html (string $html): Response
	{
		$this->prepared = new HtmlResponse(status: $this->code, html: $html);
		return $this;
	}

	public function json (array|object $data): Response
	{
		$this->prepared = new JsonResponse(status: $this->code, body: $data);
		return $this;
	}

	public function plain (string $text): Response
	{
		$this->prepared = new PlainResponse(status: $this->code, text: $text);
		return $this;
	}

	public function file (string $path, string $type = ContentType::FileStream, string $filename = ''): Response
	{
		$this->prepared = new FileResponse(
			status: $this->code,
			filepath: $path,
			contenttype: $type,
			filename: $filename
		);
		return $this;
	}

	public function generic (string $body, string $contentType): Response
	{
		$this->prepared = new GenericResponse(
			status: $this->code,
			body: $body,
			contentType: $contentType
		);
		return $this;
	}

	public function end (int $status = 200): Response
	{
		$this->prepared = new HtmlResponse(status: $this->status, html: '');
		return $this;
	}

	public function redirect (string $url): void
	{
		http_response_code(301);
		header("Location: {$url}");
		die();
	}

	public function send (): void
	{
		$response = $this->prepared;

		http_response_code(intval($response->getStatus()));
		header('Content-Type: ' . $response->getContentType());

		$response->send();
	}
}

