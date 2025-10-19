<?php

namespace GioPHP\Helpers;

use function GioPHP\Helpers\toDateTime;

function convertToType (mixed $value, string $type = 'any', bool $isArray = false): mixed
{
	switch($type)
	{
		case 'int':
		case 'integer':
			return intval($value);
			break;

		case 'float':
		case 'double':
			return floatval($value);
			break;

		case 'boolean':
		case 'bool':
			return filter_var($value, FILTER_VALIDATE_BOOLEAN);
			break;

		case 'date':
			return toDateTime($value);
			break;

		case 'array':
			return (array) $value;
			break;

		case 'object':
			return (object) $value;
			break;

		case 'any':
			return $value;
			break;

		case 'string':
		default:
			return strval($value);
			break;
	}
}

// Sort of polyfill for PHP's 8.3 'json_validate' function
function jsonValidate (string $data): bool
{
	json_decode($data);

	if(json_last_error() === 0)
	{
		return true;
	}

	return false;
}

?>