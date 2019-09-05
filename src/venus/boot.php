<?php
namespace Venus;

try {
	require('src/mars/autoload.php');
	require('src/venus/autoload.php');
	require('src/venus/autoload-cms.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('bootSystem');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
