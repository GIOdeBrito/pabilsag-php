<?php

namespace GioPHP\Error;

final class ErrorHandler
{
	private \Closure $shutdownHandler;

	public function __construct ()
	{
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

	public function disableHandler (bool $value = false)
	{

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

			error_log($output);

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

			error_log($output);

			call_user_func($this->shutdownHandler);
			die();
		});
	}
}

?>