<?php
/**
* The Plugin Menu Url
* @package Venus
*/

namespace Venus\Admin\Menu;

/**
* The Plugin Menu Url
*/
class Widget extends \Venus\Menu\Url
{
	/**
	* @var string $block_name The name of the widgets block
	*/
	protected string $block_name = 'widgets';

	/**
	* {@inheritdoc}
	*/
	public function getUrl(object $item) : string
	{
		return $this->app->uri->getAdminBlock($this->block_name, '', ['id' => $item->type_id]);
	}
}
