<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\{ResponseTypes, ContentType};
use Pabilsag\Interfaces\ResponseInterface;

final class RenderResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $view,
		private array|object $viewData,
		private string $layout
	) {}

	public function getStatus(): int { return $this->status; }
	public function getBody(): array|object { return ''; }
	public function getText(): string { return ''; }
	public function getHTML(): string { return ''; }
	public function getFilePath(): string { return ''; }
	public function getFilename(): string { return ''; }
	public function getView(): string { return $this->view; }
	public function getViewData(): array|object { return $this->viewData; }
	public function getLayout(): string { return $this->layout; }
	public function getResponseType(): ResponseTypes { return ResponseTypes::VIEW; }
	public function getContentType(): string { return ContentType::Html; }
}

?>