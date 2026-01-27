<?php

namespace Pabilsag\Helpers\Env;

function env_reader (string $path): array
{
    $file = @fopen($path, "r");

    $params = [];

    while(($buffer = fgets($file, 4096)) !== false)
    {
        // Ignores empty lines
        if(trim($buffer) === "")
        {
            continue;
        }

        // Ignores comments
        if(str_starts_with($buffer, '#'))
        {
            continue;
        }

        $split_string = explode('=', $buffer);

        $key = trim($split_string[0]);
        $value = trim($split_string[1]);

        // Checks for invalidity on key
        if(strpbrk($key, ' -"\''))
        {
            throw new \Exception("Key {$key} is not valid");
        }

        // Remove quotes from value if have any
        if(substr($value, 0, 1) === '"')
        {
            if(substr($value, -1) !== '"')
            {
                throw new Exception("String not properly wrapped: {$value}");
            }

            $value = substr($value, 1, -1);
        }
        // If does not contains quotes
        // then we check for validity
        else if(strpbrk($value, ' "\''))
        {
            throw new \Exception("Value {$value} is not valid");
        }

        $params[$key] = $value;
    }

    foreach($params as $key => $value):

        putenv("{$key}={$value}");

    endforeach;

	return $params;
}

