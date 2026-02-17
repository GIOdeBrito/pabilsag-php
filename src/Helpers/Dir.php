<?php

namespace Pabilsag\Helpers\Dir;

use function Pabilsag\Helpers\Path\standardize_file_path;

function rmdir_recursive (string $path): void
{
	$cleaned_path = standardize_file_path($path);

    $list = array_diff(scandir($cleaned_path), [ '.', '..' ]);

    try
    {
        foreach($list as $file):

            $filepath = "{$cleaned_path}/{$file}";

            if(is_dir($filepath))
            {
                rmdir_recursive($filepath);
                continue;
            }

            unlink($filepath);

        endforeach;

		// Remove path after it is completely empty
	    rmdir($path);
    }
	catch(\Throwable $err)
    {
		throw new \Exception($err->getMessage());
    }
}

?>