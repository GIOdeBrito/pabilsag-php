<?php

namespace Pabilsag\Error;

use Pabilsag\Services\Logger;

final class ErrorHandler
{
	private \Closure $onerrorcallback;

	public function __construct (
		private Logger $logger
	) {
		$this->logger = $logger;
	}

	public function handleErrors (): void
	{
		// Do not allow errors to be written on the HTML document
		error_reporting(E_ALL);
		ini_set('display_errors', '0');
		ini_set('log_errors', 1);

		$this->setErrorHandlers();

		$this->onerrorcallback = function (): void {

			echo file_get_contents(constant('Pabilsag_SRC_ROOT_PATH').'/Template/InternalError.php');
		};
	}

	public function onError (callable $func): void
	{
		$this->onerrorcallback = $func;
	}

	private function setErrorHandlers (): void
	{
		$foutput = fn($title, $message, $file, $line) => sprintf("%s -> %s | File: %s | Line: %s", $title, $message, $file, $line);;

		// Convert errors into exceptions
		set_error_handler(function ($severity, $message, $file, $line) use ($foutput)
		{
			$this->logger->error($foutput("Pabilsag WARN", $message, $file, $line));

			//throw new \ErrorException($message, 0, $severity, $file, $line);

			return true;
		});

		register_shutdown_function(function () use ($foutput)
		{
			$error = error_get_last();

			if(is_null($error))
			{
				return;
			}

			$file = $error['file'];
			$line = strval($error['line']);
			$message = $error['message'];

			$this->logger->error($foutput("Pabilsag FATAL ERROR", $message, $file, $line));

			call_user_func_array($this->onerrorcallback, [ $message, $file, $line ]);
		});

		// For uncaught exceptions
		set_exception_handler(function (\Throwable $ex) use ($foutput)
		{
			$message = $ex->getMessage();
			$file = $ex->getFile();
			$line = strval($ex->getLine());

			$this->logger->error($foutput("Pabilsag EXCEPTION", $message, $file, $line));

			call_user_func_array($this->onerrorcallback, [ $message, $file, $line ]);
		});
	}
}

?>