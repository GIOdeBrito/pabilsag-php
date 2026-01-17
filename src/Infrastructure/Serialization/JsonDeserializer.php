<?php

namespace Pabilsag\Infrastructure\Serialization;

// Basic implementation of a JSON deserializer

class JsonDeserializer
{
	public function fromJson (string $data, string $className): object
	{
        if(!class_exists($className))
        {
            throw new \Exception("Data Object: {$className} does not exist");
        }

        return new $className( ...json_decode($data, true) );
	}
}

?>