<?php

namespace Pabilsag\Helpers\Env;

function env_reader(string $path): array
{
    $file = @fopen($path, 'r');
    if (!$file) {
        throw new \Exception("Cannot open file: $path");
    }

    $params = [];

    while (($buffer = fgets($file, 4096)) !== false)
	{
        $line = trim($buffer);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $split = explode('=', $line, 2);
        if (count($split) !== 2) {
            throw new \Exception("Invalid line: $line");
        }

        $key   = trim($split[0]);
        $value = trim($split[1]);

        if (strpbrk($key, ' -"\'') || $key === '') {
            throw new \Exception("Invalid key: $key");
        }

        // Remove surrounding quotes
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        } elseif (strpbrk($value, ' "\'')) {
            throw new \Exception("Invalid value: $value");
        }

        $params[$key] = $value;
    }

    fclose($file);

    foreach ($params as $key => $value) {
        putenv("$key=$value");
    }

    return $params;
}

