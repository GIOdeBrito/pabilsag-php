<?php

namespace Pabilsag\Helpers\Path;

function standardize_file_path (string $path): string
{
	$fpath = str_replace('\\', DIRECTORY_SEPARATOR, $path);

	$prepend = '';

	if(str_starts_with($path, DIRECTORY_SEPARATOR))
	{
	    $prepend = DIRECTORY_SEPARATOR;
	}

	return $prepend.implode(DIRECTORY_SEPARATOR, array_filter(explode(DIRECTORY_SEPARATOR, $fpath), fn($x) => $x));
}

?>