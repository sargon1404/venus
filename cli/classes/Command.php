<?php

namespace Cli;

use Venus\App;

class Command extends Base
{

	/**
	* @param string $name The name of the command
	*/
	protected string $name = '';

	/**
	* @param string $action The name of the action
	*/
	protected string $action = '';

	/**
	* @param array $actions Array defining the available actions, in the format: [method, description, params(optional)]
	*/
	protected array $actions = [];

	/**
	* Builds the command object
	* @param App The app object
	* @param string $command The command name
	* @param string $action The executed action
	*/
	public function __construct(App $app, string $command = '', string $action = '')
	{
		$this->app = $app;
		$this->name = $command;
		$this->action = $action;
	}

	/**
	* Return the available actions
	*/
	public function getActions() : array
	{
		return $this->actions;
	}

	/**
	* Returns the method to be called when an action is executed
	* @param string $action The action to return the method for
	* @return string The method
	*/
	public function getMethod(string $action) : string
	{
		if (!isset($this->actions[$action][0])) {
			return '';
		}

		return $this->actions[$action][0];
	}

	/**
	* Placeholder index method
	* @param array $options The options, if any
	*/
	public function index(array $options)
	{
	}

	/**
	* @param array $options The options, if any
	*/
	public function help(array $options)
	{
		$help = new Help($this->app);
		if (!$this->action) {
			$help->listActions($this->name, $this->actions);
		} else {
			if (!isset($this->actions[$this->action])) {
				$this->app->cli->errorAndDie("Unknown action: {$this->action}");
			}

			$data = $this->actions[$this->action];

			$help->printHeader($this->name . ':' . $this->action);
			$help->printDescription($data[1]);
			$help->listOptions($this->action, $data[2] ?? []);
			$help->printUsage(App::getArray($data[3] ?? []));
			
			echo "\n";
		}
	}

	public function newline()
	{
		echo "\n";
	}

	public function done()
	{
		$this->newline();
		$this->print("Done", '1;33');
	}

	/**
	* Will print an error about the missing options
	*/
	protected function errorOptions()
	{
		$text = '';
		$missing_options = $this->getOptionsMissing();

		foreach ($missing_options as $option) {
			$text.= "The {$option} argument is missing\n";
		}

		$this->errorAndDie($text);
	}
}
