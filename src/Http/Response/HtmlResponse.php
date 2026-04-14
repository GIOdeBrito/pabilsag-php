<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\ContentType;
use Pabilsag\Interfaces\ResponseInterface;

final class HtmlResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private string $html
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
		return ContentType::Html;
	}

	public function send (): void
	{
		echo $this->html ?? '';
	}
}

