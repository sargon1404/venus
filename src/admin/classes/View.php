<?php
/**
* The View Class
* @package Venus
*/

namespace Venus\Admin;

use Venus\Controller;

/**
	* Builds the View
	* @param Controller $controller The controller the view belongs to
	*/
abstract class View extends \Venus\View
{
	/**
	* @var object $navbar Alias for $this->app->navbar
	*/
	public $navbar = null;

	/**
	* @see \Mars\View::prepare()
	* {@inheritDoc}
	*/
	protected function prepare(Controller $controller)
	{
		parent::prepare($controller);

		$this->navbar = $this->app->navbar;
	}

	/**
	* Builds a an actions list from $links
	* @see Venus\Admin\Actions::getList()
	*/
	public function getActionsList(string $item_id, array $links)
	{
		return $this->app->actions->getList($item_id, $links);
	}

	/**
	* Builds a select control listing the options
	* @see Venus\Admin\Actions::getSelect()
	*/
	public function getActionsSelect(string $item_id, array $options, string $url = '')
	{
		return $this->app->actions->getSelect($item_id, $this->item_id, $this->item_ids, $options, $url);
	}
}
