<?php

namespace Pabilsag\Http;

use Pabilsag\Enums\{ ResponseTypes, ContentType };
use Pabilsag\Services\{ Loader, Logger, AssetManager };
use Pabilsag\View\ViewRenderer;
use Pabilsag\Interfaces\ResponseInterface;
use Pabilsag\Http\Response\{ FileResponse, HtmlResponse, JsonResponse, PlainResponse, RenderResponse };

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
		$this->prepared = new RenderResponse(status: $this->code, view: $view, layout: $layout, viewData: $params);
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
		$this->prepared = new FileResponse(status: $this->code, filepath: $path, contenttype: $type, filename: $filename);
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
		header('Content-Type: '.$response->getContentType());

		// TODO: Refactor. Implement send function in each response type...

		try
		{
			switch($response->getResponseType())
			{
				case ResponseTypes::VIEW:
					$this->sendView($response->getView(), $response->getLayout(), $response->getViewData());
					break;
				case ResponseTypes::JSON:
					$this->sendJson($response->getBody());
					break;
				case ResponseTypes::HTML:
					$this->sendHtml($response->getHTML());
					break;
				case ResponseTypes::FILE:
					$this->sendFile($response->getFilePath(), $response->getFilename());
					break;
				case ResponseTypes::PLAINTEXT:
					$this->sendPlain($response->getText());
					break;
				default:
					throw new \LogicException("Unknown response type '{$response->getResponseType()}'");
			}
		}
		catch(\Exception $ex)
		{
			$this->logger?->error($ex?->getMessage());
			http_response_code(500);
			echo "Internal Server Error";
		}
	}

	private function sendView (string $view, string $layout, array|object $params): void
	{
		$viewPath = $this->loader->getViewDirectory();

		if(empty($viewPath))
		{
			throw new \Exception("Views path was not set.");
		}

		$viewrenderer = new ViewRenderer();
		$viewFilePath = "{$viewPath}/{$view}.php";

		if(!file_exists($viewFilePath))
		{
			throw new \Exception("Could not find view file.");
		}

		// Extract params as proper variables
		extract($params);

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
		include "{$this->loader?->getLayoutDirectory()}/{$layout}.php";
	}

	private function sendJson (array|object $body): void
	{
		// NOTE: No pretty print for JSON
		echo json_encode($body ?? [], JSON_UNESCAPED_UNICODE);
	}

	private function sendHtml (string $html): void
	{
		echo $html ?? '';
	}

	private function sendFile (string $path, string $filename): void
	{
		if(!empty($filename ?? ''))
		{
			header("Content-Disposition: attachment; filename=\"{$filename}\"");
		}

		readfile($path);
	}

	private function sendPlain (string $text): void
	{
		echo $text ?? "";
	}
}

