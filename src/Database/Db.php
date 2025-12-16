<?php

namespace GioPHP\Database;

use GioPHP\Services\Loader;
use GioPHP\Services\Logger;

class Database
{
	private ?\PDO $pdo = NULL;
	private Loader $loader;
	private Logger $logger;

	public function __construct (Loader $loader, Logger $logger)
	{
		$this->loader = $loader;
		$this->logger = $logger;
	}

	public function __destruct ()
	{
		$this->close();
	}

	public function open (): bool
	{
		$connection = $this->loader->getConnectionString();
		$login = $this->loader->getDatabaseLogin();
		$pwd = $this->loader->getDatabaseSecret();

		try
		{
			$this->pdo = new \PDO($connection, $login, $pwd);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->logger->info("Connected to database.");
			return true;
		}
		catch(\PDOException $ex)
		{
			$this->logger->error("Failed to open database connection: {$ex->getMessage()}.");
			return false;
		}
	}

	public function isConnected (): bool
	{
		if(is_null($this->pdo))
		{
			return false;
		}

		return true;
	}

	public function query (string $sql, array $params = [], bool $isObject = false): array|object
	{
		if(!$this->isConnected())
		{
			return [];
		}

		try
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

			// Return as array
			return $res->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch(\PDOException $ex)
		{
			return (object)[ 'err' => true, 'message' => $ex->getMessage() ];
		}
	}

	public function exec (string $sql, array $params = []): bool
	{
		if(!self::isConnected())
		{
			return false;
		}

		try
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
		catch(\Exception $ex)
		{
			$this->logger->error($ex->getMessage());
			return false;
		}
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

	public function commit (): void
	{
		$this->pdo->commit();
	}

	public function rollback (): void
	{
		$this->pdo->rollback();
	}

	public function close (): void
	{
		$this->pdo = NULL;
	}
}

?>