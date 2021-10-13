<?php
namespace Venus\Bin;

try {
	require('src/mars/autoload.php');
	require('src/venus/autoload.php');
	require('src/venus/autoload-cms.php');
	require('src/admin/autoload.php');
	require('src/bin/autoload.php');
	require('src/bin/autoload-bin.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('bin_boot');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
