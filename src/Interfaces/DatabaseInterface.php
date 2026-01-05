<?php 

namespace Pabilsag\Interfaces;

use Pabilsag\Infrastructure\ConnectionFactory;

interface DatabaseInterface
{
	public function __construct (ConnectionFactory $connectionFactory);
	
	public function connect(string $connectionName): void;
	public function query(string $sql, array $params): array|object;
	public function execute(string $sql, array $params): bool;
	public function commit(): void;
	public function rollback(): void;
}

?>