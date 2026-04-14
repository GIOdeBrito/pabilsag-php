<?php

namespace Pabilsag\Helpers\Dir;

use function Pabilsag\Helpers\Path\standardize_file_path;

function rmdir_recursive(string $path): void
{
    $path = standardize_file_path($path);

    if(!is_dir($path))
	{
        return;
    }

    $items = array_diff(scandir($path), ['.', '..']);

    foreach($items as $item)
	{
        $filepath = $path . '/' . $item;

        if(is_dir($filepath))
		{
            rmdir_recursive($filepath);
        }
		else
		{
            unlink($filepath);
        }
    }

    rmdir($path);
}

