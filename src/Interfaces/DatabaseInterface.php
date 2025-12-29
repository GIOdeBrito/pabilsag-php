<?php 

namespace GioPHP\Interfaces;

interface DatabaseInterface
{
	public function __construct (string $dsn, string $user, string $pwd, array $options);
	public function __destruct ();
	
	public function connect(): bool;
	public function disconnect(): void;
	public function query(string $sql, array $params): array|object;
	public function execute(string $sql, array $params): bool;
	public function commit(): void;
	public function rollback(): void;
}

?>