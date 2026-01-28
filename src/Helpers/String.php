<?php

namespace Pabilsag\Helpers\String;

// Concatenate n number of strings
function strcat (string ...$strings): string
{
	return array_reduce($strings, fn($total, $current) => $total . $current, "");
}

// Remove all whitespaces
function normalize_whitespace (string $str): string
{
    return preg_replace('/\s+/', '', $str);
}

function remove_linebreaks (string $str): string
{
	return str_replace([ "\r", "\n" ], '', $str);
}

// Removes accented letters, special characters
// and overall "cleans" a string
function str_remove_special_chars (string $value, string $replace = ''): string
{
	return preg_replace('/[^a-zA-Z0-9_]/', $replace, iconv('utf-8', 'ASCII//TRANSLIT', $value));
}

?>