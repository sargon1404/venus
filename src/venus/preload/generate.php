<?php
namespace Venus\Preload;

use function Mars\Preload\get_traits_and_interfaces;
use function Mars\Preload\get_classes;
use function Mars\Preload\sort_classes;
use function Mars\Preload\write_file;

chdir(dirname(__DIR__, 3));

require('src/mars/preload/functions.php');
require('src/mars/boot-bin.php');

$app->file->listDir('src/venus/classes', $dirs, $files, true, true);

$traits_and_interfaces = get_traits_and_interfaces($files);
write_file(__DIR__ . '/traits-interfaces.php', $traits_and_interfaces);

$classes = get_classes($files);
$classes = sort_classes($classes);
write_file(__DIR__ . '/classes.php', $classes);

$app->bin->print('Preload list generated');
