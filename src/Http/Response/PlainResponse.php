<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\{ResponseTypes, ContentType};
use Pabilsag\Interfaces\ResponseInterface;

final class PlainResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $text
	) {}

	public function getStatus(): int { return $this->status; }
	public function getBody(): array|object { return ''; }
	public function getText(): string { return $this->text; }
	public function getHTML(): string { return ''; }
	public function getFilePath(): string { return ''; }
	public function getFilename(): string { return ''; }
	public function getView(): string { return ''; }
	public function getViewData(): array|object { return []; }
	public function getLayout(): string { return ''; }
	public function getResponseType(): ResponseTypes { return ResponseTypes::PLAINTEXT; }
	public function getContentType(): string { return ContentType::PlainText; }
}

?>