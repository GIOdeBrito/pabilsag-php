<?php 

namespace Pabilsag\Infrastructure;

use Pabilsag\Services\{ Loader, Logger };

class ConnectionFactory
{
	// Variable that stores cached PDO connections
	private array $cached = [];
	
	public function __construct (
		public Loader $loader,
		public Logger $logger
	){}
	
	public function __destruct ()
	{
		foreach(array_keys($this->cached) as $key):
			
			$this->cached[$key] = NULL;
			$this->logger->info("Database connection destroyed: {$key}");
		
		endforeach;
	}
	
	public function get (string $connectionName): \PDO
	{
		// If a cached connection already exists
		// then return its PDO instance
		if(array_key_exists($connectionName, $this->cached))
		{
			return $this->cached[$connectionName];
		}
		
		$connection = $this->create($connectionName, []);
		
		$this->cached[$connectionName] = $connection;
		
		return $connection;
	}
	
	private function create (string $connectionName, array $options = []): \PDO
	{
		$metadata = $this->getConnectionByKeyName($connectionName);
		
		$defaultSettings = [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
		];
		
		$pdo = new \PDO(
			$metadata->dsn,
			$metadata->user,
			$metadata->pwd,
			$metadata->options + $defaultSettings
		);
		
		return $pdo;
	}
	
	private function getConnectionByKeyName (string $key): object
	{
        $metadata = $this->loader->getConnectionMetadata();
        
        if(array_key_exists($key, $metadata))
        {
            return (object) $metadata[$key];
        }
        
        throw new \Exception("Connection '{$key}' does not exist");
	}
}

?>