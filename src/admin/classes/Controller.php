<?php
/**
* The Controller Class
* @package Venus
*/

namespace Venus\Admin;

use Venus\Document;

/**
* The Controller Class
* Implements the Controller functionality of the MVC pattern
*/
abstract class Controller extends \Venus\Controller
{
	/**
	* @var object $controls The controls object. Alias for $this->app->controls
	*/
	public $controls = null;

	/**
	* @see \Venus\Controller::prepare();
	* {@inheritDoc}
	*/
	protected function prepare(Document $document = null)
	{
		parent::prepare($document);

		$this->controls = $this->app->controls;
	}
}
