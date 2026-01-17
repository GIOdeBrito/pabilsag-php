<?php

namespace Pabilsag\Helpers\Object;

function object_to_assoc_array ($obj, $includePrivate = false): array
{
	$objarray = (array) $obj;

    $className = strval(get_class($obj));
    $keys = array_keys($objarray);

    foreach($keys as $key)
    {
        if(str_starts_with(trim($key), $className))
        {
            if($includePrivate)
            {
                $value = $objarray[$key];
                $objarray[trim(str_replace($className, '', $key))] = $value;
            }

            unset($objarray[$key]);
        }
    }

    return $objarray;
}

?>