<?php
namespace Venus\Cli;

try {
	require('src/mars/autoload.php');
	require('src/venus/autoload.php');
	require('src/venus/autoload-cms.php');
	require('src/admin/autoload.php');
	require('src/cli/autoload.php');
	require('src/cli/autoload-cli.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('cliBootSystem');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
