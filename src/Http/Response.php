<?php

namespace GioPHP\Http;

use GioPHP\Enums\{ResponseTypes, ContentType};
use GioPHP\Services\{Loader, Logger, ComponentRegistry};
use GioPHP\View\ViewRenderer;
use GioPHP\Interface\ResponseInterface;
use GioPHP\Http\Response\{FileResponse, HtmlResponse, JsonResponse, PlainResponse, RenderResponse};

class Response
{
	private Loader $loader;
	private Logger $logger;
	private ComponentRegistry $components;

	public function __construct (Loader $loader, Logger $logger, ComponentRegistry $components)
	{
		$this->loader = $loader;
		$this->logger = $logger;
		$this->components = $components;
	}

	public function render (int $status, string $view, string $layout = '_layout', array $params = []): void
	{
		$this->send(new RenderResponse(status: $status, view: $view, layout: $layout, viewData: $params));
	}

	public function html (int $status, string $html): void
	{
		$this->send(new HtmlResponse(status: $status, html: $html));
	}

	public function json (int $status, array|object $data): void
	{
		$this->send(new JsonResponse(status: $status, body: $data));
	}

	public function plain (int $status, string $text): void
	{
		$this->send(new PlainResponse(status: $status, text: $text));
	}

	public function file (int $status, string $path, string $type = ContentType::FileStream, string $filename = ''): void
	{
		$this->send(new FileResponse(status: $status, filepath: $path, contenttype: $type, filename: $filename));
	}

	public function end (int $status = 200): void
	{
		http_response_code($status);
		die();
	}

	public function redirect (string $url): void
	{
		http_response_code(301);
		header("Location: {$url}");
		die();
	}

	private function send ($response): void
	{
		http_response_code(intval($response->getStatus()));
		header('Content-Type: '.$response->getContentType());

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
					throw new \LogicException("Unknown response '{$response->getResponseType()}'.");
			}
		}
		catch(\Exception $ex)
		{
			$this->logger?->error($ex?->getMessage());
			http_response_code(500);
			echo "Internal Server Error";
		}

		die();
	}

	private function sendView (string $view, string $layout, array|object $params): void
	{
		$viewPath = $this->loader->getViewDirectory();

		if(empty($viewPath))
		{
			throw new \Exception("Views path was not set.");
		}

		$viewrenderer = new ViewRenderer($this->components);
		$viewFilePath = "{$viewPath}/{$view}.php";

		if(!file_exists($viewFilePath))
		{
			throw new \Exception("Could not find view file.");
		}

		// Capture view's content
		$viewrenderer->beginCapture();
		include $viewFilePath;
		$viewrenderer->endCapture();

		// Replace components if allowed
		if($this->components->isUsingComponents())
		{
			$viewrenderer->setComponentsForElements();
		}

		$body = $viewrenderer->getHtml();

		// Extract params as proper variables
		extract($params);

		// Load layout
		include "{$this->loader?->getLayoutDirectory()}/{$layout}.php";
	}

	private function sendJson (array|object $body): void
	{
		echo json_encode($body ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	private function sendHtml (string $html): void
	{
		echo $html ?? '';
	}

	private function sendFile (string $path, string $filename): void
	{
		if(!empty($filename))
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

?>