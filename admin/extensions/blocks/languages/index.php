<?php
namespace Cms\Admin\Blocks\Languages;

if (!defined('VENUS')) {
	die;
}

$this->loadLanguage();
$this->loadPlugins();

$controller = $this->createController($this->app->request->getController());

$this->app->plugins->run('admin_block_languages_init', $controller);

$controller->dispatch($this->getAction());
