<?php

namespace Pabilsag\Infrastructure;

use Pabilsag\Services\Loader;

class ConnectionFactory
{
    private array $connections = [];

    public function __construct(
        private readonly Loader $loader
    ) {}

	public function get(string $name): \PDO
    {
        return $this->connections[$name] ??= $this->create($name);
    }

	private function create(string $name): \PDO
    {
        $meta = $this->getMetadata($name);

		return new \PDO(
            $meta->dsn,
            $meta->user,
            $meta->pwd,
            $meta->options + [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    private function getMetadata(string $name): object
    {
        $metadata = $this->loader->getConnectionMetadata();

        return array_key_exists($name, $metadata)
			? (object) $metadata[$name] : throw new \Exception("Connection '{$name}' does not exist");
    }
}

