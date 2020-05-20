<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the venus files
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Venus\\Admin\\')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = __DIR__ . '/classes/' . get_filename($parts, 2);

	require($filename);
});
