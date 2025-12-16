<?php

namespace GioPHP\Helpers\Types;

// Sort of polyfill for PHP's 8.3 'json_validate' function
function json_validator (string $data): bool
{
	json_decode($data);

	if(json_last_error() === 0)
	{
		return true;
	}

	return false;
}

?>