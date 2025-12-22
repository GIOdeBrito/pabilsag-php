<?php

namespace GioPHP\Helpers\Polyfill;

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

// Polyfill for the array_find function
function garray_find (array $arr, callable $predicate): mixed
{
    foreach($arr as $key => $value):

        $result = $predicate($value, $key);

        if($result)
        {
            return $value;
        }

    endforeach;

    return NULL;
}

?>