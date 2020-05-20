<?php
namespace Venus\Autoload;

use function Mars\Autoload\get_filename;

/**
* Autoloader for the cms files
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Cms\\')) {
		return;
	}

	$map = [
		'Cms\Plugins' => 'Cms\Extensions\Plugins',
		'Cms\Blocks' => 'Cms\Extensions\Blocks',
		'Cms\Admin\Blocks' => 'Cms\Admin\Extensions\Blocks',
	];

	$name = str_replace(array_keys($map), $map, $name);

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/' . get_filename($parts);

	require($filename);
});
