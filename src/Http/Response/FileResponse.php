<?php

namespace GioPHP\Http\Response;

use GioPHP\Enums\{ResponseTypes, ContentType};
use GioPHP\Interface\ResponseInterface;

final class FileResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $filepath,
		private string $contenttype = ContentType::FileStream,
		private ?string $filename = '',
	) {}

	public function getStatus(): int { return $this->status; }
	public function getBody(): array|object { return ''; }
	public function getText(): string { return ''; }
	public function getHTML(): string { return ''; }
	public function getFilePath(): string { return $this->filepath; }
	public function getFilename(): string { return $this->filename; }
	public function getView(): string { return ''; }
	public function getViewData(): array|object { return []; }
	public function getLayout(): string { return ''; }
	public function getResponseType(): ResponseTypes { return ResponseTypes::FILE; }
	public function getContentType(): string { return $this->contenttype; }
}

?>