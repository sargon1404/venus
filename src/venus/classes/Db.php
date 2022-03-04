<?php
/**
* The Database Class
* @package Venus
*/

namespace Venus;

/**
* The Database Class
* Handles the database interactions
*/
class Db extends \Mars\Db
{
	/**
	* @see \Mars\Db:$class_name
	* {@inheritdoc}
	*/
	protected string $class_name = '\Venus\Row';
}
