<?php
namespace Venus\Cli;

if (PHP_SAPI != 'cli') {
	die("bin/venus must be run as a CLI application\n");
}

chdir(dirname(__DIR__));

define('VENUS', 1);

require('src/cli/boot.php');

try {
	$command = $app->cli->getCommandName();
	$action = $app->cli->getCommandAction();
	$options = $app->cli->getOptions();

	if (!$command) {
		$command = 'help';
	}

	$class = $app->cli->getCommandClass($command);
	$class = $app->plugins->filter('cliClass', $class, $command);

	if (!$class || !class_exists($class)) {
		$app->cli->error("Unknown command: {$command}");
	}

	$obj = new $class($app, $command, $action, $options);
	if (!$obj instanceof \Cli\Command) {
		$app->cli->error("Class {$class} must extend \Cli\Command");
	}

	$method = $obj->getMethod($action);
	$method = $app->plugins->filter('cliMethod', $method, $action);

	//always call the index method for the Help command
	if ($command == 'help') {
		$method = 'index';
	}
	//always call the help method, if the --help option is passed or no action is specified
	if (!$method || isset($options['help']) || isset($options['h'])) {
		$method = 'help';
	}

	if (!method_exists($obj, $method)) {
		$app->cli->error("Unknown action: {$action}");
	}

	//can the method be called?
	$rm = new \ReflectionMethod($obj, $method);

	if (!$rm->isPublic() || $rm->getDeclaringClass()->isAbstract()) {
		$app->cli->error("Unknown action: {$action}");
	}

	$obj->$method();
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
