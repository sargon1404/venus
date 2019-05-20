<?php
/**
* The Entities Class
* @package Venus
*/
namespace Venus;

/**
* The Records Class
* Container of multiple records
*/
abstract class Entities extends \Mars\Entities
{
	/**
	* @var string $class The class of the loaded items
	*/
	protected static $class = '\Venus\Entity';
}
