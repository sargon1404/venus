<?php

namespace Bin;

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
	* @param array $options List of options
	*/
	protected array $options = [];

	/**
	* Builds the command object
	* @param App The app object
	* @param string $command The command name
	* @param string $action The executed action
	*/
	public function __construct(App $app, string $command = '', string $action = '', array $options = [])
	{
		$this->app = $app;
		$this->name = $command;
		$this->action = $action;
		$this->options = $options;
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
	*/
	public function index()
	{
	}

	/**
	* @ Shows the help options for a command
	*/
	public function help()
	{
		$help = new Help($this->app);
		if (!$this->action) {
			$help->listActions($this->name, $this->actions);
		} else {
			if (!isset($this->actions[$this->action])) {
				$this->app->bin->error("Unknown action: {$this->action}");
			}

			$data = $this->actions[$this->action];

			echo "\n";
			$help->printHeader($this->name . ':' . $this->action);
			echo "\n";
			$help->printDescription($data[1]);
			$help->listOptions($this->action, $data[2] ?? []);
			$help->printUsage((array)$data[3]);

			echo "\n";
		}
	}

	/**
	* Prints an info text
	*/
	public function printInfo(string $text)
	{
		$this->newline();

		$this->print($text, $this->getColor('info'));
	}

	/**
	* Prints a newline
	*/
	public function newline()
	{
		echo "\n";
	}

	/**
	* Prints the done message
	*/
	public function done()
	{
		$this->newline();
		$this->print("Done", '1;33');
		$this->newline();
	}

	/**
	* Will print an error about the missing arguments
	* @param int $size The number of required arguments
	*/
	protected function errorArguments(int $size = 1)
	{
		$data = $this->actions[$this->action];
		$usage_array = App::getArray($data[3] ?? []);

		$text = "{$size} arguments must be passed to this command.";
		if ($size == 1) {
			$text = "One argument must be passed to this command.";
		}

		if (!empty($usage_array)) {
			$text.= "\n\n";
			$text.= $this->padStringLeft("Usage:", 10);
			$text.= "\n\n";

			foreach ($usage_array as $usage) {
				$text.= $this->padStringLeft($usage . "\n", 10);
			}
		}

		$this->error(trim($text));
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

		$this->error($text);
	}
}
