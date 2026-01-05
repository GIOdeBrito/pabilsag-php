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

?>