<?php

namespace Pabilsag\Services;

use Pabilsag\Services\{ Loader };

class FileLogger
{
	public function __construct (
		private readonly Loader $loader
	) {}

	// TODO: Implement line limit

    public function log (string $level, string $message): void
	{
        $line = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . PHP_EOL;
		file_put_contents($this->loader->getAppLogPath(), $line, FILE_APPEND | LOCK_EX);
    }
}

