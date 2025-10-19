<?php

namespace GioPHP\Error;

use GioPHP\Services\Logger;

final class ErrorHandler
{
	private \Closure $shutdownHandler;

	private Logger $logger;

	public function __construct (Logger $logger)
	{
		$this->logger = $logger;

		// Do not allow errors to be written on the HTML document
		ini_set('display_errors', '0');
		error_reporting(E_ALL);

		$this->initErrorHandler();
		$this->initShutdownHandler();

		$this->shutdownHandler = function ()
		{
			echo file_get_contents(constant('GIOPHP_SRC_ROOT_PATH').'/Template/InternalError.php');
		};
	}

	public function setErrorCallback (callable $func)
	{
		$this->shutdownHandler = $func;
	}

	private function initErrorHandler (): void
	{
		// Convert errors into exceptions
		set_error_handler(function ($severity, $message, $file, $line)
		{
			if (!(error_reporting() & $severity)) {
				return;
			}

			$output = "GioPHP ERROR -> {$message}. File: {$file}. Line: {$line}";

			$this->logger->error($output);

			throw new \ErrorException($message, 0, $severity, $file, $line);
		});
	}

	private function initShutdownHandler (): void
	{
		register_shutdown_function(function ()
		{
			$error = error_get_last();

			if(is_null($error) || $error['type'] !== E_ERROR)
			{
				return;
			}

			$file = $error['file'];
			$line = $error['line'];
			$message = $error['message'];

			$output = "GioPHP ERROR -> {$message}. File: {$file}. Line: {$line}";

			$this->logger->error($output);

			call_user_func_array($this->shutdownHandler, [ $message, $file, $line ]);
			die();
		});
	}
}

?>