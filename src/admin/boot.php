<?php
namespace Venus\Admin;

try {
	require('src/mars/autoload.php');
	require('src/venus/autoload.php');
	require('src/venus/autoload-cms.php');
	require('src/admin/autoload.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('admin_boot');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
