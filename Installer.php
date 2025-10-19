<?php

// Make sure to run this file from the command prompt
if(php_sapi_name() !== 'cli')
{
	echo "Halted. Run this file from the command prompt.".PHP_EOL;
	die();
}

echo "GioPHP Installer.".PHP_EOL;

// Alter the version for the one to installer will fetch
$VERSION = "1.0.6";

echo "Fetching GioPHP standalone version: {$VERSION}.".PHP_EOL;

$filename = "gio-php-v{$VERSION}.tar.gz";
$url = "https://github.com/GIOdeBrito/gio-php/releases/download/{$VERSION}/".$filename;

if(!file_put_contents("gio-php-v{$VERSION}.tar.gz", file_get_contents($url)))
{
	echo "Error: could not download file from repository.".PHP_EOL;
	echo "Check if the desired version does exist or if it is currently available.".PHP_EOL;
	die();
}

echo "Downloaded standalone file from GitHub repository.".PHP_EOL;

try
{
	// Decompress file
	$p = new PharData($filename);
	$p->decompress();

	echo "File decompressed.".PHP_EOL;

	$tempDir = "__TEMPGIOPHP__";

	$phar = new PharData($filename);
	$phar->extractTo($tempDir, "src/");

	echo "Extracted file's content to the local directory.".PHP_EOL;

	// Rename the source folder to GioPHP
	rename("{$tempDir}/src", "GioPHP");
	rmdir($tempDir);

	echo "Removed temporary directory.".PHP_EOL;

	unlink($filename);

	echo "Removed original tar file.".PHP_EOL;
}
catch(Exception $ex)
{
	echo "Error: ".$ex->getMessage();
	echo PHP_EOL;
	die();
}

echo "Finished installation.".PHP_EOL;

echo "Thank you for using GioPHP. Hope you also leave some feedback :D".PHP_EOL;

?>