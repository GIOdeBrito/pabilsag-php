<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\{ResponseTypes, ContentType};
use Pabilsag\Interfaces\ResponseInterface;

final class JsonResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private array|object $body
	) {}

	public function getStatus(): int { return $this->status; }
	public function getBody(): array|object { return $this->body; }
	public function getText(): string { return ''; }
	public function getHTML(): string { return ''; }
	public function getFilePath(): string { return ''; }
	public function getFilename(): string { return ''; }
	public function getView(): string { return ''; }
	public function getViewData(): array|object { return []; }
	public function getLayout(): string { return ''; }
	public function getResponseType(): ResponseTypes { return ResponseTypes::JSON; }
	public function getContentType(): string { return ContentType::Json; }
}

?>