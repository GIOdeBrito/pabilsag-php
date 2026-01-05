<?php

namespace Pabilsag\Services;

class Logger
{
	private function log (string $level, string $message): void
	{
		$now = date('Y-m-d H:i:s');

		$loglevel = [
			'INFO' 		=> LOG_INFO,
			'WARNING' 	=> LOG_WARNING,
			'ERROR' 	=> LOG_ERR
		];

		openlog('Pabilsag', LOG_PID | LOG_PERROR, LOG_USER);
		syslog($loglevel[$level], "[{$now}] $message");
		closelog();
	}

	public function info (string $message): void
	{
		$this->log('INFO', $message);
	}

	public function warning (string $message): void
	{
		$this->log('WARNING', $message);
	}

	public function error (string $message): void
	{
		$this->log('ERROR', $message);
	}
}

?>