<?php
define('VENUS', 1);
define('VENUS_ADMIN', 1);

chdir('..');
require('src/admin/boot/system.php');
die("cxvcxvxc");
try {
	if (!$venus->session->get('admin')) {
		$venus->session->set('admin_referrer', $venus->full_url);

		$venus->redirectForce('login.php');
	}

	$block_name = $venus->request->value($venus->config->block_param);
	if (!$block_name) {
		$block_name = 'index';
	}

	$venus->document = new \venus\admin\Block($block_name);

	$venus->start();
	$venus->document->output();
	$venus->end();

	$venus->output();
} catch (\Exception $e) {
	$venus->fatalError($e->getMessage());
}
