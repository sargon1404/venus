<?php
//#!/usr/bin/env php
namespace Venus\Cli;

if (PHP_SAPI != 'cli') {
    die("bin/venus must be run as a CLI application\n");
}

chdir(dirname(__DIR__));

define('VENUS', 1);

require('src/cli/boot.php');

$name = $app->cli->getCommandName();
if (!$name) {
	$name = 'help';
}

$action = $app->cli->getCommandAction();
$options = $app->cli->getOptions();

print_r($action);
print_r($options);die;

$class = '\\Cli\\' . App::strToClass($name);
$class = $app->plugins->filter('cli_class', $class, $name);
echo $class;die;
$obj = new $class; 