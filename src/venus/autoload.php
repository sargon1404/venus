<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the venus files
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Venus\\')) {
		return;
	}

	//don't load admin classes
	if (str_contains($name, 'Venus\\Admin\\')) {
		return;
	}

	//don't load cli classes
	if (str_contains($name, 'Venus\\Cli\\')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = __DIR__ . '/classes/' . get_filename($parts);

	require($filename);
});
