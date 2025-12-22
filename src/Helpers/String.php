<?php 

namespace GioPHP\Helpers\String;

// Remove all whitespaces
function normalize_whitespace ($str)
{
    return preg_replace('/\s+/', '', $str);
}

?>