<?php 

namespace GioPHP\Helpers\String;

// Remove all whitespaces
function normalize_whitespace (string $str): string
{
    return preg_replace('/\s+/', '', $str);
}

?>