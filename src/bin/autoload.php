<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the BIN classes
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Venus\\Bin\\')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = __DIR__ . '/classes/' . get_filename($parts, 2);

	require($filename);
});
