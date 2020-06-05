<?php
/**
* The Model Class
* @package Venus
*/

namespace Venus;

use Venus\Helpers\Controls;

/**
* The Model Class
* Implements the Model functionality of the MVC pattern
*/
abstract class Model extends \Mars\Model
{
	use MvcTrait;

	/**
	* @internal
	*/
	protected static string $users_table = 'venus_users';

	/**
	* @internal
	*/
	protected static string $usergroups_table = 'venus_usergroups';

	/**
	* @internal
	*/
	protected static string $categories_table = 'venus_categories';

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
