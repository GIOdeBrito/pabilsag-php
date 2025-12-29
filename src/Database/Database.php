<?php 

namespace GioPHP\Database;

use GioPHP\Interfaces\DatabaseInterface;
use GioPHP\Infrastructure\ConnectionFactory;

class Database implements DatabaseInterface
{
	private ConnectionFactory $factory;
	private ?\PDO $pdo = NULL;
	
	public function __construct (ConnectionFactory $connectionFactory)
	{
		$this->factory = $connectionFactory;
	}
	
	public function connect (string $connectionName): void
	{
		$pdo = $this->factory->get($connectionName);
		
		$this->pdo = $pdo;
	}
	
	public function query (string $sql, array $params = [], bool $isObject = false): array|object
	{
		$res = $this->pdo->prepare($sql);

		// Set param binds
		if(count($params) > 0)
		{
			$this->setPDOBinds($res, $params);
		}

		$res->execute();

		// Return data as objects
		if($isObject)
		{
			return $res->fetchAll(\PDO::FETCH_OBJ);
		}

		// Return data as associative array
		return $res->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	public function execute (string $sql, array $params = []): bool
	{
		$res = $this->pdo->prepare($sql);

		$this->pdo->beginTransaction();

		// Set param binds
		if(count($params) > 0)
		{
			$this->setPDOBinds($res, $params);
		}

		$result = $res->execute();

		return $result;
	}
	
	public function commit(): void
	{
		$this->pdo->commit();
	}
	
	public function rollback(): void
	{
		$this->pdo->rollBack();
	}
	
	private function setPDOBinds (\PDOStatement $statement, array $params): void
	{
		// Sequential bindings
		if(array_is_list($params))
		{
			foreach($params as $i => $value)
			{
				$statement->bindValue($i + 1, $value);
			}

			return;
		}

		// Associative bindings
		foreach($params as $key => $value)
		{
			if(!str_starts_with($key, ':'))
			{
				$key = ':'.$key;
			}

			$statement->bindValue($key, $value);
		}
	}
}

?>