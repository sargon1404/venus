<?php
namespace Venus;

define('VENUS', 1);

require('src/venus/boot.php');

try {
	$app->start();
	echo 'Some content';
	$app->end();
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}

$app->output();

/*
try {
	$venus->document = null;
	$venus->router = new Router;

	$type = $venus->request->get($venus->config->type_param);
	$id = $venus->request->get($venus->config->id_param, 'id');
	$slug_name = $venus->request->get('slug');


	if ($type) {
		//return the document from type
		$venus->document = $venus->router->get_document($type, $id);
	} elseif ($slug_name) {
		//return the document from slug
		$venus->document = $venus->router->get_slug_document($slug_name);
	} else {
		//return the homepage doc
		$venus->is_homepage = $venus->theme->is_homepage = true;

		$venus->document = $venus->router->get_homepage_document();
	}

	if (!$venus->document) {
		$venus->redirect_404();
	}

	//output the document
	$venus->start_content();
	$venus->document->output();
	$venus->end_content();
} catch (\Exception $e) {
	$venus->fatal_error($e->getMessage());
}

$venus->output();
*/
