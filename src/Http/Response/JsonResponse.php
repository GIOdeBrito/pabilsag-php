<?php

namespace Pabilsag\Http\Response;

use Pabilsag\Enums\ContentType;
use Pabilsag\Interfaces\ResponseInterface;

final class JsonResponse implements ResponseInterface
{
	public function __construct(
		private int $status,
		private array|object $body
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
		return ContentType::Json;
	}

	public function send (): void
	{
		// NOTE: No pretty print for JSON
		echo json_encode($this->body ?? [], JSON_UNESCAPED_UNICODE);
	}
}

