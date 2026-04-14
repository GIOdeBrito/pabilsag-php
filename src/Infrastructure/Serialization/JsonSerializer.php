<?php

namespace Pabilsag\Infrastructure\Serialization;

class JsonSerializer
{
	public static function serialize(object $object): string
	{
		$data = self::extract($object);
		return json_encode($data, JSON_THROW_ON_ERROR);
	}

	private static function extract(object $object): array
	{
		$reflection = new ReflectionClass($object);
		$data = [];

		foreach ($reflection->getProperties() as $property)
		{
			$property->setAccessible(true);
			$value = $property->getValue($object);
			$key = $property->getName();

			if (is_object($value))
			{
				$data[$key] = self::extract($value);
			}
			else
			{
				$data[$key] = $value;
			}
		}

		return $data;
	}
}

