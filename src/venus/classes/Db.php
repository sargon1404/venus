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
	* @see \Mars\Db:getRowClassName()
	* {@inheritDoc}
	*/
	protected function getRowClassName() : string
	{
		return '\Venus\Row';
	}
}
