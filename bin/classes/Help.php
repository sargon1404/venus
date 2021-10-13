<?php

namespace Bin;

class Help extends Command
{
	protected array $actions = [
		'show' => ['index', 'Shows the available commands'],
		'version' => ['version', 'Shows the version'],
	];

	/**
	* Shows the available commands & actions
	*/
	public function index()
	{
		$commands = $this->app->bin->getCommandClasses();

		$list = [];
		foreach ($commands as $name => $class) {
			$obj = new $class($this->app);

			$list[$name] = [];

			$actions = $obj->getActions();
			foreach ($actions as $action_name => $data) {
				$list[$name][] = [$name . ':' . $action_name, $data[1] ?? ''];
			}
		}

		echo "\n";
		$this->list($list);
		echo "\n";
	}

	/**
	* List actions
	* @param string $name The name of the command
	* @param array The actions to list
	*/
	public function listActions(string $name, array $actions)
	{
		$list = [];
		foreach ($actions as $action_name => $data) {
			$list[$name][] = [$name . ':' . $action_name, $data[1] ?? ''];
		}

		echo "\n";
		$this->list($list);
		echo "\n";
	}

	/**
	* Lists the options of an action
	* @param string $action The name of action
	* @param array $options The options list
	* @param array The actions to list
	*/
	public function listOptions(string $action, array $options)
	{
		if (!$options) {
			return;
		}

		$list = [];
		foreach ($options as $key => $val) {
			$list[] = [$key, $val];
		}

		echo "\n";
		$this->list([$action => $list], false);
	}

	/**
	* Prints the usage
	* @param array $usage_array
	*/
	public function printUsage(array $usage_array)
	{
		if (!$usage_array) {
			return;
		}

		echo "\n";
		$this->print('Usage:', '', 5);
		echo "\n";

		foreach ($usage_array as $usage) {
			$this->print($usage, '', 5);
		}
	}

	/**
	* Prints a header
	* @param string $text The header
	*/
	public function printHeader(string $text)
	{
		$this->header($text);
	}

	/**
	* Prints a description
	* @param string $text The description
	*/
	public function printDescription(string $text)
	{
		$this->print($text, '', 5);
	}
}
