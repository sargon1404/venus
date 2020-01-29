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
	protected static string $id_name = 'uid';

	/**
	* @internal
	*/
	protected static string $table = 'venus_users';

	/**
	* @internal
	*/
	protected static string $class = '\Venus\User';
}
