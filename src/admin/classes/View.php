<?php
/**
* The View Class
* @package Venus
*/

namespace Venus\Admin;

use Mars\Controller;

/**
	* Builds the View
	* @param Controller $controller The controller the view belongs to
	*/
abstract class View extends \Venus\View
{
	/**
	* @var Navbar $navbar Alias for $this->app->navbar
	*/
	public Navbar $navbar;

	/**
	* @var Actions $actions Alias for $this->app->actions
	*/
	public Actions $actions;

	/**
	* @see \Mars\View::prepare()
	* {@inheritDoc}
	*/
	protected function prepare(Controller $controller)
	{
		parent::prepare($controller);

		$this->navbar = $this->app->navbar;
		$this->actions = $this->app->actions;
	}

	/**
	* Builds a select control listing the options
	* @see Venus\Admin\Actions::getSelect()
	*/
	public function getActionsSelect(string $item_id, array $options, string $url = '')
	{
		return $this->actions->getSelect($item_id, $this->item_id, $this->item_ids, $options, $url);
	}
}
