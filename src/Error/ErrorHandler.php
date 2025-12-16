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
	}
	
	public function useErrorLogging (): void
	{
		// Do not allow errors to be written on the HTML document
		ini_set('display_errors', '0');
		error_reporting(E_ALL);

		$this->createErrorAndShutdownRules();

		$this->shutdownHandler = function (): void {
			
			echo file_get_contents(constant('GIOPHP_SRC_ROOT_PATH').'/Template/InternalError.php');
		};
	}

	public function setErrorCallback (callable $func): void
	{
		$this->shutdownHandler = $func;
	}
	
	private function createErrorAndShutdownRules (): void
	{
		$foutput = fn($message, $file, $line) => "GioPHP ERROR -> {$message}. File: {$file}. Line: {$line}";
		
		// Convert errors into exceptions
		set_error_handler(function ($severity, $message, $file, $line) use ($foutput)
		{
			if (!(error_reporting() & $severity)) {
				return;
			}

			$this->logger->error($foutput($message, $file, $line));

			throw new \ErrorException($message, 0, $severity, $file, $line);
		});
		
		register_shutdown_function(function () use ($foutput)
		{
			$error = error_get_last();

			if(is_null($error) || $error['type'] !== E_ERROR)
			{
				return;
			}

			$file = $error['file'];
			$line = $error['line'];
			$message = $error['message'];

			$this->logger->error($foutput($message, $file, $line));

			call_user_func_array($this->shutdownHandler, [ $message, $file, $line ]);
			die();
		});
	}
}

?>