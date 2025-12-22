<?php 

namespace GioPHP\Interfaces;

interface DatabaseInterface
{
	public function __construct ();
	public function __destruct ();
	
	public function connect(): void;
	public function disconnect(): void;
	public function query(string $sql, array $params): array|object;
	public function execute(string $sql, array $params): bool;
	public function commit(): void;
	public function rollback(): void;
}

?>