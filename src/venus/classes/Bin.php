<?php
/**
* The Bin Class
* @package Venus
*/

namespace Venus;

/**
* The Bin Class
*/
class Bin extends \Mars\Bin
{
	/**
	* @internal
	*/
	protected static string $table = 'venus_bin';

	/**
	* Returns all defined bin classes
	* @return array
	*/
	public function getCommandClasses() : array
	{
		return $this->app->db->selectList(static::$table, 'command', 'class', [], 'command');
	}

	/**
	* Returns the class associated with a command
	* @param string $command The command to return the class for
	* @return $class The class
	*/
	public function getCommandClass(string $command) : string
	{
		return (string)$this->app->db->selectResult(static::$table, 'class', ['command' => $command]);
	}

	/**
	* Adds a command to the list of bin commands
	* @param string $command The command to add
	* @param string $class The class handling the command
	* @return $this
	*/
	public function addCommand(string $command, string $class)
	{
		if ($this->app->db->count(static::$table, ['command' => $command])) {
			return $this;
		}

		$insert_array = [
			'command' => $command,
			'class' => $class
		];

		$this->app->db->insert(static::$table, $insert_array);

		return $this;
	}

	/**
	* Removes a command from the list of bin commands
	* @param string $command The command to remove
	* @return $this
	*/
	public function deleteCommand(string $command)
	{
		$this->app->db->delete(static::$table, ['command' => $command]);

		return $this;
	}
}
