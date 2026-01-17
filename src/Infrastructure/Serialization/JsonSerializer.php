<?php

namespace Pabilsag\Infrastructure\Serialization;

use function Pabilsag\Helpers\Object\object_to_assoc_array;

// Basic implementation of JSON serialization

class JsonSerializer
{
	public function __construct (
		public Logger $logger
	) {}

	public function toJson (object $obj, bool $includePrivate = false): string
	{
	    $objarray = object_to_assoc_array($obj, $includePrivate);

	    return json_encode($objarray, JSON_THROW_ON_ERROR);
	}
}

?>