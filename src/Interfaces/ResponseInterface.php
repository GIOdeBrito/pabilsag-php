<?php

namespace Pabilsag\Interfaces;

use Pabilsag\Enums\ResponseTypes;

interface ResponseInterface
{
	public function getStatus(): int;
	public function setStatus(int $code): void;
	public function getContentType(): string;
	public function send(): void;
}

