<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the cms files
*/
\spl_autoload_register(function ($name) {
	if (strpos($name, 'Cms\\') !== 0) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/' . get_filename($parts);
	var_dump($filename);
	die;
	require($filename);
});
