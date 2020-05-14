<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the CLI classes
*/
\spl_autoload_register(function ($name) {
	if (strpos($name, 'Venus\\Cli\\') !== 0) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = __DIR__ . '/classes/' . get_filename($parts, 2);

	require($filename);
});
