<?php

namespace Pabilsag\Interfaces;

use Pabilsag\Enums\ResponseTypes;

interface ResponseInterface2
{
	public function setStatus(): void;
	public function setBody(): void;
	public function send(): void;
}

