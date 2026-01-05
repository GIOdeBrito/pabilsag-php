<?php

namespace Pabilsag\Interfaces;

use Pabilsag\Enums\ResponseTypes;

interface ResponseInterface
{
	public function getStatus(): int;
	public function getBody(): array|object;
	public function getText(): string;
	public function getHTML(): string;
	public function getFilePath(): string;
	public function getFilename(): string;
	public function getView(): string;
	public function getViewData(): array|object;
	public function getLayout(): string;
	public function getResponseType(): ResponseTypes;
	public function getContentType(): string;
}

?>