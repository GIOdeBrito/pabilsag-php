<?php

namespace Pabilsag\Error;

use Pabilsag\Services\{ Logger, FileLogger };

final class ErrorHandler
{
	private \Closure $onErrorCallback;
	private bool $writeOutput = false;

	public function __construct (
		private readonly Logger $logger,
		private readonly FileLogger $fileLogger
	) {
		$this->onErrorCallback = function (): void {
			echo file_get_contents(__DIR__ . '/../Template/InternalError.php');
		};
	}

	public function onError(callable $func): void
	{
		$this->onErrorCallback = \Closure::fromCallable($func);
	}

	public function outputErrorsToLogFile (bool $value): void
	{
		$this->writeOutput = $value;
	}

	private function shouldWriteToFile (): bool
	{
		return $this->writeOutput;
	}

	public function handleErrors(): void
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '0');
		ini_set('log_errors', '1');

		$this->registerHandlers();
	}

	private function registerHandlers(): void
	{
		$log = function (string $type, string $msg, string $file, int|string $line): void {
            $this->logger->error("Pabilsag $type -> $msg | File: $file | Line: $line");

			// Write the error to the log file
			if($this->shouldWriteToFile())
			{
				$this->fileLogger->log($type, sprintf("%s | Message: %s | File: %s | Line: %s", $type, $msg, $file, $line));
			}
        };

		// Convert errors to log only (no throw)
		set_error_handler(function (int $severity, string $message, string $file, int $line) use ($log): bool {
			$log('WARN', $message, $file, $line);

			// suppress default PHP error handling
			return true;
		});

		// Fatal errors + shutdown
		register_shutdown_function(function () use ($log): void {
			$error = error_get_last();
			if ($error === null) {
				return;
			}

			$log('FATAL ERROR', $error['message'], $error['file'], $error['line']);
			($this->onErrorCallback)($error['message'], $error['file'], $error['line']);
		});

		// Uncaught exceptions
		set_exception_handler(function (\Throwable $ex) use ($log): void {
			$log('EXCEPTION', $ex->getMessage(), $ex->getFile(), $ex->getLine());
			($this->onErrorCallback)($ex->getMessage(), $ex->getFile(), $ex->getLine());
		});
	}
}

