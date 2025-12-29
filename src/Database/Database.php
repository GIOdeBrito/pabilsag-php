<?php 

namespace GioPHP\Database;

use GioPHP\Interfaces\DatabaseInterface;

class Database implements DatabaseInterface
{
	private \PDO $pdo;
	
	public function __construct (
		public string $dsn,
		public string $user,
		public string $pwd,
		public array $options = []
	) {
		
	}
	
	public function connect (): bool
	{
		$defaultSettings = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		];
		
		$this->pdo = new PDO(
			$this->dsn,
			$this->user,
			$this->pwd,
			$this->options + $defaultSettings,
		);
		
		
	}
}

?>