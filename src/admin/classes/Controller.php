<?php
/**
* The Controller Class
* @package Venus
*/

namespace Venus\Admin;

use Venus\Document;
use Venus\Helpers\Controls;

/**
* The Controller Class
* Implements the Controller functionality of the MVC pattern
*/
abstract class Controller extends \Venus\Controller
{
	/**
	* @var Controls $controls The controls object. Alias for $this->app->controls
	*/
	public Controls $controls;

	/**
	* @see \Venus\Controller::prepare();
	* {@inheritdoc}
	*/
	protected function prepare(Document $document = null)
	{
		parent::prepare($document);

		$this->controls = $this->app->controls;
	}
}
