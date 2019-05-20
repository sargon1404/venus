<?php
/**
* The Model Class
* @package Venus
*/

namespace Venus;

/**
* The Model Class
* Implements the Model functionality of the MVC pattern
*/
abstract class Model extends \Mars\Model
{
	/**
	* @var string $prefix Prefix to be used when calling plugins
	*/
	public $prefix = '';

	/**
	* @internal
	*/
	protected static $users_table = 'venus_users';

	/**
	* @internal
	*/
	protected static $usergroups_table = 'venus_usergroups';

	/**
	* @internal
	*/
	protected static $categories_table = 'venus_categories';

	/**
	* Builds the Model
	*/
	public function __construct()
	{
		parent::__construct();

		$this->prefix = $this->getPrefix('model');
	}

	/**
	* Returns the users table name
	* @return string
	*/
	public function getUsersTable() : string
	{
		return static::$users_table;
	}

	/**
	* Returns the usergroups table name
	* @return string
	*/
	public function getUsergroupsTable() : string
	{
		return static::$usergroups_table;
	}

	/**
	* Returns the categories table name
	* @return string
	*/
	public function getCategoriesTable() : string
	{
		return static::$categories_table;
	}
}
