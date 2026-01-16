<?php

// Pabilsag's Autoloader
//
// Import this file into your application's main entrypoint
//
// PHP 8 or higher is required to properly run this tool

spl_autoload_register(function (string $classname)
{
	// Get the root folder of the framework
	$root = __DIR__.'/../';
	$namespace = 'Pabilsag';

	// Splits the path to the selected class
	$paths = explode('/', str_replace('\\', DIRECTORY_SEPARATOR, $classname));

	if($paths[0] !== $namespace)
	{
		return;
	}

	// Removes the first item of the array
	array_shift($paths);

	$classPath = implode('/', $paths);

	// Searches for the file within Pabilsag's folder
	$fullpath = $root.str_replace('\\', DIRECTORY_SEPARATOR, $classPath).'.php';

	if(!file_exists($fullpath))
	{
		throw new Exception("Class '{$classname}' not found");
	}

	require $fullpath;
});

?>
