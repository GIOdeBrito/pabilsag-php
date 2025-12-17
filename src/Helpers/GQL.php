<?php 

namespace GioPHP\Helpers\GQL;

function where (iterable $data, \Closure $condition): mixed
{
    foreach ($data as $item)
    {
        if($condition($item))
        {
            yield $item;
        }
    }
}

?>