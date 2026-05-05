<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Interfaces\ResponseInterface;

final class GenericResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $body,
		private string $contentType
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
		return $this->contentType;
	}

	public function send (): void
	{
		echo $this->body ?? '';
	}
}
