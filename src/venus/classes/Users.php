<?php
/**
* The Users Class
* @package Venus
*/

namespace Venus;

/**
* The Users Class. Container for multiple users
*/
class Users extends Items
{
	/**
	* @internal
	*/
	protected static $id_name = 'uid';

	/**
	* @internal
	*/
	protected static $table = 'venus_users';

	/**
	* @internal
	*/
	protected static $class = '\Venus\User';
}
