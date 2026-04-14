<?php

namespace Pabilsag\Infrastructure\Serialization;

class JsonDeserializer
{
	public static function deserialize(string $json, string $class): object
	{
		$data = json_decode($json, true);

		if(json_last_error() !== JSON_ERROR_NONE)
		{
			throw new Exception('Invalid JSON: ' . json_last_error_msg());
		}

		return self::hydrate($data, $class);
	}

	private static function hydrate(array $data, string $class): object
	{
		$reflection = new ReflectionClass($class);
		$instance = $reflection->newInstanceWithoutConstructor();

		foreach($data as $key => $value)
		{
			$property = self::findProperty($reflection, $key);

			if($property)
			{
				$property->setAccessible(true);
				$property->setValue($instance, self::castValue($value, $property));
			}
		}

		return $instance;
	}

	private static function findProperty(ReflectionClass $reflection, string $key): ?ReflectionProperty
	{
		if($reflection->hasProperty($key))
		{
			return $reflection->getProperty($key);
		}

		// snake_case to camelCase fallback
		$camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));

		if($reflection->hasProperty($camel))
		{
			return $reflection->getProperty($camel);
		}

		return null;
	}

	private static function castValue($value, ReflectionProperty $property)
	{
		$type = $property->getType();

		if(!$type)
		{
			return $value;
		}

		$typeName = $type->getName();

		if($typeName === 'array' && is_array($value))
		{
			return $value;
		}

		return $value;
	}
}

