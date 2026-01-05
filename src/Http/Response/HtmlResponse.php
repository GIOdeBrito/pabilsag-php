<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\{ResponseTypes, ContentType};
use Pabilsag\Interfaces\ResponseInterface;

final class HtmlResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $html
	) {}

	public function getStatus(): int { return $this->status; }
	public function getBody(): array|object { return ''; }
	public function getText(): string { return ''; }
	public function getHTML(): string { return $this->html; }
	public function getFilePath(): string { return ''; }
	public function getFilename(): string { return ''; }
	public function getView(): string { return ''; }
	public function getViewData(): array|object { return []; }
	public function getLayout(): string { return ''; }
	public function getResponseType(): ResponseTypes { return ResponseTypes::HTML; }
	public function getContentType(): string { return ContentType::Html; }
}

?>