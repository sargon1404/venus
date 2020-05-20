<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the CLI classes
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Cli\\')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/cli/classes/' . get_filename($parts, 1);

	if (!is_file($filename)) {
		return;
	}

	require($filename);
});
