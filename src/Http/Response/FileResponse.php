<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\ContentType;
use Pabilsag\Interfaces\ResponseInterface;

final class FileResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $filepath,
		private string $contenttype = ContentType::FileStream,
		private ?string $filename = '',
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
		return $this->contenttype;
	}

	public function send (): void
	{
		if(!empty($this->filename))
		{
			header("Content-Disposition: attachment; filename=\"{$this->filename}\"");
		}

		readfile($this->filepath);
	}
}

