<?php

namespace Pabilsag\Helpers\String;

// Remove all whitespaces
function normalize_whitespace (string $str): string
{
    return preg_replace('/\s+/', '', $str);
}

function remove_linebreaks (string $str): string
{
	return str_replace([ "\r", "\n" ], '', $str);
}

function str_remove_special_chars (string $value, string $replace = ''): string
{
	return preg_replace('/[^a-zA-Z0-9_]/', $replace, iconv('utf-8', 'ASCII//TRANSLIT', $value));
}

?>