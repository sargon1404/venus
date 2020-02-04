<?php
namespace Cms\Admin\Blocks\Login;

if(!defined('VENUS')) die;


$this->loadLanguage();
$this->loadPlugins();

$this->app->theme->header_template = 'login/header';
$this->app->theme->footer_template = 'login/footer';

$controller = $this->getController();

$this->app->plugins->run('adminBlockLoginInit', $controller);

$controller->dispatch($this->getAction());