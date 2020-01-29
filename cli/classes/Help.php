<?php

namespace Cli;

class Help extends Command
{
	protected array $actions = [
		'show' => ['index', 'Shows the available commands'],
		'version' => ['version', 'Shows the version'],
	];

	/**
	* Shows the available commands & actions
	* @param array $options The options, if any
	*/
	public function index(array $options)
	{
		$commands = $this->app->cli->getCommandClasses();
		
		$list = [];			
		foreach ($commands as $name => $class) {
			$obj = new $class($this->app);
			
			$list[$name] = [];
			
			$actions = $obj->getActions();
			foreach ($actions as $action_name => $data) {
				$list[$name][] = [$name . ':' . $action_name, $data[1] ?? ''];
			}
		}
		
		$this->list($list);
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

		$this->list($list);
	}
	
	/**
	* Lists the options of an action
	* @param string $name The name of the action
	* @param string $desc The action's description
	* @param array $options The params list
	* @param array The actions to list
	*/
	public function listOptions(string $action, string $desc, array $options)
	{
		$this->app->cli->header($action);
		$this->app->cli->message(str_pad($desc, 5 + strlen($desc), ' ', STR_PAD_LEFT));
		
		if ($options) {
			echo "\n";
			
			$list = [];
			foreach($options as $key => $val) {
				$list[] = [$key, $val];
			}		
				
			$this->list([$action => $list], false);
		}
	}
}
