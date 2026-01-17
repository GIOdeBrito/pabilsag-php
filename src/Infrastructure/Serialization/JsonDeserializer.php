<?php

namespace Pabilsag\Infrastructure\Serialization;

use Pabilsag\Services\Logger;

// Basic implementation of a JSON deserializer

class JsonDeserializer
{
	public function __construct (
		public Logger $logger
	) {}

	public function fromJson (string $data, string $className): object
	{
        if(!class_exists($className))
        {
            throw new \Exception("Data Object: {$className} does not exist");
        }

		try
		{
			return new $className( ...json_decode($data, true) );
		}
		// Throwable catches both errors and exceptions
		catch(\Throwable $ex)
		{
			$this->logger->error($ex->getMessage());
			throw new \Exception("An error ocurred during object deserialization");
		}
	}
}

?>