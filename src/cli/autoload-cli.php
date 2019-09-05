<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the CLI classes
*/
\spl_autoload_register(function ($name) {
	if (strpos($name, 'Cli\\') !== 0) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/' . get_filename($parts, false);
	var_dump($filename);
	die;
	require($filename);
});
