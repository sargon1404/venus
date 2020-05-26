<?php
namespace Venus\Admin;

define('VENUS', 1);
define('VENUS_ADMIN', 1);

chdir('..');
require('src/admin/boot.php');

try {
	$block_name = $app->request->value($app->config->block_param);

	if (!$app->session->get('admin')) {
		$block_name = 'login';
	} else {
		if (!$block_name) {
			$block_name = 'index';
		}
	}
$block_name = 'login';
	$app->document = new Block($block_name);

	$app->start();
	$app->document->output();
	$app->end();

	$app->output();
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
