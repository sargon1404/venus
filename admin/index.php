<?php
namespace Venus\Admin;

define('VENUS', 1);
define('VENUS_ADMIN', 1);

chdir('..');
require('src/admin/boot.php');

//echo $app->html->selectOptions([1 => 'raz', 2 => 'at<oth', 3 => 'hermes'], 2);
echo $app->html->select('some name', [1 => 'raz', 2 => 'at<oth', 3 => 'hermes'], 2);

die;

try {
	$block_name = $app->request->value($app->config->block_param);
	if (!$app->session->get('admin')) {
		$block_name = 'login';
	} else {
		if (!$block_name) {
			$block_name = 'index';
		}
	}

	$app->document = new Block($block_name);

	$app->start();
	$app->document->output();
	$app->end();

	$app->output();
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
