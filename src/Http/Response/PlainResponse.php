<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\ContentType;
use Pabilsag\Interfaces\ResponseInterface;

final class PlainResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $text
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
		return ContentType::PlainText;
	}

	public function send (): void
	{
		echo $this->text ?? '';
	}
}

