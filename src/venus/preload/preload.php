<?php

chdir(dirname(__DIR__, 3));

require('src/mars/preload/preload.php');

//load the traits & interfaces
$files = require('src/venus/preload/traits-interfaces.php');
foreach ($files as $file) {
	//opcache_compile_file($file);
}

//load the classes
$files = require('src/venus/preload/classes.php');

foreach ($files as $file) {
	//opcache_compile_file($file);
}
