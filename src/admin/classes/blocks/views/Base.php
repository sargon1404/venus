<?php
/**
* The Base view for admin blocks
* @package Venus
*/

namespace Venus\Admin\Blocks\Views;

/**
* The Base view for admin blocks
*/
abstract class Base extends \Venus\Admin\View
{
	/**
	* @var string $lang_prefix Prefix to be used when using language strings
	*/
	public string $lang_prefix = '';

	/**
	* Prepares the items for listing
	*/
	public function list()
	{
		global $venus;
		$this->prepare_items();

		$venus->plugins->run($this->prefix . 'list', $this);
	}

	/**
	* Prepares the display of the quick action options an item has
	* @param object $item The item
	*/
	//object
	public function item_edit_quick_actions($item)
	{
		$item->id = $item->get_id();

		return $this->get_quick_actions($item->id, $this->get_item_quick_actions_edit($item));
	}
}
