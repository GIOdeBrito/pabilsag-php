<?php

namespace Pabilsag\Helpers\Dir;

function rmdir_recursive (string $path): bool
{
    $cleaned_path = normalize_file_path($path);

    $list = array_diff(scandir($cleaned_path), [ '.', '..' ]);

    try
    {
        foreach($list as $file):

            $filepath = "{$cleaned_path}/{$file}";

            echo $filepath, PHP_EOL;

            if(is_dir($filepath))
            {
                rmdir_recursive($filepath);
                continue;
            }

            unlink($filepath);

        endforeach;
    }
    catch(\Throable $err)
    {
        throw $err;
    }

	// Remove path after it is completely empty
    rmdir($path);

    return true;
}

?>